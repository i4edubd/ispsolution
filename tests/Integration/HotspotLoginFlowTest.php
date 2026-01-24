<?php

namespace Tests\Integration;

use App\Models\HotspotUser;
use App\Models\Otp;
use App\Models\Package;
use App\Models\Tenant;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class HotspotLoginFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Package $package;
    protected HotspotUser $hotspotUser;
    protected OtpService $otpService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test tenant and package
        $this->tenant = Tenant::factory()->create();
        $this->package = Package::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Package',
            'price' => 100,
            'validity_days' => 30,
        ]);

        // Create active hotspot user
        $this->hotspotUser = HotspotUser::factory()->create([
            'tenant_id' => $this->tenant->id,
            'phone_number' => '01712345678',
            'username' => 'HS12345678',
            'package_id' => $this->package->id,
            'status' => 'active',
            'is_verified' => true,
            'verified_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $this->otpService = app(OtpService::class);

        // Fake HTTP for SMS
        Http::fake();
        Config::set('sms.enabled', false);
        Config::set('app.debug', true);
    }

    public function test_user_can_view_login_form()
    {
        $response = $this->get(route('hotspot.login'));

        $response->assertStatus(200);
        $response->assertSee('Hotspot Login');
        $response->assertSee('Send Login OTP');
    }

    public function test_user_can_request_login_otp()
    {
        $response = $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        $response->assertRedirect(route('hotspot.login.verify-otp'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('hotspot_login.mobile_number', $this->hotspotUser->phone_number);
    }

    public function test_cannot_login_with_unregistered_mobile_number()
    {
        $response = $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => '09999999999',
        ]);

        $response->assertSessionHasErrors('mobile_number');
    }

    public function test_cannot_login_with_inactive_account()
    {
        $this->hotspotUser->update(['status' => 'suspended']);

        $response = $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        $response->assertSessionHasErrors('mobile_number');
    }

    public function test_cannot_login_with_expired_account()
    {
        $this->hotspotUser->update(['expires_at' => now()->subDay()]);

        $response = $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        $response->assertSessionHasErrors('mobile_number');
    }

    public function test_user_can_verify_otp_and_login()
    {
        // First request OTP
        $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        // Get OTP from database (in debug mode)
        $otpRecord = Otp::where('mobile_number', $this->hotspotUser->phone_number)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        // For testing, we'll use a fixed OTP
        $testOtp = '123456';
        if ($otpRecord) {
            $otpRecord->update(['otp' => \Illuminate\Support\Facades\Hash::make($testOtp)]);
        }

        // Verify OTP
        $response = $this->post(route('hotspot.login.verify-otp.post'), [
            'mobile_number' => $this->hotspotUser->phone_number,
            'otp_code' => $testOtp,
        ]);

        $response->assertRedirect(route('hotspot.dashboard'));
        $response->assertSessionHas('hotspot_auth');
    }

    public function test_user_can_view_dashboard_after_login()
    {
        // Login user manually
        Session::put('hotspot_auth', [
            'user_id' => $this->hotspotUser->id,
            'session_id' => 'test-session-id',
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'logged_in_at' => now()->timestamp,
        ]);

        $this->hotspotUser->update([
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'active_session_id' => 'test-session-id',
            'last_login_at' => now(),
        ]);

        $response = $this->get(route('hotspot.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Welcome');
        $response->assertSee($this->hotspotUser->username);
        $response->assertSee($this->package->name);
    }

    public function test_device_conflict_is_detected()
    {
        // Set existing session with different MAC
        $this->hotspotUser->update([
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'active_session_id' => 'existing-session',
            'last_login_at' => now(),
        ]);

        // Request OTP
        $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        // Get and update OTP
        $testOtp = '123456';
        $otpRecord = Otp::where('mobile_number', $this->hotspotUser->phone_number)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['otp' => \Illuminate\Support\Facades\Hash::make($testOtp)]);
        }

        // Try to verify with different device (different MAC will be generated)
        $response = $this->post(route('hotspot.login.verify-otp.post'), [
            'mobile_number' => $this->hotspotUser->phone_number,
            'otp_code' => $testOtp,
        ]);

        // Should redirect to device conflict page
        $response->assertRedirect(route('hotspot.login.device-conflict'));
        $response->assertSessionHas('hotspot_login.device_conflict', true);
    }

    public function test_user_can_force_login_on_new_device()
    {
        // Setup existing session
        $this->hotspotUser->update([
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'active_session_id' => 'old-session',
            'last_login_at' => now(),
        ]);

        // Set session with conflict
        Session::put('hotspot_login', [
            'mobile_number' => $this->hotspotUser->phone_number,
            'user_id' => $this->hotspotUser->id,
            'device_conflict' => true,
            'new_mac_address' => '11:22:33:44:55:66',
        ]);

        // Force login
        $response = $this->post(route('hotspot.login.force-login'));

        $response->assertRedirect(route('hotspot.dashboard'));
        $response->assertSessionHas('success');

        // Verify old session is cleared and new one is set
        $this->hotspotUser->refresh();
        $this->assertNotEquals('old-session', $this->hotspotUser->active_session_id);
    }

    public function test_user_can_logout()
    {
        // Login user
        Session::put('hotspot_auth', [
            'user_id' => $this->hotspotUser->id,
            'session_id' => 'test-session-id',
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
        ]);

        $this->hotspotUser->update([
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'active_session_id' => 'test-session-id',
        ]);

        // Logout
        $response = $this->post(route('hotspot.logout'));

        $response->assertRedirect(route('hotspot.login'));
        $response->assertSessionHas('success');
        $response->assertSessionMissing('hotspot_auth');

        // Verify session is cleared in database
        $this->hotspotUser->refresh();
        $this->assertNull($this->hotspotUser->active_session_id);
    }

    public function test_invalid_otp_shows_error()
    {
        // Request OTP
        $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        // Try to verify with wrong OTP
        $response = $this->post(route('hotspot.login.verify-otp.post'), [
            'mobile_number' => $this->hotspotUser->phone_number,
            'otp_code' => '000000',
        ]);

        $response->assertSessionHasErrors('otp_code');
    }

    public function test_mac_address_is_tracked_on_login()
    {
        // Request and verify OTP
        $this->post(route('hotspot.login.request-otp'), [
            'mobile_number' => $this->hotspotUser->phone_number,
        ]);

        $testOtp = '123456';
        $otpRecord = Otp::where('mobile_number', $this->hotspotUser->phone_number)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($otpRecord) {
            $otpRecord->update(['otp' => \Illuminate\Support\Facades\Hash::make($testOtp)]);
        }

        $this->post(route('hotspot.login.verify-otp.post'), [
            'mobile_number' => $this->hotspotUser->phone_number,
            'otp_code' => $testOtp,
        ]);

        // Verify MAC address is set
        $this->hotspotUser->refresh();
        $this->assertNotNull($this->hotspotUser->mac_address);
        $this->assertNotNull($this->hotspotUser->active_session_id);
        $this->assertNotNull($this->hotspotUser->last_login_at);
    }
}

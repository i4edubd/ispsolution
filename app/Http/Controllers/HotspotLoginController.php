<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\HotspotLogin\RequestLoginOtpRequest;
use App\Http\Requests\HotspotLogin\VerifyLoginOtpRequest;
use App\Models\HotspotUser;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class HotspotLoginController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('hotspot-login.login-form');
    }

    /**
     * Request OTP for login
     */
    public function requestLoginOtp(RequestLoginOtpRequest $request)
    {
        try {
            $mobileNumber = $request->validated()['mobile_number'];
            $ipAddress = $request->ip();

            // Check if user exists and is active
            $hotspotUser = HotspotUser::where('phone_number', $mobileNumber)
                ->where('is_verified', true)
                ->first();

            if (!$hotspotUser) {
                return back()->withErrors([
                    'mobile_number' => 'This mobile number is not registered. Please sign up first.',
                ])->withInput();
            }

            if ($hotspotUser->status !== 'active') {
                $message = 'Your account is currently ' . $hotspotUser->status . '. Please contact support.';
                return back()->withErrors([
                    'mobile_number' => $message,
                ])->withInput();
            }

            // Check if account is expired
            if ($hotspotUser->expires_at && $hotspotUser->expires_at->isPast()) {
                return back()->withErrors([
                    'mobile_number' => 'Your account has expired. Please renew your subscription.',
                ])->withInput();
            }

            // Get tenant ID
            $tenantId = $hotspotUser->tenant_id;

            // Generate and store OTP
            $otpData = $this->otpService->storeOtp($mobileNumber, $ipAddress, $tenantId);

            // Store login data in session
            session([
                'hotspot_login' => [
                    'mobile_number' => $mobileNumber,
                    'user_id' => $hotspotUser->id,
                    'otp_expires_at' => $otpData['expires_at']->timestamp,
                ],
            ]);

            return redirect()
                ->route('hotspot.login.verify-otp')
                ->with('success', 'OTP sent to your mobile number. Please check your SMS.');

        } catch (\Exception $e) {
            Log::error('Login OTP request failed', [
                'mobile_number' => $request->mobile_number,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show OTP verification form for login
     */
    public function showVerifyLoginOtp()
    {
        $loginData = session('hotspot_login');

        if (!$loginData) {
            return redirect()
                ->route('hotspot.login')
                ->withErrors(['error' => 'Session expired. Please start again.']);
        }

        return view('hotspot-login.verify-otp', [
            'mobile_number' => $loginData['mobile_number'],
            'expires_at' => $loginData['otp_expires_at'],
        ]);
    }

    /**
     * Verify OTP and login user
     */
    public function verifyLoginOtp(VerifyLoginOtpRequest $request)
    {
        try {
            $loginData = session('hotspot_login');

            if (!$loginData) {
                return redirect()
                    ->route('hotspot.login')
                    ->withErrors(['error' => 'Session expired. Please start again.']);
            }

            $mobileNumber = $request->validated()['mobile_number'];
            $otpCode = $request->validated()['otp_code'];
            $ipAddress = $request->ip();

            // Verify mobile number matches session
            if ($mobileNumber !== $loginData['mobile_number']) {
                return back()->withErrors([
                    'mobile_number' => 'Mobile number does not match.',
                ])->withInput();
            }

            // Verify OTP
            $this->otpService->verifyOtp($mobileNumber, $otpCode, $ipAddress);

            // Get hotspot user
            $hotspotUser = HotspotUser::findOrFail($loginData['user_id']);

            // Get MAC address from request
            $macAddress = $this->getMacAddress($request);

            // Check if user has active session on different device
            if ($hotspotUser->hasActiveSessionOnDifferentDevice($macAddress)) {
                // Store conflict info in session
                session(['hotspot_login.device_conflict' => true]);
                session(['hotspot_login.new_mac_address' => $macAddress]);

                return redirect()
                    ->route('hotspot.login.device-conflict')
                    ->with('warning', 'You are already logged in on another device.');
            }

            // Create new session and login
            $this->loginUser($hotspotUser, $macAddress, $request);

            return redirect()
                ->route('hotspot.dashboard')
                ->with('success', 'Login successful! Welcome back.');

        } catch (\Exception $e) {
            Log::error('Login OTP verification failed', [
                'mobile_number' => $request->mobile_number ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['otp_code' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show device conflict page
     */
    public function showDeviceConflict()
    {
        $loginData = session('hotspot_login');

        if (!$loginData || !isset($loginData['device_conflict'])) {
            return redirect()->route('hotspot.login');
        }

        $hotspotUser = HotspotUser::findOrFail($loginData['user_id']);

        return view('hotspot-login.device-conflict', [
            'user' => $hotspotUser,
            'current_mac' => $hotspotUser->mac_address,
            'new_mac' => $loginData['new_mac_address'] ?? 'unknown',
        ]);
    }

    /**
     * Force login by logging out from other device
     */
    public function forceLogin(Request $request)
    {
        $loginData = session('hotspot_login');

        if (!$loginData || !isset($loginData['device_conflict'])) {
            return redirect()->route('hotspot.login');
        }

        try {
            $hotspotUser = HotspotUser::findOrFail($loginData['user_id']);
            $newMacAddress = $loginData['new_mac_address'];

            // Clear old session and login with new device
            $this->loginUser($hotspotUser, $newMacAddress, $request);

            // Clear conflict flag
            session()->forget('hotspot_login');

            return redirect()
                ->route('hotspot.dashboard')
                ->with('success', 'Login successful! Your previous device has been logged out.');

        } catch (\Exception $e) {
            Log::error('Force login failed', [
                'user_id' => $loginData['user_id'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Failed to login. Please try again.']);
        }
    }

    /**
     * Show dashboard for logged in users
     */
    public function showDashboard(Request $request)
    {
        $hotspotUser = $this->getAuthenticatedUser($request);

        if (!$hotspotUser) {
            return redirect()
                ->route('hotspot.login')
                ->withErrors(['error' => 'Please login to access the dashboard.']);
        }

        return view('hotspot-login.dashboard', [
            'user' => $hotspotUser->load('package'),
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $hotspotUser = $this->getAuthenticatedUser($request);

        if ($hotspotUser) {
            // Clear session in database
            $hotspotUser->clearSession();

            Log::info('Hotspot user logged out', [
                'user_id' => $hotspotUser->id,
                'mac_address' => $hotspotUser->mac_address,
            ]);
        }

        // Clear session data
        session()->forget('hotspot_auth');
        session()->forget('hotspot_login');

        return redirect()
            ->route('hotspot.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Login user and create session
     */
    protected function loginUser(HotspotUser $hotspotUser, string $macAddress, Request $request): void
    {
        // Generate unique session ID
        $sessionId = Str::uuid()->toString();

        // Update user's login session
        $hotspotUser->updateLoginSession($macAddress, $sessionId);

        // Store auth data in session
        session([
            'hotspot_auth' => [
                'user_id' => $hotspotUser->id,
                'session_id' => $sessionId,
                'mac_address' => $macAddress,
                'logged_in_at' => now()->timestamp,
            ],
        ]);

        Log::info('Hotspot user logged in', [
            'user_id' => $hotspotUser->id,
            'mac_address' => $macAddress,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Get authenticated user from session
     */
    protected function getAuthenticatedUser(Request $request): ?HotspotUser
    {
        $authData = session('hotspot_auth');

        if (!$authData) {
            return null;
        }

        $hotspotUser = HotspotUser::find($authData['user_id']);

        if (!$hotspotUser) {
            return null;
        }

        // Verify session ID matches
        if ($hotspotUser->active_session_id !== $authData['session_id']) {
            // Session has been invalidated
            session()->forget('hotspot_auth');
            return null;
        }

        // Verify MAC address matches
        $currentMac = $this->getMacAddress($request);
        if ($hotspotUser->mac_address !== $currentMac) {
            // Different device
            session()->forget('hotspot_auth');
            return null;
        }

        return $hotspotUser;
    }

    /**
     * Get MAC address from request
     * In real hotspot scenarios, this would come from RADIUS or router
     * For web-based login, we use a combination of factors as identifier
     */
    protected function getMacAddress(Request $request): string
    {
        // In a real hotspot system, MAC address would be provided by the router
        // For this implementation, we'll use a fingerprint based on:
        // - IP address
        // - User agent
        // This creates a unique identifier for each device

        $fingerprint = $request->ip() . '|' . $request->userAgent();
        
        // Create a hash that looks like a MAC address format
        $hash = md5($fingerprint);
        $mac = substr($hash, 0, 2) . ':' .
               substr($hash, 2, 2) . ':' .
               substr($hash, 4, 2) . ':' .
               substr($hash, 6, 2) . ':' .
               substr($hash, 8, 2) . ':' .
               substr($hash, 10, 2);

        return strtoupper($mac);
    }
}

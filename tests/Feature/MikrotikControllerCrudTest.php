<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\MikrotikIpPool;
use App\Models\MikrotikProfile;
use App\Models\MikrotikQueue;
use App\Models\MikrotikRouter;
use App\Models\MikrotikVpnAccount;
use App\Models\Package;
use App\Models\PackageProfileMapping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MikrotikControllerCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private MikrotikRouter $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->router = MikrotikRouter::create([
            'name' => 'Test Router',
            'ip_address' => 'localhost',
            'api_port' => 8728,
            'username' => 'admin',
            'password' => 'password',
            'status' => 'active',
        ]);
    }

    // VPN Account Tests
    public function test_can_view_single_vpn_account(): void
    {
        $vpnAccount = MikrotikVpnAccount::create([
            'router_id' => $this->router->id,
            'username' => 'vpnuser1',
            'password' => 'password123',
            'profile' => 'default',
            'enabled' => true,
        ]);

        $response = $this->getJson("/api/v1/mikrotik/vpn-accounts/{$vpnAccount->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'username' => 'vpnuser1',
                'profile' => 'default',
            ]);
    }

    public function test_can_update_vpn_account(): void
    {
        $vpnAccount = MikrotikVpnAccount::create([
            'router_id' => $this->router->id,
            'username' => 'vpnuser1',
            'password' => 'password123',
            'profile' => 'default',
            'enabled' => true,
        ]);

        $response = $this->putJson("/api/v1/mikrotik/vpn-accounts/{$vpnAccount->id}", [
            'profile' => 'new-profile',
            'enabled' => false,
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'VPN account updated successfully',
            ]);

        $this->assertDatabaseHas('mikrotik_vpn_accounts', [
            'id' => $vpnAccount->id,
            'profile' => 'new-profile',
            'enabled' => false,
        ]);
    }

    public function test_can_delete_vpn_account(): void
    {
        $vpnAccount = MikrotikVpnAccount::create([
            'router_id' => $this->router->id,
            'username' => 'vpnuser1',
            'password' => 'password123',
            'profile' => 'default',
            'enabled' => true,
        ]);

        $response = $this->deleteJson("/api/v1/mikrotik/vpn-accounts/{$vpnAccount->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'VPN account deleted successfully',
            ]);

        $this->assertDatabaseMissing('mikrotik_vpn_accounts', [
            'id' => $vpnAccount->id,
        ]);
    }

    // Queue Tests
    public function test_can_view_single_queue(): void
    {
        $queue = MikrotikQueue::create([
            'router_id' => $this->router->id,
            'name' => 'queue-test',
            'target' => '192.168.1.10/32',
            'max_limit' => '10M/10M',
            'priority' => 5,
        ]);

        $response = $this->getJson("/api/v1/mikrotik/queues/{$queue->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'queue-test',
                'target' => '192.168.1.10/32',
            ]);
    }

    public function test_can_update_queue(): void
    {
        $queue = MikrotikQueue::create([
            'router_id' => $this->router->id,
            'name' => 'queue-test',
            'target' => '192.168.1.10/32',
            'max_limit' => '10M/10M',
            'priority' => 5,
        ]);

        $response = $this->putJson("/api/v1/mikrotik/queues/{$queue->id}", [
            'max_limit' => '20M/20M',
            'priority' => 3,
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Queue updated successfully',
            ]);

        $this->assertDatabaseHas('mikrotik_queues', [
            'id' => $queue->id,
            'max_limit' => '20M/20M',
            'priority' => 3,
        ]);
    }

    public function test_can_delete_queue(): void
    {
        $queue = MikrotikQueue::create([
            'router_id' => $this->router->id,
            'name' => 'queue-test',
            'target' => '192.168.1.10/32',
            'max_limit' => '10M/10M',
            'priority' => 5,
        ]);

        $response = $this->deleteJson("/api/v1/mikrotik/queues/{$queue->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Queue deleted successfully',
            ]);

        $this->assertDatabaseMissing('mikrotik_queues', [
            'id' => $queue->id,
        ]);
    }

    // Profile Tests
    public function test_can_view_single_profile(): void
    {
        $profile = MikrotikProfile::create([
            'router_id' => $this->router->id,
            'name' => 'profile-10mbps',
            'local_address' => '192.168.1.1',
            'remote_address' => '192.168.1.0/24',
            'rate_limit' => '10M/10M',
        ]);

        $response = $this->getJson("/api/v1/mikrotik/profiles/{$profile->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'profile-10mbps',
                'rate_limit' => '10M/10M',
            ]);
    }

    public function test_can_update_profile(): void
    {
        $profile = MikrotikProfile::create([
            'router_id' => $this->router->id,
            'name' => 'profile-10mbps',
            'local_address' => '192.168.1.1',
            'remote_address' => '192.168.1.0/24',
            'rate_limit' => '10M/10M',
        ]);

        $response = $this->putJson("/api/v1/mikrotik/profiles/{$profile->id}", [
            'rate_limit' => '20M/20M',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('mikrotik_profiles', [
            'id' => $profile->id,
            'rate_limit' => '20M/20M',
        ]);
    }

    public function test_can_delete_profile(): void
    {
        $profile = MikrotikProfile::create([
            'router_id' => $this->router->id,
            'name' => 'profile-10mbps',
            'local_address' => '192.168.1.1',
            'remote_address' => '192.168.1.0/24',
            'rate_limit' => '10M/10M',
        ]);

        $response = $this->deleteJson("/api/v1/mikrotik/profiles/{$profile->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Profile deleted successfully',
            ]);

        $this->assertDatabaseMissing('mikrotik_profiles', [
            'id' => $profile->id,
        ]);
    }

    // IP Pool Tests
    public function test_can_view_single_ip_pool(): void
    {
        $pool = MikrotikIpPool::create([
            'router_id' => $this->router->id,
            'name' => 'pool-clients',
            'ranges' => ['192.168.1.10-192.168.1.100'],
        ]);

        $response = $this->getJson("/api/v1/mikrotik/ip-pools/{$pool->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'pool-clients',
            ]);
    }

    public function test_can_update_ip_pool(): void
    {
        $pool = MikrotikIpPool::create([
            'router_id' => $this->router->id,
            'name' => 'pool-clients',
            'ranges' => ['192.168.1.10-192.168.1.100'],
        ]);

        $response = $this->putJson("/api/v1/mikrotik/ip-pools/{$pool->id}", [
            'ranges' => ['192.168.1.10-192.168.1.200'],
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'IP pool updated successfully',
            ]);

        $this->assertDatabaseHas('mikrotik_ip_pools', [
            'id' => $pool->id,
            'name' => 'pool-clients',
        ]);
    }

    public function test_can_delete_ip_pool(): void
    {
        $pool = MikrotikIpPool::create([
            'router_id' => $this->router->id,
            'name' => 'pool-clients',
            'ranges' => ['192.168.1.10-192.168.1.100'],
        ]);

        $response = $this->deleteJson("/api/v1/mikrotik/ip-pools/{$pool->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'IP pool deleted successfully',
            ]);

        $this->assertDatabaseMissing('mikrotik_ip_pools', [
            'id' => $pool->id,
        ]);
    }

    // Package Mapping Tests
    public function test_can_view_single_package_mapping(): void
    {
        $package = Package::factory()->create();

        $mapping = PackageProfileMapping::create([
            'package_id' => $package->id,
            'router_id' => $this->router->id,
            'profile_name' => 'profile-10mbps',
        ]);

        $response = $this->getJson("/api/v1/mikrotik/package-mappings/{$mapping->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'profile_name' => 'profile-10mbps',
            ]);
    }

    public function test_can_update_package_mapping(): void
    {
        $package = Package::factory()->create();

        $mapping = PackageProfileMapping::create([
            'package_id' => $package->id,
            'router_id' => $this->router->id,
            'profile_name' => 'profile-10mbps',
        ]);

        $response = $this->putJson("/api/v1/mikrotik/package-mappings/{$mapping->id}", [
            'profile_name' => 'profile-20mbps',
        ]);

        $response->assertOk()
            ->assertJson([
                'message' => 'Package mapping updated successfully',
            ]);

        $this->assertDatabaseHas('package_profile_mappings', [
            'id' => $mapping->id,
            'profile_name' => 'profile-20mbps',
        ]);
    }

    public function test_can_delete_package_mapping(): void
    {
        $package = Package::factory()->create();

        $mapping = PackageProfileMapping::create([
            'package_id' => $package->id,
            'router_id' => $this->router->id,
            'profile_name' => 'profile-10mbps',
        ]);

        $response = $this->deleteJson("/api/v1/mikrotik/package-mappings/{$mapping->id}");

        $response->assertOk()
            ->assertJson([
                'message' => 'Package mapping deleted successfully',
            ]);

        $this->assertDatabaseMissing('package_profile_mappings', [
            'id' => $mapping->id,
        ]);
    }

    // Error handling tests
    public function test_view_non_existent_vpn_account_returns_404(): void
    {
        $response = $this->getJson('/api/v1/mikrotik/vpn-accounts/9999');

        $response->assertNotFound();
    }

    public function test_update_non_existent_queue_returns_404(): void
    {
        $response = $this->putJson('/api/v1/mikrotik/queues/9999', [
            'max_limit' => '20M/20M',
        ]);

        $response->assertNotFound();
    }

    public function test_delete_non_existent_profile_returns_404(): void
    {
        $response = $this->deleteJson('/api/v1/mikrotik/profiles/9999');

        $response->assertNotFound();
    }
}

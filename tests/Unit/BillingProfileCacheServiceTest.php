<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\BillingProfileCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Test Billing Profile Cache Service
 * 
 * Reference: REFERENCE_SYSTEM_QUICK_GUIDE.md - Quick Win #4
 */
class BillingProfileCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private BillingProfileCacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BillingProfileCacheService();
        Cache::flush();
    }

    /**
     * Test getting billing profiles with caching
     */
    public function test_get_billing_profiles_with_caching(): void
    {
        $tenantId = 1;

        // First call should hit database
        $profiles = $this->service->getBillingProfiles($tenantId);
        
        // Second call should hit cache
        $cachedProfiles = $this->service->getBillingProfiles($tenantId);
        
        $this->assertIsObject($profiles);
        $this->assertEquals($profiles, $cachedProfiles);
    }

    /**
     * Test refreshing billing profiles cache
     */
    public function test_refresh_billing_profiles_cache(): void
    {
        $tenantId = 1;

        // Get profiles to populate cache
        $this->service->getBillingProfiles($tenantId);
        
        // Verify cache exists
        $this->assertTrue(Cache::has("billing_profiles:tenant:{$tenantId}"));
        
        // Refresh cache
        $profiles = $this->service->getBillingProfiles($tenantId, true);
        
        $this->assertIsObject($profiles);
    }

    /**
     * Test invalidating billing profile cache
     */
    public function test_invalidate_billing_profile_cache(): void
    {
        $profileId = 1;

        // Populate cache
        Cache::put("billing_profile:{$profileId}", ['test' => 'data'], 300);
        Cache::put("billing_profile_customer_count:{$profileId}", 10, 300);
        
        // Verify cache exists
        $this->assertTrue(Cache::has("billing_profile:{$profileId}"));
        $this->assertTrue(Cache::has("billing_profile_customer_count:{$profileId}"));
        
        // Invalidate cache
        $this->service->invalidateCache($profileId);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has("billing_profile:{$profileId}"));
        $this->assertFalse(Cache::has("billing_profile_customer_count:{$profileId}"));
    }

    /**
     * Test invalidating tenant billing profiles cache
     */
    public function test_invalidate_tenant_cache(): void
    {
        $tenantId = 1;

        // Populate cache
        Cache::put("billing_profiles:tenant:{$tenantId}", ['test' => 'data'], 300);
        
        // Verify cache exists
        $this->assertTrue(Cache::has("billing_profiles:tenant:{$tenantId}"));
        
        // Invalidate cache
        $this->service->invalidateTenantCache($tenantId);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has("billing_profiles:tenant:{$tenantId}"));
    }

    /**
     * Test getting customer count with caching
     */
    public function test_get_customer_count_with_caching(): void
    {
        $profileId = 1;

        // First call should hit database
        $count = $this->service->getCustomerCount($profileId);
        
        // Second call should hit cache
        $cachedCount = $this->service->getCustomerCount($profileId);
        
        $this->assertIsInt($count);
        $this->assertEquals($count, $cachedCount);
        $this->assertGreaterThanOrEqual(0, $count);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\OperatorStatsCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Test Operator Statistics Cache Service
 * 
 * Reference: REFERENCE_SYSTEM_QUICK_GUIDE.md - Quick Win #4
 */
class OperatorStatsCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private OperatorStatsCacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OperatorStatsCacheService();
        Cache::flush();
    }

    /**
     * Test getting dashboard stats with caching
     */
    public function test_get_dashboard_stats_with_caching(): void
    {
        $operatorId = 1;

        // First call should hit database
        $stats = $this->service->getDashboardStats($operatorId);
        
        // Second call should hit cache
        $cachedStats = $this->service->getDashboardStats($operatorId);
        
        $this->assertIsArray($stats);
        $this->assertEquals($stats, $cachedStats);
        $this->assertArrayHasKey('total_customers', $stats);
        $this->assertArrayHasKey('active_customers', $stats);
        $this->assertArrayHasKey('timestamp', $stats);
    }

    /**
     * Test refreshing dashboard stats cache
     */
    public function test_refresh_dashboard_stats_cache(): void
    {
        $operatorId = 1;

        // Get stats to populate cache
        $this->service->getDashboardStats($operatorId);
        
        // Verify cache exists
        $this->assertTrue(Cache::has("operator_stats:dashboard:{$operatorId}"));
        
        // Refresh cache
        $stats = $this->service->getDashboardStats($operatorId, true);
        
        $this->assertIsArray($stats);
    }

    /**
     * Test getting customer stats with caching
     */
    public function test_get_customer_stats_with_caching(): void
    {
        $operatorId = 1;

        // First call should hit database
        $stats = $this->service->getCustomerStats($operatorId);
        
        // Second call should hit cache
        $cachedStats = $this->service->getCustomerStats($operatorId);
        
        $this->assertIsArray($stats);
        $this->assertEquals($stats, $cachedStats);
    }

    /**
     * Test getting revenue stats with caching
     */
    public function test_get_revenue_stats_with_caching(): void
    {
        $operatorId = 1;

        // First call should hit database
        $stats = $this->service->getRevenueStats($operatorId);
        
        // Second call should hit cache
        $cachedStats = $this->service->getRevenueStats($operatorId);
        
        $this->assertIsArray($stats);
        $this->assertEquals($stats, $cachedStats);
    }

    /**
     * Test invalidating operator cache
     */
    public function test_invalidate_operator_cache(): void
    {
        $operatorId = 1;

        // Populate cache
        Cache::put("operator_stats:dashboard:{$operatorId}", ['test' => 'data'], 300);
        Cache::put("operator_stats:customers:{$operatorId}", ['test' => 'data'], 300);
        Cache::put("operator_stats:revenue:{$operatorId}", ['test' => 'data'], 60);
        
        // Verify cache exists
        $this->assertTrue(Cache::has("operator_stats:dashboard:{$operatorId}"));
        $this->assertTrue(Cache::has("operator_stats:customers:{$operatorId}"));
        $this->assertTrue(Cache::has("operator_stats:revenue:{$operatorId}"));
        
        // Invalidate cache
        $this->service->invalidateCache($operatorId);
        
        // Verify cache is cleared
        $this->assertFalse(Cache::has("operator_stats:dashboard:{$operatorId}"));
        $this->assertFalse(Cache::has("operator_stats:customers:{$operatorId}"));
        $this->assertFalse(Cache::has("operator_stats:revenue:{$operatorId}"));
    }

    /**
     * Test that stats contain expected structure
     */
    public function test_dashboard_stats_structure(): void
    {
        $operatorId = 1;

        $stats = $this->service->getDashboardStats($operatorId);
        
        $this->assertArrayHasKey('total_customers', $stats);
        $this->assertArrayHasKey('active_customers', $stats);
        $this->assertArrayHasKey('suspended_customers', $stats);
        $this->assertArrayHasKey('expired_customers', $stats);
        $this->assertArrayHasKey('timestamp', $stats);
        
        $this->assertIsInt($stats['total_customers']);
        $this->assertIsInt($stats['active_customers']);
        $this->assertIsInt($stats['suspended_customers']);
        $this->assertIsInt($stats['expired_customers']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\NetworkUser;
use App\Models\Payment;
use App\Models\ServicePackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a tenant
        $this->tenant = Tenant::factory()->create([
            'name' => 'Test ISP',
            'subdomain' => 'test-isp',
        ]);

        // Create admin role
        $adminRole = Role::factory()->create([
            'name' => 'Admin',
            'slug' => 'admin',
            'level' => 20,
        ]);

        // Create an admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
            'tenant_id' => $this->tenant->id,
        ]);

        $this->adminUser->roles()->attach($adminRole);
    }

    public function test_analytics_dashboard_is_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('panels.admin.analytics.dashboard');
        $response->assertViewHas('analytics');
        $response->assertViewHas('startDate');
        $response->assertViewHas('endDate');
    }

    public function test_analytics_dashboard_displays_with_data(): void
    {
        // Create test data
        $package = ServicePackage::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test Package',
            'price' => 1000.00,
        ]);

        NetworkUser::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'package_id' => $package->id,
            'is_active' => true,
        ]);

        Payment::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'amount' => 1000.00,
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.dashboard'));

        $response->assertStatus(200);
        
        // Check that analytics data is present
        $analytics = $response->viewData('analytics');
        $this->assertArrayHasKey('revenue_analytics', $analytics);
        $this->assertArrayHasKey('customer_analytics', $analytics);
        $this->assertArrayHasKey('service_analytics', $analytics);
    }

    public function test_revenue_report_is_accessible(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.revenue-report'));

        $response->assertStatus(200);
        $response->assertViewIs('panels.admin.analytics.revenue-report');
        $response->assertViewHas('analytics');
    }

    public function test_customer_report_is_accessible(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.customer-report'));

        $response->assertStatus(200);
        $response->assertViewIs('panels.admin.analytics.customer-report');
        $response->assertViewHas('analytics');
    }

    public function test_service_report_is_accessible(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.service-report'));

        $response->assertStatus(200);
        $response->assertViewIs('panels.admin.analytics.service-report');
        $response->assertViewHas('analytics');
    }

    public function test_analytics_export_returns_csv(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_revenue_api_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.api.revenue'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_revenue',
            'daily_revenue',
            'revenue_by_method',
            'average_daily_revenue',
            'growth_rate',
        ]);
    }

    public function test_customer_api_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.api.customers'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_customers',
            'active_customers',
            'new_customers',
            'churn_rate',
            'average_revenue_per_user',
        ]);
    }

    public function test_service_api_endpoint_returns_json(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.api.services'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'package_distribution',
            'total_packages',
        ]);
    }

    public function test_analytics_dashboard_filters_by_date_range(): void
    {
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');

        $response = $this->actingAs($this->adminUser)
            ->get(route('panel.admin.analytics.dashboard', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]));

        $response->assertStatus(200);
        $this->assertEquals($startDate, $response->viewData('startDate')->format('Y-m-d'));
        $this->assertEquals($endDate, $response->viewData('endDate')->format('Y-m-d'));
    }

    public function test_non_admin_cannot_access_analytics(): void
    {
        // Create a customer role
        $customerRole = Role::factory()->create([
            'name' => 'Customer',
            'slug' => 'customer',
            'level' => 100,
        ]);

        $customer = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $customer->roles()->attach($customerRole);

        $response = $this->actingAs($customer)
            ->get(route('panel.admin.analytics.dashboard'));

        $response->assertStatus(403);
    }
}

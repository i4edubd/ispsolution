<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperCustomersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    public function test_developer_can_view_all_customers_page(): void
    {
        // Create a developer user with the developer role
        $developer = User::factory()->create(['operator_level' => 0]);
        $developerRole = \App\Models\Role::where('slug', 'developer')->first();
        $developer->roles()->attach($developerRole);

        // Create some test customers with different is_active values
        $tenant = Tenant::factory()->create();
        User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => true,
        ]);
        User::factory()->count(2)->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => false,
        ]);

        // Act as developer and visit the all customers page
        $response = $this->actingAs($developer)
            ->get('/panel/developer/customers');

        // Assert the response is successful
        $response->assertStatus(200);
    }

    public function test_all_customers_query_uses_is_active_column(): void
    {
        // Create a developer user with the developer role
        $developer = User::factory()->create(['operator_level' => 0]);
        $developerRole = \App\Models\Role::where('slug', 'developer')->first();
        $developer->roles()->attach($developerRole);

        // Create test customers
        $tenant = Tenant::factory()->create();
        User::factory()->count(5)->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => true,
        ]);
        User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => false,
        ]);

        // Act as developer and get the page
        $response = $this->actingAs($developer)
            ->get('/panel/developer/customers');

        // Assert the response is successful (no SQL errors)
        $response->assertStatus(200);

        // Assert the view has the correct stats
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 9 && $stats['active'] === 6;
        });
    }

    public function test_search_customers_query_uses_is_active_column(): void
    {
        // Create a developer user with the developer role
        $developer = User::factory()->create(['operator_level' => 0]);
        $developerRole = \App\Models\Role::where('slug', 'developer')->first();
        $developer->roles()->attach($developerRole);

        // Create test customers
        $tenant = Tenant::factory()->create();
        User::factory()->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => true,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        User::factory()->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => false,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        // Act as developer and search for customers
        $response = $this->actingAs($developer)
            ->get('/panel/developer/customers/search?query=john');

        // Assert the response is successful (no SQL errors)
        $response->assertStatus(200);

        // Assert the view has stats
        $response->assertViewHas('stats');
    }

    public function test_search_customers_without_query_returns_all_customers(): void
    {
        // Create a developer user with the developer role
        $developer = User::factory()->create(['operator_level' => 0]);
        $developerRole = \App\Models\Role::where('slug', 'developer')->first();
        $developer->roles()->attach($developerRole);

        // Create test customers
        $tenant = Tenant::factory()->create();
        User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
            'operator_level' => 100,
            'is_active' => true,
        ]);

        // Act as developer and search without query
        $response = $this->actingAs($developer)
            ->get('/panel/developer/customers/search');

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert the view has stats with correct total
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 4; // 3 customers + 1 developer
        });
    }
}

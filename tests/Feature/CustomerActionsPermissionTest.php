<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerActionsPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected User $operator;
    protected User $subOperator;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tenant
        $this->tenant = Tenant::factory()->create();

        // Create admin user (level 20)
        $this->admin = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'operator_level' => 20,
            'name' => 'Admin User',
        ]);
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $this->admin->roles()->attach($adminRole);
        }

        // Create operator user (level 30)
        $this->operator = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'operator_level' => 30,
            'name' => 'Operator User',
            'created_by' => $this->admin->id,
        ]);
        $operatorRole = Role::where('slug', 'operator')->first();
        if ($operatorRole) {
            $this->operator->roles()->attach($operatorRole);
        }

        // Create sub-operator user (level 40)
        $this->subOperator = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'operator_level' => 40,
            'name' => 'Sub-Operator User',
            'created_by' => $this->operator->id,
        ]);
        $subOperatorRole = Role::where('slug', 'sub_operator')->first();
        if ($subOperatorRole) {
            $this->subOperator->roles()->attach($subOperatorRole);
        }

        // Create customer user (level 100)
        $this->customer = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'operator_level' => 100,
            'name' => 'Test Customer',
            'created_by' => $this->operator->id,
        ]);
    }

    /** @test */
    public function admin_has_full_access_to_all_customer_actions()
    {
        // Admin should have access to ALL actions
        $this->assertTrue($this->admin->can('view', $this->customer));
        $this->assertTrue($this->admin->can('update', $this->customer));
        $this->assertTrue($this->admin->can('delete', $this->customer));
        $this->assertTrue($this->admin->can('activate', $this->customer));
        $this->assertTrue($this->admin->can('suspend', $this->customer));
        $this->assertTrue($this->admin->can('disconnect', $this->customer));
        $this->assertTrue($this->admin->can('changePackage', $this->customer));
        $this->assertTrue($this->admin->can('editSpeedLimit', $this->customer));
        $this->assertTrue($this->admin->can('activateFup', $this->customer));
        $this->assertTrue($this->admin->can('removeMacBind', $this->customer));
        $this->assertTrue($this->admin->can('generateBill', $this->customer));
        $this->assertTrue($this->admin->can('editBillingProfile', $this->customer));
        $this->assertTrue($this->admin->can('sendSms', $this->customer));
        $this->assertTrue($this->admin->can('sendLink', $this->customer));
        $this->assertTrue($this->admin->can('advancePayment', $this->customer));
        $this->assertTrue($this->admin->can('changeOperator', $this->customer));
        $this->assertTrue($this->admin->can('editSuspendDate', $this->customer));
        $this->assertTrue($this->admin->can('hotspotRecharge', $this->customer));
        $this->assertTrue($this->admin->can('dailyRecharge', $this->customer));
    }

    /** @test */
    public function operator_cannot_access_admin_only_actions()
    {
        // Actions that should be DENIED for Operator
        $this->assertFalse($this->operator->can('disconnect', $this->customer), 'Operator should NOT have disconnect permission');
        $this->assertFalse($this->operator->can('editSpeedLimit', $this->customer), 'Operator should NOT have editSpeedLimit permission');
        $this->assertFalse($this->operator->can('generateBill', $this->customer), 'Operator should NOT have generateBill permission');
        $this->assertFalse($this->operator->can('editBillingProfile', $this->customer), 'Operator should NOT have editBillingProfile permission');
        $this->assertFalse($this->operator->can('changeOperator', $this->customer), 'Operator should NOT have changeOperator permission');
        $this->assertFalse($this->operator->can('editSuspendDate', $this->customer), 'Operator should NOT have editSuspendDate permission');
        $this->assertFalse($this->operator->can('hotspotRecharge', $this->customer), 'Operator should NOT have hotspotRecharge permission');
        $this->assertFalse($this->operator->can('dailyRecharge', $this->customer), 'Operator should NOT have dailyRecharge permission');
        $this->assertFalse($this->operator->can('delete', $this->customer), 'Operator should NOT have delete permission');
        $this->assertFalse($this->operator->can('activateFup', $this->customer), 'Operator should NOT have activateFup permission');
    }

    /** @test */
    public function sub_operator_cannot_access_admin_only_actions()
    {
        // Actions that should be DENIED for Sub-Operator
        $this->assertFalse($this->subOperator->can('disconnect', $this->customer), 'Sub-Operator should NOT have disconnect permission');
        $this->assertFalse($this->subOperator->can('editSpeedLimit', $this->customer), 'Sub-Operator should NOT have editSpeedLimit permission');
        $this->assertFalse($this->subOperator->can('generateBill', $this->customer), 'Sub-Operator should NOT have generateBill permission');
        $this->assertFalse($this->subOperator->can('editBillingProfile', $this->customer), 'Sub-Operator should NOT have editBillingProfile permission');
        $this->assertFalse($this->subOperator->can('changeOperator', $this->customer), 'Sub-Operator should NOT have changeOperator permission');
        $this->assertFalse($this->subOperator->can('editSuspendDate', $this->customer), 'Sub-Operator should NOT have editSuspendDate permission');
        $this->assertFalse($this->subOperator->can('hotspotRecharge', $this->customer), 'Sub-Operator should NOT have hotspotRecharge permission');
        $this->assertFalse($this->subOperator->can('dailyRecharge', $this->customer), 'Sub-Operator should NOT have dailyRecharge permission');
        $this->assertFalse($this->subOperator->can('delete', $this->customer), 'Sub-Operator should NOT have delete permission');
        $this->assertFalse($this->subOperator->can('activateFup', $this->customer), 'Sub-Operator should NOT have activateFup permission');
    }

    /** @test */
    public function operator_can_view_own_customers()
    {
        // Operator should be able to view their own customers
        $this->assertTrue($this->operator->can('view', $this->customer));
    }

    /** @test */
    public function sub_operator_can_view_own_customers()
    {
        // Create a customer created by sub-operator
        $subOpCustomer = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'operator_level' => 100,
            'name' => 'Sub-Op Customer',
            'created_by' => $this->subOperator->id,
        ]);

        // Sub-Operator should be able to view their own customers
        $this->assertTrue($this->subOperator->can('view', $subOpCustomer));
    }

    /** @test */
    public function operator_with_permission_can_activate_customer()
    {
        // Grant activate_customers permission to operator role
        $operatorRole = Role::where('slug', 'operator')->first();
        if ($operatorRole && method_exists($operatorRole, 'givePermissionTo')) {
            $operatorRole->givePermissionTo('activate_customers');
            $operatorRole->givePermissionTo('edit_customers');
        }

        // Refresh operator to get updated permissions
        $this->operator = $this->operator->fresh();

        // With permission, operator should be able to activate
        $canActivate = $this->operator->can('activate', $this->customer);
        
        // Verify the activation permission works when granted
        $this->assertTrue($canActivate, 'Operator with permissions should be able to activate customer');
    }

    /** @test */
    public function operator_with_permission_can_suspend_customer()
    {
        // Grant suspend_customers permission to operator role
        $operatorRole = Role::where('slug', 'operator')->first();
        if ($operatorRole && method_exists($operatorRole, 'givePermissionTo')) {
            $operatorRole->givePermissionTo('suspend_customers');
            $operatorRole->givePermissionTo('edit_customers');
        }

        // Refresh operator to get updated permissions
        $this->operator = $this->operator->fresh();

        // With permission, operator should be able to suspend
        $canSuspend = $this->operator->can('suspend', $this->customer);
        
        // Verify the suspend permission works when granted
        $this->assertTrue($canSuspend, 'Operator with permissions should be able to suspend customer');
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_customers') || $user->operator_level <= 80;
    }

    /**
     * Determine if the user can view the customer.
     */
    public function view(User $user, User $customer): bool
    {
        // Developer and Super Admin can view all
        if ($user->operator_level <= 10) {
            return true;
        }

        // Check if user has permission
        if (! $user->hasPermission('view_customers')) {
            return false;
        }

        // Check tenant isolation
        if ($user->tenant_id && $customer->tenant_id !== $user->tenant_id) {
            return false;
        }

        // Check if has special permission to access all customers
        if ($this->hasSpecialPermission($user, 'access_all_customers')) {
            return true;
        }

        // Check manager hierarchy (if manager_id is set)
        if ($customer->manager_id === $user->id) {
            return true;
        }

        // Check if customer belongs to same zone/area
        if ($this->isSameZoneOrArea($user, $customer)) {
            return true;
        }

        // Operators and sub-operators can view their own customers
        if ($user->operator_level <= 40) {
            return $this->isInHierarchy($user, $customer);
        }

        return false;
    }

    /**
     * Determine if the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_customers') || $user->operator_level <= 30;
    }

    /**
     * Determine if the user can update the customer.
     */
    public function update(User $user, User $customer): bool
    {
        // Check basic permission
        if (! $user->hasPermission('edit_customers')) {
            return false;
        }

        // Check tenant isolation
        if ($user->tenant_id && $customer->tenant_id !== $user->tenant_id) {
            return false;
        }

        // Developer and Super Admin can edit all
        if ($user->operator_level <= 10) {
            return true;
        }

        // Check if has special permission
        if ($this->hasSpecialPermission($user, 'access_all_customers')) {
            return true;
        }

        // Check manager hierarchy
        if ($customer->manager_id === $user->id) {
            return true;
        }

        // Check zone/area-based access control
        if ($this->isSameZoneOrArea($user, $customer)) {
            return $this->isInHierarchy($user, $customer);
        }

        return false;
    }

    /**
     * Check if customer is in the user's zone or area.
     */
    private function isSameZoneOrArea(User $user, User $customer): bool
    {
        // If user has zone_id set, check if customer is in same zone
        if (isset($user->zone_id) && isset($customer->zone_id)) {
            return $user->zone_id === $customer->zone_id;
        }

        // If user has area_id set, check if customer is in same area
        if (isset($user->area_id) && isset($customer->area_id)) {
            return $user->area_id === $customer->area_id;
        }

        // If no zone/area restrictions, allow based on other checks
        return true;
    }

    /**
     * Check if customer is in user's management hierarchy.
     */
    private function isInHierarchy(User $user, User $customer): bool
    {
        // Check if user created this customer
        if (isset($customer->created_by) && $customer->created_by === $user->id) {
            return true;
        }

        // Check if customer is in user's subordinates
        return $user->subordinates()->where('id', $customer->id)->exists();
    }

    /**
     * Determine if the user can delete the customer.
     */
    public function delete(User $user, User $customer): bool
    {
        // Only high-level operators can delete
        if ($user->operator_level > 30) {
            return false;
        }

        // Check permission
        if (! $user->hasPermission('delete_customers')) {
            return false;
        }

        // Check tenant isolation
        if ($user->tenant_id && $customer->tenant_id !== $user->tenant_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user can suspend the customer.
     */
    public function suspend(User $user, User $customer): bool
    {
        return $this->update($user, $customer) && $user->hasPermission('suspend_customers');
    }

    /**
     * Determine if the user can activate the customer.
     */
    public function activate(User $user, User $customer): bool
    {
        return $this->update($user, $customer) && $user->hasPermission('activate_customers');
    }

    /**
     * Check if user has a special permission.
     */
    private function hasSpecialPermission(User $user, string $permission): bool
    {
        return $user->operatorPermissions()
            ->where('permission_key', $permission)
            ->where('is_enabled', true)
            ->exists();
    }
}

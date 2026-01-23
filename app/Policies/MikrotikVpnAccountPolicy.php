<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MikrotikVpnAccount;
use App\Models\User;

class MikrotikVpnAccountPolicy
{
    /**
     * Determine if the user can view the VPN account.
     */
    public function view(User $user, MikrotikVpnAccount $account): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine if the user can update the VPN account.
     */
    public function update(User $user, MikrotikVpnAccount $account): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can delete the VPN account.
     */
    public function delete(User $user, MikrotikVpnAccount $account): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}

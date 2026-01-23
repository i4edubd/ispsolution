<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MikrotikIpPool;
use App\Models\User;

class MikrotikIpPoolPolicy
{
    /**
     * Determine if the user can view the IP pool.
     */
    public function view(User $user, MikrotikIpPool $pool): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine if the user can update the IP pool.
     */
    public function update(User $user, MikrotikIpPool $pool): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can delete the IP pool.
     */
    public function delete(User $user, MikrotikIpPool $pool): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}

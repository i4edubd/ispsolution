<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MikrotikProfile;
use App\Models\User;

class MikrotikProfilePolicy
{
    /**
     * Determine if the user can view the profile.
     */
    public function view(User $user, MikrotikProfile $profile): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine if the user can update the profile.
     */
    public function update(User $user, MikrotikProfile $profile): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can delete the profile.
     */
    public function delete(User $user, MikrotikProfile $profile): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}

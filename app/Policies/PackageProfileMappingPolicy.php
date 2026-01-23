<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PackageProfileMapping;
use App\Models\User;

class PackageProfileMappingPolicy
{
    /**
     * Determine if the user can view the package mapping.
     */
    public function view(User $user, PackageProfileMapping $mapping): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine if the user can update the package mapping.
     */
    public function update(User $user, PackageProfileMapping $mapping): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can delete the package mapping.
     */
    public function delete(User $user, PackageProfileMapping $mapping): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MikrotikQueue;
use App\Models\User;

class MikrotikQueuePolicy
{
    /**
     * Determine if the user can view the queue.
     */
    public function view(User $user, MikrotikQueue $queue): bool
    {
        return true; // Allow all authenticated users to view
    }

    /**
     * Determine if the user can update the queue.
     */
    public function update(User $user, MikrotikQueue $queue): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can delete the queue.
     */
    public function delete(User $user, MikrotikQueue $queue): bool
    {
        return $user->isAdmin() || $user->isManager();
    }
}

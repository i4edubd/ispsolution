<?php

namespace App\Contracts;

use App\Models\User;

interface RadiusServiceInterface
{
    /**
     * Authenticate a user against RADIUS database
     * 
     * @return array|false Returns RADIUS attributes on success, false on failure
     */
    public function authenticate(string $username, string $password): array|false;

    /**
     * Start accounting session
     */
    public function accountingStart(array $data): bool;

    /**
     * Update accounting session
     */
    public function accountingUpdate(array $data): bool;

    /**
     * Stop accounting session
     */
    public function accountingStop(array $data): bool;

    /**
     * Sync user credentials and attributes to RADIUS database
     */
    public function syncUser(User $user, ?string $password = null): bool;

    /**
     * Remove user from RADIUS database
     */
    public function removeUser(User $user): bool;

    /**
     * Get user statistics from accounting records
     */
    public function getUserStats(string $username): array;
}

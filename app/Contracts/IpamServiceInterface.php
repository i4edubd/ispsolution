<?php

namespace App\Contracts;

use App\Models\IpAllocation;
use App\Models\IpPool;
use App\Models\IpSubnet;
use Illuminate\Support\Collection;

interface IpamServiceInterface
{
    /**
     * Create a new IP pool
     */
    public function createPool(string $name, string $type, ?string $description = null): IpPool;

    /**
     * Create a new subnet within a pool
     */
    public function createSubnet(
        int $poolId,
        string $network,
        int $prefix,
        ?string $gateway = null,
        ?int $vlanId = null,
        ?string $description = null
    ): IpSubnet;

    /**
     * Allocate an IP address to a user
     */
    public function allocateIP(
        int $subnetId,
        int $userId,
        string $type = 'dynamic',
        ?int $ttl = null
    ): IpAllocation;

    /**
     * Release an IP allocation
     */
    public function releaseIP(int $allocationId, ?string $reason = null): bool;

    /**
     * Reserve a specific IP address
     */
    public function reserveIP(int $subnetId, string $ipAddress, ?string $reason = null): IpAllocation;

    /**
     * List all allocations for a user
     */
    public function listAllocations(int $userId): Collection;

    /**
     * Clean up expired allocations
     */
    public function cleanupExpired(): int;

    /**
     * Detect if a network overlaps with existing subnets
     */
    public function detectOverlap(string $network, int $prefix): bool;

    /**
     * Get next available IP in a subnet
     */
    public function getNextAvailableIP(int $subnetId): ?string;
}

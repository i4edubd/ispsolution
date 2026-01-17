<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OltPerformanceMetric extends Model
{
    protected $fillable = [
        'olt_id',
        'cpu_usage',
        'memory_usage',
        'temperature',
        'bandwidth_rx',
        'bandwidth_tx',
        'total_onus',
        'online_onus',
        'offline_onus',
        'port_utilization',
    ];

    protected $casts = [
        'cpu_usage' => 'decimal:2',
        'memory_usage' => 'decimal:2',
        'temperature' => 'decimal:2',
        'bandwidth_rx' => 'integer',
        'bandwidth_tx' => 'integer',
        'total_onus' => 'integer',
        'online_onus' => 'integer',
        'offline_onus' => 'integer',
        'port_utilization' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function olt(): BelongsTo
    {
        return $this->belongsTo(Olt::class);
    }

    public function scopeForOlt($query, int $oltId)
    {
        return $query->where('olt_id', $oltId);
    }

    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public static function recordMetrics(int $oltId, array $metrics): self
    {
        return static::create(array_merge(['olt_id' => $oltId], $metrics));
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFup extends Model
{
    use HasFactory;

    protected $table = 'package_fup';

    protected $fillable = [
        'package_id',
        'type',
        'data_limit_bytes',
        'time_limit_minutes',
        'reduced_speed',
        'reset_period',
        'notify_customer',
        'notify_at_percent',
        'is_active',
    ];

    protected $casts = [
        'data_limit_bytes' => 'integer',
        'time_limit_minutes' => 'integer',
        'notify_customer' => 'boolean',
        'notify_at_percent' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the package that owns this FUP policy.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get formatted data limit.
     */
    public function getFormattedDataLimitAttribute(): string
    {
        if (!$this->data_limit_bytes) {
            return 'Unlimited';
        }

        $bytes = $this->data_limit_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        
        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Get formatted time limit.
     */
    public function getFormattedTimeLimitAttribute(): string
    {
        if (!$this->time_limit_minutes) {
            return 'Unlimited';
        }

        $minutes = $this->time_limit_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . 
                   ($mins > 0 ? ' ' . $mins . ' minute' . ($mins > 1 ? 's' : '') : '');
        }

        return $mins . ' minute' . ($mins > 1 ? 's' : '');
    }

    /**
     * Check if FUP is exceeded for given usage.
     */
    public function isExceeded(int $usedBytes = 0, int $usedMinutes = 0): bool
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->type) {
            case 'data':
                return $this->data_limit_bytes && $usedBytes >= $this->data_limit_bytes;
            case 'time':
                return $this->time_limit_minutes && $usedMinutes >= $this->time_limit_minutes;
            case 'both':
                return ($this->data_limit_bytes && $usedBytes >= $this->data_limit_bytes) ||
                       ($this->time_limit_minutes && $usedMinutes >= $this->time_limit_minutes);
        }

        return false;
    }

    /**
     * Check if customer should be notified.
     */
    public function shouldNotify(int $usedBytes = 0, int $usedMinutes = 0): bool
    {
        if (!$this->notify_customer || !$this->is_active) {
            return false;
        }

        $threshold = $this->notify_at_percent / 100;

        switch ($this->type) {
            case 'data':
                if ($this->data_limit_bytes) {
                    $usage = $usedBytes / $this->data_limit_bytes;
                    return $usage >= $threshold && $usage < 1;
                }
                break;
            case 'time':
                if ($this->time_limit_minutes) {
                    $usage = $usedMinutes / $this->time_limit_minutes;
                    return $usage >= $threshold && $usage < 1;
                }
                break;
            case 'both':
                if ($this->data_limit_bytes) {
                    $dataUsage = $usedBytes / $this->data_limit_bytes;
                    if ($dataUsage >= $threshold && $dataUsage < 1) {
                        return true;
                    }
                }
                if ($this->time_limit_minutes) {
                    $timeUsage = $usedMinutes / $this->time_limit_minutes;
                    if ($timeUsage >= $threshold && $timeUsage < 1) {
                        return true;
                    }
                }
                break;
        }

        return false;
    }

    /**
     * Get usage percentage for data.
     */
    public function getDataUsagePercent(int $usedBytes): float
    {
        if (!$this->data_limit_bytes) {
            return 0;
        }

        return min(100, round(($usedBytes / $this->data_limit_bytes) * 100, 2));
    }

    /**
     * Get usage percentage for time.
     */
    public function getTimeUsagePercent(int $usedMinutes): float
    {
        if (!$this->time_limit_minutes) {
            return 0;
        }

        return min(100, round(($usedMinutes / $this->time_limit_minutes) * 100, 2));
    }
}

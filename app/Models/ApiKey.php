<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'key',
        'secret',
        'is_active',
        'expires_at',
        'last_used_at',
        'permissions',
        'rate_limit',
        'ip_whitelist',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'permissions' => 'array',
        'ip_whitelist' => 'array',
        'rate_limit' => 'integer',
    ];

    protected $hidden = [
        'secret',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(): array
    {
        return [
            'key' => 'pk_' . Str::random(32),
            'secret' => 'sk_' . Str::random(64),
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGateway extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug', // twilio, nexmo, msg91, bulksms, custom
        'is_active',
        'is_default',
        'configuration', // JSON field for API keys, secrets, sender_id, etc.
        'balance',
        'rate_per_sms',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'configuration' => 'encrypted:array',
        'balance' => 'decimal:2',
        'rate_per_sms' => 'decimal:4',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function hasBalance(int $smsCount = 1): bool
    {
        return $this->balance >= ($smsCount * $this->rate_per_sms);
    }
}

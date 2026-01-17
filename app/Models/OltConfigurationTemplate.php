<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OltConfigurationTemplate extends Model
{
    protected $fillable = [
        'name',
        'vendor',
        'model',
        'description',
        'template_content',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVendor($query, string $vendor)
    {
        return $query->where('vendor', $vendor);
    }

    public function renderTemplate(array $values = []): string
    {
        $content = $this->template_content;
        
        foreach ($values as $key => $value) {
            $content = str_replace("{{" . $key . "}}", (string) $value, $content);
        }
        
        return $content;
    }
}

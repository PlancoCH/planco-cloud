<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'notes',
        'api_key',
        'mapping_key',
        'polling_rate',
        'wifi_rssi',
        'led_enabled',
    ];

    protected $casts = [
        'led_enabled' => 'boolean',
    ];

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setApiKeyAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['api_key'] = null;

            return;
        }

        $this->attributes['api_key'] = hash('sha256', $value);
    }

    public function setMappingKeyAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['mapping_key'] = null;

            return;
        }

        $this->attributes['mapping_key'] = hash('sha256', $value);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class Device extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'notes',
        'api_key',
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
}

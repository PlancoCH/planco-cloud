<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class Device extends Model
{
    protected $fillable = [
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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'device_user')
            ->using(DeviceUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function setApiKeyAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['api_key'] = null;
            return;
        }

        $this->attributes['api_key'] = hash('sha256', $value);
    }

    protected static function booted()
    {
        // Prevent non-owner members from updating or deleting devices at model level.
        static::saving(function (self $device) {
            if (! $device->exists) {
                return true; // creating — allow
            }

            if (app()->runningInConsole()) {
                return true; // allow console operations (seeds, artisan)
            }

            $user = auth()->user();
            if (! $user) {
                return true; // no auth context — allow
            }

            $role = DB::table('device_user')
                ->where('user_id', $user->id)
                ->where('device_id', $device->id)
                ->value('role');

            if ($role === 'member') {
                throw new AuthorizationException('Members are not allowed to modify devices.');
            }

            return true;
        });

        static::deleting(function (self $device) {
            if (app()->runningInConsole()) {
                return true;
            }

            $user = auth()->user();
            if (! $user) {
                return true;
            }

            $role = DB::table('device_user')
                ->where('user_id', $user->id)
                ->where('device_id', $device->id)
                ->value('role');

            if ($role === 'member') {
                throw new AuthorizationException('Members are not allowed to delete devices.');
            }

            return true;
        });
    }
}

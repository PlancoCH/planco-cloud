<?php

namespace App\Models;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Plant extends Model
{
    protected $fillable = [
        'device_id',
        'plant_type_id',
        'custom_image',
        'nickname',
        'notes',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function plantType(): BelongsTo
    {
        return $this->belongsTo(PlantType::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(PlantData::class);
    }

    public function insights(): HasMany
    {
        return $this->hasMany(DailyInsight::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'plant_user')
            ->using(PlantUser::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    protected static function booted()
    {
        static::saving(function (self $plant) {
            if (! $plant->exists) {
                return true; // creating — allow
            }

            if (app()->runningInConsole()) {
                return true; // allow console operations
            }

            $user = auth()->user();
            if (! $user) {
                return true; // no auth context — allow
            }

            $role = DB::table('plant_user')
                ->where('user_id', $user->id)
                ->where('plant_id', $plant->id)
                ->value('role');

            if ($role === 'member') {
                throw new AuthorizationException('Members are not allowed to modify plants.');
            }

            return true;
        });

        static::deleting(function (self $plant) {
            if (app()->runningInConsole()) {
                return true;
            }

            $user = auth()->user();
            if (! $user) {
                return true;
            }

            $role = DB::table('plant_user')
                ->where('user_id', $user->id)
                ->where('plant_id', $plant->id)
                ->value('role');

            if ($role === 'member') {
                throw new AuthorizationException('Members are not allowed to delete plants.');
            }

            return true;
        });
    }
}

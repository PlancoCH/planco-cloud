<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DeviceUser extends Pivot
{
    protected $table = 'device_user';

    protected $fillable = [
        'user_id',
        'device_id',
        'role',
    ];

    public $incrementing = true;
    public $timestamps = true;
}

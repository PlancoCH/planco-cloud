<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlantUser extends Pivot
{
    protected $table = 'plant_user';

    protected $fillable = [
        'user_id',
        'plant_id',
        'role',
    ];

    public $incrementing = true;

    public $timestamps = true;
}

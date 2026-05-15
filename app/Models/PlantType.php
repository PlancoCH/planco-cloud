<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantType extends Model
{
    protected $fillable = [
        'common_name',
        'description',
        'standard_image',
        'scientific_name',
        'ideal_temp',
        'ideal_moisture',
        'ideal_light_lux',
        'ideal_humidity',
    ];

    public function plants(): HasMany
    {
        return $this->hasMany(Plant::class);
    }
}

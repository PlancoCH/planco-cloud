<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantData extends Model
{
    protected $table = 'plant_data';

    protected $fillable = [
        'plant_id',
        'plant_score',
        'temperature',
        'humidity',
        'air_pressure',
        'light_intensity',
        'soil_moisture',
    ];

    public function plant(): BelongsTo
    {
        return $this->belongsTo(Plant::class);
    }
}

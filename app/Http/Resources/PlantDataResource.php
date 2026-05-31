<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plant_id' => $this->plant_id,
            'plant_score' => $this->plant_score,
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'air_pressure' => $this->air_pressure,
            'light_intensity' => $this->light_intensity,
            'soil_moisture' => $this->soil_moisture,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantTypeResource extends JsonResource
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
            'common_name' => $this->common_name,
            'description' => $this->description,
            'scientific_name' => $this->scientific_name,
            'ideal_temp' => $this->ideal_temp,
            'ideal_moisture' => $this->ideal_moisture,
            'ideal_light_lux' => $this->ideal_light_lux,
            'ideal_humidity' => $this->ideal_humidity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

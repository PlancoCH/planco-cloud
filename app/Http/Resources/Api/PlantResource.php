<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantResource extends JsonResource
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
            'device_id' => $this->device_id,
            'plant_type_id' => $this->plant_type_id,
            'nickname' => $this->nickname,
            'notes' => $this->notes,
            'custom_image' => $this->custom_image,
            'sharing_token' => $this->sharing_token,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'plant_type' => new PlantTypeResource($this->whenLoaded('plantType')),
            'device' => new DeviceResource($this->whenLoaded('device')),
            'role' => $this->whenPivotLoaded('plant_user', function () {
                return $this->pivot->role;
            }),
        ];
    }
}

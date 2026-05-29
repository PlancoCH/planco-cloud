<?php

namespace App\Http\Requests\Api\Plant;

use Illuminate\Foundation\Http\FormRequest;

class MapDeviceToPlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_id' => ['required', 'exists:devices,id'],
        ];
    }
}

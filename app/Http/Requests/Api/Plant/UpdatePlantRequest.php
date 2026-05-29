<?php

namespace App\Http\Requests\Api\Plant;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'plant_type_id' => ['sometimes', 'exists:plant_types,id'],
            'custom_image' => ['nullable', 'string'],
        ];
    }
}

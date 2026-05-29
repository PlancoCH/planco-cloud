<?php

namespace App\Http\Requests\Api\Plant;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'plant_type_id' => ['required', 'exists:plant_types,id'],
            'custom_image' => ['nullable', 'string'],
        ];
    }
}

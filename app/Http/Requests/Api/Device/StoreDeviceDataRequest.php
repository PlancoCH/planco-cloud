<?php

namespace App\Http\Requests\Api\Device;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by the VerifyDeviceApiKey middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric'],
            'humidity' => ['required', 'numeric'],
            'air_pressure' => ['nullable', 'numeric'],
            'light_intensity' => ['required', 'numeric'],
            'soil_moisture' => ['required', 'numeric'],
        ];
    }
}

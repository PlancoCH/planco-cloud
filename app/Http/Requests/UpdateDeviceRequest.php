<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Users can only update their own devices. Authorization logic can be handled here or in a policy.
        return $this->user()->id === $this->route('device')->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'polling_rate' => ['sometimes', 'integer', 'in:15,30,60,120,300,600,1800,3600'], // Assuming some example dropdown values, feel free to adjust
            'led_enabled' => ['sometimes', 'boolean'],
        ];
    }
}

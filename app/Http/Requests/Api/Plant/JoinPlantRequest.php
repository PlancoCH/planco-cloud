<?php

namespace App\Http\Requests\Api\Plant;

use Illuminate\Foundation\Http\FormRequest;

class JoinPlantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sharing_token' => ['required', 'string', 'exists:plants,sharing_token'],
        ];
    }
}

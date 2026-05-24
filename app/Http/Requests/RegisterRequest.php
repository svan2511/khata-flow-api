<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be 10-15 digits.',
        ];
    }
}

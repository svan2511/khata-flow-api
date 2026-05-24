<?php

namespace App\Http\Requests\Customer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CustomerQuickRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'phone' => 'sometimes|string|regex:/^[\d\+\-\(\)\s]{7,20}$/|max:20',
            'email' => 'sometimes|email|max:255',
            'address' => 'sometimes|string|max:500',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'phone.regex' => 'Please enter a valid phone number.',
        ];
    }
}

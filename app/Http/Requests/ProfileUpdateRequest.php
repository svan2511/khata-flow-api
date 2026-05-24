<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email,'.$this->user()->id,
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/',
                'unique:users,phone,'.$this->user()->id,
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'shop_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'owner_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
            ],
            'state' => [
                'nullable',
                'string',
                'max:100',
            ],
            'pincode' => [
                'nullable',
                'string',
                'regex:/^[0-9]{6,10}$/',
            ],
            'gstin' => [
                'nullable',
                'string',
                'regex:/^[0-9A-Z]{15}$/',
            ],
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already taken.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.regex' => 'Phone number must be 10-15 digits.',
            'avatar.image' => 'Avatar must be an image file.',
            'logo.image' => 'Logo must be an image file.',
            'logo.max' => 'Logo must not exceed 2MB.',
            'gstin.regex' => 'GSTIN must be 15 alphanumeric characters.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\OtpPurpose;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class VerifyOtpRequest extends FormRequest
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
            'otp' => [
                'required',
                'string',
                'size:4',
            ],
            'purpose' => [
                'required',
                'string',
                new Enum(OtpPurpose::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Phone number must be 10-15 digits.',
            'otp.required' => 'OTP is required.',
            'otp.size' => 'OTP must be 4 digits.',
            'purpose.required' => 'OTP purpose is required.',
        ];
    }
}

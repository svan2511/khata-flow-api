<?php

namespace App\Http\Requests\Bill;

use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'payment_method' => ['required', 'string', Rule::in(PaymentMethod::values())],
            'reference' => 'sometimes|string|max:100',
            'notes' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be positive.',
            'payment_method.required' => 'Payment method is required.',
        ];
    }
}

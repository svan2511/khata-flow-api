<?php

namespace App\Http\Requests\Bill;

use App\Enums\DiscountType;
use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBillRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'sometimes|string|size:36',
            'items.*.product_name' => 'required_without:items.*.product_uuid|string|max:255',
            'items.*.unit_price' => 'required_without:items.*.product_uuid|numeric|min:0|max:999999.99',
            'items.*.quantity' => 'required|numeric|min:0.01|max:999999.99',
            'items.*.discount_type' => 'sometimes|string|in:'.implode(',', DiscountType::values()),
            'items.*.discount_value' => 'sometimes|numeric|min:0|max:999999.99',
            'items.*.gst_rate' => 'sometimes|numeric|min:0|max:100',

            'customer_uuid' => 'sometimes|string|size:36',
            'discount_type' => 'sometimes|string|in:'.implode(',', DiscountType::values()),
            'discount_value' => 'sometimes|numeric|min:0|max:999999.99',
            'paid_amount' => 'sometimes|numeric|min:0|max:999999.99',
            'payment_method' => ['required', 'string', Rule::in(PaymentMethod::values())],
            'notes' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required to create a bill.',
            'items.min' => 'At least one item is required.',
            'items.*.product_uuid.size' => 'Invalid product ID format.',
            'items.*.product_name.required_without' => 'Product name is required for custom items.',
            'items.*.unit_price.required_without' => 'Unit price is required for custom items.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 0.01.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method. Use: cash, upi, card, mix, or credit.',
        ];
    }
}

<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'product_category_id' => ['nullable', 'integer', 'exists:product_categories,id'],
            'category_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

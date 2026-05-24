<?php

namespace App\Http\Requests\Bill;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListBillRequest extends FormRequest
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
            'per_page' => 'sometimes|integer|min:1|max:100',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'search' => 'sometimes|string|max:100',
            'payment_status' => 'sometimes|string|in:pending,partial,paid,cancelled',
        ];
    }
}

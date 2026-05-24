<?php

namespace App\DTOs;

class StoreExpenseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly float $amount,
        public readonly string $expenseDate,
        public readonly ?string $category = null,
        public readonly ?string $paymentMethod = 'cash',
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            amount: (float) $data['amount'],
            expenseDate: $data['expense_date'],
            category: $data['category'] ?? null,
            paymentMethod: $data['payment_method'] ?? 'cash',
            notes: $data['notes'] ?? null,
        );
    }
}

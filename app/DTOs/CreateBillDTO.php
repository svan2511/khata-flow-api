<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class CreateBillDTO
{
    public readonly int $userId;

    public readonly ?int $customerId;

    /**
     * @param  Collection<int, BillItemDTO>  $items
     */
    public function __construct(
        int $userId,
        public readonly Collection $items,
        public readonly ?string $customerUuid = null,
        public readonly ?string $discountType = null,
        public readonly ?float $discountValue = null,
        public readonly ?float $paidAmount = null,
        public readonly string $paymentMethod = 'cash',
        public readonly ?string $notes = null,
        ?int $customerId = null,
    ) {
        $this->userId = $userId;
        $this->customerId = $customerId;
    }

    public static function fromRequest(array $data, int $userId): self
    {
        $items = collect($data['items'])
            ->map(fn (array $item) => BillItemDTO::fromRequest($item));

        return new self(
            userId: $userId,
            items: $items,
            customerUuid: $data['customer_uuid'] ?? null,
            discountType: $data['discount_type'] ?? null,
            discountValue: isset($data['discount_value']) ? (float) $data['discount_value'] : null,
            paidAmount: isset($data['paid_amount']) ? (float) $data['paid_amount'] : null,
            paymentMethod: $data['payment_method'] ?? 'cash',
            notes: $data['notes'] ?? null,
        );
    }

    public function hasCustomer(): bool
    {
        return $this->customerUuid !== null;
    }

    public function hasBillDiscount(): bool
    {
        return $this->discountValue !== null && $this->discountValue > 0;
    }

    public function isCredit(): bool
    {
        return $this->paymentMethod === 'credit';
    }
}

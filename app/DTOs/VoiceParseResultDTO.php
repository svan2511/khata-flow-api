<?php

namespace App\DTOs;

class VoiceParseResultDTO
{
    /**
     * @param  VoiceParseItemDTO[]  $items
     * @param  array{uuid: string, name: string, phone: ?string}|null  $customer
     * @param  array{type: string, value: float}|null  $discount
     */
    public function __construct(
        public readonly array $items,
        public readonly ?array $customer,
        public readonly string $customerName,
        public readonly string $paymentMethod,
        public readonly ?array $discount,
        public readonly string $notes,
        public readonly string $assistantReply,
        public readonly string $source = 'gemini',
    ) {}

    public function toArray(): array
    {
        return [
            'items' => array_map(
                fn (VoiceParseItemDTO $item) => $item->toArray(),
                $this->items
            ),
            'customer' => $this->customer,
            'customer_name' => $this->customerName,
            'payment_method' => $this->paymentMethod,
            'discount' => $this->discount,
            'notes' => $this->notes,
            'assistant_reply' => $this->assistantReply,
            'source' => $this->source,
        ];
    }
}

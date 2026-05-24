<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class ShopSetupDTO
{
    public function __construct(
        public readonly string $shopName,
        public readonly ?string $ownerName,
        public readonly ?string $phone,
        public readonly ?string $email,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $pincode,
        public readonly ?string $gstin,
        public readonly ?UploadedFile $logo,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            shopName: $data['shop_name'],
            ownerName: $data['owner_name'] ?? null,
            phone: isset($data['phone']) ? preg_replace('/\D/', '', $data['phone']) : null,
            email: $data['email'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            pincode: $data['pincode'] ?? null,
            gstin: $data['gstin'] ?? null,
            logo: $data['logo'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'shop_name' => $this->shopName,
            'owner_name' => $this->ownerName,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
            'gstin' => $this->gstin,
        ];
    }

    public function hasLogo(): bool
    {
        return ! is_null($this->logo) && $this->logo->isValid();
    }
}

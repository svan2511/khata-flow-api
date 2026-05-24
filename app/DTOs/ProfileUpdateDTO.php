<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class ProfileUpdateDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?UploadedFile $avatar,
        public readonly ?UploadedFile $logo,
        public readonly ?string $shopName,
        public readonly ?string $ownerName,
        public readonly ?string $address,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly ?string $pincode,
        public readonly ?string $gstin,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            phone: isset($data['phone']) ? preg_replace('/\D/', '', $data['phone']) : null,
            avatar: $data['avatar'] ?? null,
            logo: $data['logo'] ?? null,
            shopName: $data['shop_name'] ?? null,
            ownerName: $data['owner_name'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            pincode: $data['pincode'] ?? null,
            gstin: $data['gstin'] ?? null,
        );
    }

    public function hasAvatar(): bool
    {
        return ! is_null($this->avatar) && $this->avatar->isValid();
    }

    public function hasLogo(): bool
    {
        return ! is_null($this->logo) && $this->logo->isValid();
    }

    public function hasUserUpdates(): bool
    {
        return ! is_null($this->name) || ! is_null($this->email) || ! is_null($this->phone) || $this->hasAvatar();
    }

    public function hasShopUpdates(): bool
    {
        return ! is_null($this->shopName) || ! is_null($this->ownerName) || ! is_null($this->address)
            || ! is_null($this->city) || ! is_null($this->state) || ! is_null($this->pincode)
            || ! is_null($this->gstin) || $this->hasLogo();
    }
}

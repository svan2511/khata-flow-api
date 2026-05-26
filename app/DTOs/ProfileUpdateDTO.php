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
        $nullable = fn(mixed $v): ?string => (isset($v) && $v !== '') ? $v : null;

        return new self(
            name: $nullable($data['name'] ?? null),
            email: $nullable($data['email'] ?? null),
            phone: isset($data['phone']) && $data['phone'] !== '' ? preg_replace('/\D/', '', $data['phone']) : null,
            avatar: $data['avatar'] ?? null,
            logo: $data['logo'] ?? null,
            shopName: $nullable($data['shop_name'] ?? null),
            ownerName: $nullable($data['owner_name'] ?? null),
            address: $nullable($data['address'] ?? null),
            city: $nullable($data['city'] ?? null),
            state: $nullable($data['state'] ?? null),
            pincode: $nullable($data['pincode'] ?? null),
            gstin: $nullable($data['gstin'] ?? null),
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

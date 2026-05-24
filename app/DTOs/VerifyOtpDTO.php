<?php

namespace App\DTOs;

class VerifyOtpDTO
{
    public function __construct(
        public readonly string $phone,
        public readonly string $otp,
        public readonly string $purpose,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            phone: preg_replace('/\D/', '', $data['phone']),
            otp: $data['otp'],
            purpose: $data['purpose'] ?? 'registration',
        );
    }

    public function toArray(): array
    {
        return [
            'phone' => $this->phone,
            'otp' => $this->otp,
            'purpose' => $this->purpose,
        ];
    }
}

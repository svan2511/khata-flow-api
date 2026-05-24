<?php

namespace App\Repositories\Contracts;

use App\Models\Otp;

interface OtpRepositoryInterface
{
    public function create(array $data): Otp;

    public function findValidOtp(string $phone, string $otp, string $purpose): ?Otp;

    public function markAsUsed(Otp $otp): Otp;

    public function invalidatePreviousOtps(string $phone, string $purpose): void;

    public function getRecentOtp(string $phone, string $purpose): ?Otp;
}

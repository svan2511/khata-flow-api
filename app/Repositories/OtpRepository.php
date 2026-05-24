<?php

namespace App\Repositories;

use App\Models\Otp;
use App\Repositories\Contracts\OtpRepositoryInterface;

class OtpRepository implements OtpRepositoryInterface
{
    public function create(array $data): Otp
    {
        return Otp::create($data);
    }

    public function findValidOtp(string $phone, string $otp, string $purpose): ?Otp
    {
        return Otp::valid()
            ->forPhone($phone, $purpose)
            ->where('otp', $otp)
            ->latest()
            ->first();
    }

    public function markAsUsed(Otp $otp): Otp
    {
        $otp->update([
            'is_used' => true,
            'verified_at' => now(),
        ]);

        return $otp->fresh();
    }

    public function invalidatePreviousOtps(string $phone, string $purpose): void
    {
        Otp::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }

    public function getRecentOtp(string $phone, string $purpose): ?Otp
    {
        return Otp::forPhone($phone, $purpose)
            ->valid()
            ->latest()
            ->first();
    }
}

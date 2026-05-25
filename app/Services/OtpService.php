<?php

namespace App\Services;

use App\Exceptions\OtpException;
use App\Models\Otp;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private const OTP_LENGTH = 4;

    private const OTP_EXPIRY_MINUTES = 10;

    private const RESEND_THROTTLE_SECONDS = 30;

    public function __construct(
        private readonly OtpRepositoryInterface $otpRepository,
    ) {}

    public function generate(string $phone, string $purpose): Otp
    {
        $recentOtp = $this->otpRepository->getRecentOtp($phone, $purpose);

        if ($recentOtp && $recentOtp->created_at->diffInSeconds(now()) < self::RESEND_THROTTLE_SECONDS) {
            $waitSeconds = self::RESEND_THROTTLE_SECONDS - $recentOtp->created_at->diffInSeconds(now());
            throw new OtpException("Please wait {$waitSeconds} seconds before requesting a new OTP.");
        }

        $this->otpRepository->invalidatePreviousOtps($phone, $purpose);

        $otp = $this->otpRepository->create([
            'phone' => $phone,
            'otp' => $this->generateOtp(),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        Log::info("OTP sent to {$phone} for {$purpose}: {$otp->otp}");

        return $otp;
    }

    public function verify(string $phone, string $otp, string $purpose): Otp
    {
        $otpRecord = $this->otpRepository->findValidOtp($phone, $otp, $purpose);

        if (! $otpRecord) {
            throw new OtpException('Invalid or expired OTP.');
        }

        return $this->otpRepository->markAsUsed($otpRecord);
    }

    private function generateOtp(): string
    {
        $otp = '';
        for ($i = 0; $i < self::OTP_LENGTH; $i++) {
            $otp .= random_int(0, 9);
        }

        return $otp;
    }
}

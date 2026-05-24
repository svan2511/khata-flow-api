<?php

namespace App\Services;

use App\DTOs\RegisterDTO;
use App\DTOs\VerifyOtpDTO;
use App\Enums\OtpPurpose;
use App\Exceptions\OtpException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;

class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly OtpService $otpService,
    ) {}

    public function register(RegisterDTO $dto): User
    {
        $this->otpService->generate($dto->phone, OtpPurpose::Registration->value);

        $user = $this->userRepository->findByPhone($dto->phone);

        if (! $user) {
            $user = $this->userRepository->create([
                'phone' => $dto->phone,
            ]);
        }

        return $user;
    }

    public function verifyOtp(VerifyOtpDTO $dto): array
    {
        $this->otpService->verify($dto->phone, $dto->otp, $dto->purpose);

        $user = $this->userRepository->findOrCreateByPhone($dto->phone);

        if (! $user->isPhoneVerified()) {
            $this->userRepository->markPhoneAsVerified($user);
        }

        try {
            $tokenResult = $user->createToken('dukaan-sahayak');
            $token = $tokenResult->accessToken;
        } catch (\RuntimeException $e) {
            Log::error('Token creation failed', ['error' => $e->getMessage()]);
            throw new OtpException('Unable to generate authentication token. Please try again.', 500);
        }

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout(User $user): void
    {
        $user->token()->revoke();
    }
}

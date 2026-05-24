<?php

namespace App\Http\Controllers;

use App\DTOs\RegisterDTO;
use App\DTOs\VerifyOtpDTO;
use App\Exceptions\OtpException;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $dto = RegisterDTO::fromRequest($request->validated());
            $user = $this->authService->register($dto);

            return $this->success(
                data: new UserResource($user),
                message: 'OTP sent successfully to your phone.',
            );
        } catch (OtpException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage()]);

            return $this->error('Registration failed. Please try again.', 500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $dto = VerifyOtpDTO::fromRequest($request->validated());
            $result = $this->authService->verifyOtp($dto);

            return $this->success(
                data: [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                    'token_type' => $result['token_type'],
                ],
                message: 'OTP verified successfully.',
            );
        } catch (OtpException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 400);
        } catch (\Exception $e) {
            Log::error('OTP verification failed', ['error' => $e->getMessage()]);

            return $this->error('OTP verification failed. Please try again.', 500);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $user = request()->user();
            $this->authService->logout($user);

            return $this->success(message: 'Logged out successfully.');
        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);

            return $this->error('Logout failed. Please try again.', 500);
        }
    }
}

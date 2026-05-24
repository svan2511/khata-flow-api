<?php

namespace App\Http\Controllers;

use App\DTOs\ProfileUpdateDTO;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserProfileResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function profile(): JsonResponse
    {
        try {
            $user = $this->userService->getProfile(request()->user());

            return $this->success(
                data: new UserProfileResource($user),
                message: 'Profile retrieved successfully.',
            );
        } catch (\Exception $e) {
            Log::error('Profile fetch failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to fetch profile.', 500);
        }
    }

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $dto = ProfileUpdateDTO::fromRequest($request->validated() + $request->allFiles());
            $user = $this->userService->updateProfile($request->user(), $dto);

            return $this->success(
                data: new UserProfileResource($user),
                message: 'Profile updated successfully.',
            );
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage()]);

            return $this->error('Failed to update profile.', 500);
        }
    }
}

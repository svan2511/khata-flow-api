<?php

namespace App\Services;

use App\DTOs\ProfileUpdateDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CloudinaryService $cloudinaryService,
        private readonly ShopService $shopService,
    ) {}

    public function getProfile(User $user): User
    {
        return $user->load('shop');
    }

    public function updateProfile(User $user, ProfileUpdateDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto): User {
            $userData = [];

            if ($dto->name !== null) {
                $userData['name'] = $dto->name;
            }
            if ($dto->email !== null) {
                $userData['email'] = $dto->email;
            }
            if ($dto->phone !== null) {
                $userData['phone'] = $dto->phone;
            }

            if ($dto->hasAvatar()) {
                if ($user->avatar) {
                    // Delete old avatar if it was on Cloudinary
                }
                $avatarData = $this->cloudinaryService->upload($dto->avatar, 'dukaan-sahayak/avatars');
                if ($avatarData) {
                    $userData['avatar'] = $avatarData['url'];
                }
            }

            if (! empty($userData)) {
                $this->userRepository->update($user, $userData);
            }

            if ($dto->hasShopUpdates() && $user->shop) {
                $shopData = [];
                if ($dto->shopName !== null) {
                    $shopData['shop_name'] = $dto->shopName;
                }
                if ($dto->ownerName !== null) {
                    $shopData['owner_name'] = $dto->ownerName;
                }
                if ($dto->address !== null) {
                    $shopData['address'] = $dto->address;
                }
                if ($dto->city !== null) {
                    $shopData['city'] = $dto->city;
                }
                if ($dto->state !== null) {
                    $shopData['state'] = $dto->state;
                }
                if ($dto->pincode !== null) {
                    $shopData['pincode'] = $dto->pincode;
                }
                if ($dto->gstin !== null) {
                    $shopData['gstin'] = $dto->gstin;
                }

                if ($dto->hasLogo()) {
                    if ($user->shop->cloudinary_public_id) {
                        $this->cloudinaryService->delete($user->shop->cloudinary_public_id);
                    }
                    $logoData = $this->cloudinaryService->upload($dto->logo, 'dukaan-sahayak/shops');
                    if ($logoData) {
                        $shopData['logo'] = $logoData['url'];
                        $shopData['cloudinary_public_id'] = $logoData['public_id'];
                    }
                }

                if (! empty($shopData)) {
                    $user->shop->update($shopData);
                }
            }

            return $user->fresh()->load('shop');
        });
    }
}

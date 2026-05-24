<?php

namespace App\Services;

use App\DTOs\ShopSetupDTO;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\Contracts\ShopRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopService
{
    public function __construct(
        private readonly ShopRepositoryInterface $shopRepository,
        private readonly CloudinaryService $cloudinaryService,
    ) {}

    public function setup(User $user, ShopSetupDTO $dto): Shop
    {
        return DB::transaction(function () use ($user, $dto): Shop {
            $logoData = null;
            if ($dto->hasLogo()) {
                $logoData = $this->cloudinaryService->upload($dto->logo, 'dukaan-sahayak/shops');
            }

            $shopData = $dto->toArray();
            $shopData['user_id'] = $user->id;
            $shopData['shop_slug'] = Str::slug($dto->shopName).'-'.Str::random(6);

            if ($logoData) {
                $shopData['logo'] = $logoData['url'];
                $shopData['cloudinary_public_id'] = $logoData['public_id'];
            }

            return $this->shopRepository->create($shopData);
        });
    }

    public function getShopByUser(User $user): ?Shop
    {
        return $this->shopRepository->findByUser($user);
    }

    public function updateShop(User $user, ShopSetupDTO $dto): Shop
    {
        return DB::transaction(function () use ($user, $dto): Shop {
            $shop = $this->shopRepository->findByUser($user);

            if (! $shop) {
                throw new \RuntimeException('Shop not found. Please setup your shop first.');
            }

            $shopData = $dto->toArray();
            $shopData = array_filter($shopData, fn ($value) => ! is_null($value));

            if ($dto->hasLogo()) {
                if ($shop->cloudinary_public_id) {
                    $this->cloudinaryService->delete($shop->cloudinary_public_id);
                }

                $logoData = $this->cloudinaryService->upload($dto->logo, 'dukaan-sahayak/shops');
                if ($logoData) {
                    $shopData['logo'] = $logoData['url'];
                    $shopData['cloudinary_public_id'] = $logoData['public_id'];
                }
            }

            return $this->shopRepository->update($shop, $shopData);
        });
    }

    public function isShopSetup(User $user): bool
    {
        return $this->shopRepository->findByUser($user) !== null;
    }
}

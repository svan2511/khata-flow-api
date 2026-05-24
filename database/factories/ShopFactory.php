<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'shop_name' => $name,
            'shop_slug' => Str::slug($name).'-'.Str::random(6),
            'owner_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'pincode' => fake()->postcode(),
            'gstin' => strtoupper(Str::random(15)),
            'is_active' => true,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'sku' => strtoupper(Str::random(8)),
            'barcode' => fake()->ean13(),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'cost_price' => fake()->randomFloat(2, 5, 800),
            'mrp' => fake()->randomFloat(2, 10, 1200),
            'unit' => fake()->randomElement(['piece', 'kg', 'g', 'liter', 'ml', 'dozen']),
            'stock_quantity' => fake()->randomFloat(2, 0, 500),
            'low_stock_threshold' => 10,
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->randomFloat(2, 0, 10),
            'low_stock_threshold' => 10,
        ]);
    }
}

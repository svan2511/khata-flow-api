<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    private function thresholdForUnit(string $unit): float
    {
        return match ($unit) {
            'kg' => fake()->randomFloat(2, 2, 10),
            'g' => fake()->randomFloat(2, 100, 500),
            'liter' => fake()->randomFloat(2, 1, 5),
            'ml' => fake()->randomFloat(2, 100, 500),
            'dozen' => fake()->randomFloat(2, 1, 5),
            default => fake()->randomFloat(2, 5, 20),
        };
    }

    public function definition(): array
    {
        $unit = fake()->randomElement(['piece', 'kg', 'g', 'liter', 'ml', 'dozen']);
        return [
            'name' => fake()->unique()->words(3, true),
            'slug' => Str::slug(fake()->unique()->words(3, true)),
            'sku' => strtoupper(Str::random(8)),
            'barcode' => fake()->ean13(),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'cost_price' => fake()->randomFloat(2, 5, 800),
            'mrp' => fake()->randomFloat(2, 10, 1200),
            'unit' => $unit,
            'stock_quantity' => fake()->randomFloat(2, 0, 500),
            'low_stock_threshold' => $this->thresholdForUnit($unit),
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => fake()->randomFloat(2, 0, 5),
            'low_stock_threshold' => $this->thresholdForUnit($attributes['unit'] ?? 'piece'),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '9'.fake()->numerify('##########'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'total_credit' => fake()->randomFloat(2, 0, 5000),
        ];
    }
}

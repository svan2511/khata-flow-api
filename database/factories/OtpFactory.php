<?php

namespace Database\Factories;

use App\Models\Otp;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtpFactory extends Factory
{
    protected $model = Otp::class;

    public function definition(): array
    {
        return [
            'phone' => '9999999999',
            'otp' => '123456',
            'purpose' => 'registration',
            'is_used' => false,
            'expires_at' => now()->addMinutes(10),
        ];
    }
}

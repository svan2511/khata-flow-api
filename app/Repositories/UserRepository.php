<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::where('uuid', $uuid)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function findOrCreateByPhone(string $phone): User
    {
        $user = $this->findByPhone($phone);

        if (! $user) {
            $user = $this->create([
                'uuid' => (string) Str::uuid(),
                'phone' => $phone,
            ]);
        }

        return $user;
    }

    public function markPhoneAsVerified(User $user): User
    {
        return $this->update($user, [
            'phone_verified_at' => now(),
        ]);
    }
}

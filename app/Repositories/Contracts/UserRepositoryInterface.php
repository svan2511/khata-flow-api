<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByUuid(string $uuid): ?User;

    public function findByPhone(string $phone): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function findOrCreateByPhone(string $phone): User;

    public function markPhoneAsVerified(User $user): User;
}

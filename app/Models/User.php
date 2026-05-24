<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'phone_verified_at',
        'email_verified_at',
        'password',
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function isPhoneVerified(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    public function hasShopSetup(): bool
    {
        return $this->shop()->exists();
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => preg_replace('/\D/', '', $value),
        );
    }
}

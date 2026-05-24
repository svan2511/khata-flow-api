<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case Registration = 'registration';
    case Login = 'login';
    case ProfileUpdate = 'profile_update';

    public function label(): string
    {
        return match ($this) {
            self::Registration => 'Registration',
            self::Login => 'Login',
            self::ProfileUpdate => 'Profile Update',
        };
    }
}

<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Upi = 'upi';
    case Card = 'card';
    case Mix = 'mix';
    case Credit = 'credit';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

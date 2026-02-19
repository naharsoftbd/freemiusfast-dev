<?php

namespace App\Enums;

enum RefundPolicy: string
{
    case FLEXIBLE = 'flexible';
    case MODERATE = 'moderate';
    case STRICT = 'strict';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

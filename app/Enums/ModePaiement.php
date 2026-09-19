<?php

namespace App\Enums;

enum ModePaiement: string
{
    case MtnMomo = 'MTN_MOMO';
    case OrangeMoney = 'ORANGE_MONEY';

    public function label(): string
    {
        return match ($this) {
            self::MtnMomo => 'MTN Mobile Money',
            self::OrangeMoney => 'Orange Money',
        };
    }

    public function prefixeReference(): string
    {
        return match ($this) {
            self::MtnMomo => 'MTN',
            self::OrangeMoney => 'OM',
        };
    }
}

<?php

namespace App\Enums;

enum OperateurMobileMoney: string
{
    case MtnMomo = 'mtn_momo';
    case OrangeMoney = 'orange_money';

    public function label(): string
    {
        return match ($this) {
            self::MtnMomo => 'MTN MoMo',
            self::OrangeMoney => 'Orange Money',
        };
    }

    /** Préfixes de numéros camerounais par opérateur */
    public function prefixes(): array
    {
        return match ($this) {
            // MTN Cameroun : 65x, 67x, 68x — Orange : 69x
            self::MtnMomo => ['65', '67', '68'],
            self::OrangeMoney => ['69'],
        };
    }
}

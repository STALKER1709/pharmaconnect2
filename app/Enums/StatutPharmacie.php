<?php

namespace App\Enums;

enum StatutPharmacie: string
{
    case Ouverte = 'ouverte';
    case Fermee = 'fermee';
    case DeGarde = 'de_garde';

    public function label(): string
    {
        return match ($this) {
            self::Ouverte => 'Ouverte',
            self::Fermee => 'Fermée',
            self::DeGarde => 'De garde',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ouverte => 'bg-emerald-100 text-emerald-800',
            self::Fermee => 'bg-red-100 text-red-800',
            self::DeGarde => 'bg-sky-100 text-sky-800',
        };
    }
}

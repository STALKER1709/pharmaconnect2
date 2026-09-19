<?php

namespace App\Enums;

enum TypeAvis: string
{
    case Service = 'SERVICE';
    case Medicament = 'MEDICAMENT';

    public function label(): string
    {
        return match ($this) {
            self::Service => 'Service de la pharmacie',
            self::Medicament => 'Médicament',
        };
    }
}

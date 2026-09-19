<?php

namespace App\Enums;

enum StatutCompte: string
{
    case Actif = 'actif';
    case EnAttente = 'en_attente';
    case Suspendu = 'suspendu';

    public function label(): string
    {
        return match ($this) {
            self::Actif => 'Actif',
            self::EnAttente => 'En attente de validation',
            self::Suspendu => 'Suspendu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Actif => 'bg-emerald-100 text-emerald-800',
            self::EnAttente => 'bg-amber-100 text-amber-800',
            self::Suspendu => 'bg-red-100 text-red-800',
        };
    }
}

<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case EnAttente = 'EN_ATTENTE';
    case Valide = 'VALIDE';
    case Echoue = 'ECHOUE';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Valide => 'Validé',
            self::Echoue => 'Échoué',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-amber-100 text-amber-800',
            self::Valide => 'bg-emerald-100 text-emerald-800',
            self::Echoue => 'bg-red-100 text-red-800',
        };
    }
}

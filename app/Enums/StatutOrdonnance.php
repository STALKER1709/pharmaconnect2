<?php

namespace App\Enums;

enum StatutOrdonnance: string
{
    case EnAttente = 'EN_ATTENTE';
    case Validee = 'VALIDEE';
    case Rejetee = 'REJETEE';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente de validation',
            self::Validee => 'Validée',
            self::Rejetee => 'Rejetée',
        };
    }
}

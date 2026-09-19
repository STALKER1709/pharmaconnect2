<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Client = 'client';
    case Pharmacie = 'pharmacie';
    case Livreur = 'livreur';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Client => 'Client',
            self::Pharmacie => 'Pharmacie',
            self::Livreur => 'Livreur',
        };
    }

    /** Préfixe des routes dédiées (/client, /pharmacie, /livreur, /admin). */
    public function prefix(): string
    {
        return $this->value;
    }
}

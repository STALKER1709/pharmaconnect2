<?php

namespace App\Enums;

enum UserRole: string
{
    case Client = 'client';
    case Pharmacie = 'pharmacie';
    case Livreur = 'livreur';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Pharmacie => 'Pharmacie',
            self::Livreur => 'Livreur',
            self::Admin => 'Administrateur',
        };
    }
}

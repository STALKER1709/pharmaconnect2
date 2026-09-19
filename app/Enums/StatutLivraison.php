<?php

namespace App\Enums;

enum StatutLivraison: string
{
    case EnAttente = 'EN_ATTENTE';
    case Acceptee = 'ACCEPTEE';
    case EnCours = 'EN_COURS';
    case Livree = 'LIVREE';
    case Echouee = 'ECHOUEE';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente de coursier',
            self::Acceptee => 'Acceptée',
            self::EnCours => 'En cours',
            self::Livree => 'Livrée',
            self::Echouee => 'Échouée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-slate-100 text-slate-800',
            self::Acceptee => 'bg-indigo-100 text-indigo-800',
            self::EnCours => 'bg-amber-100 text-amber-800',
            self::Livree => 'bg-emerald-100 text-emerald-800',
            self::Echouee => 'bg-red-100 text-red-800',
        };
    }
}

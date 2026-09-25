<?php

namespace App\Enums;

enum LivraisonStatut: string
{
    case Disponible = 'disponible';
    case Assignee = 'assignee';
    case Acceptee = 'acceptee';
    case EnRoute = 'en_route';
    case Arrivee = 'arrivee';
    case Livree = 'livree';
    case Echec = 'echec';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Assignee => 'Assignée',
            self::Acceptee => 'Acceptée',
            self::EnRoute => 'En route',
            self::Arrivee => 'Arrivé sur place',
            self::Livree => 'Livrée',
            self::Echec => 'Échec',
            self::Annulee => 'Annulée',
        };
    }

    /** Classes Tailwind de la pastille de statut. */
    public function couleur(): string
    {
        return match ($this) {
            self::Disponible => 'bg-amber-100 text-amber-800',
            self::Assignee, self::Acceptee => 'bg-violet-100 text-violet-800',
            self::EnRoute, self::Arrivee => 'bg-cyan-100 text-cyan-800',
            self::Livree => 'bg-emerald-100 text-emerald-800',
            self::Echec => 'bg-red-100 text-red-800',
            self::Annulee => 'bg-gray-100 text-gray-800',
        };
    }
}

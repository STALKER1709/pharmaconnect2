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
}

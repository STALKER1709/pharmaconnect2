<?php

namespace App\Enums;

enum CommandeStatut: string
{
    case EnAttente = 'en_attente';
    case Confirmee = 'confirmee';
    case Refusee = 'refusee';
    case Prete = 'prete';
    case Assignee = 'assignee';
    case EnLivraison = 'en_livraison';
    case Livree = 'livree';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente',
            self::Confirmee => 'Confirmée',
            self::Refusee => 'Refusée',
            self::Prete => 'Prête',
            self::Assignee => 'Assignée',
            self::EnLivraison => 'En livraison',
            self::Livree => 'Livrée',
            self::Annulee => 'Annulée',
        };
    }

    public function couleur(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-amber-100 text-amber-800',
            self::Confirmee => 'bg-sky-100 text-sky-800',
            self::Refusee => 'bg-red-100 text-red-800',
            self::Prete => 'bg-indigo-100 text-indigo-800',
            self::Assignee => 'bg-violet-100 text-violet-800',
            self::EnLivraison => 'bg-cyan-100 text-cyan-800',
            self::Livree => 'bg-emerald-100 text-emerald-800',
            self::Annulee => 'bg-gray-100 text-gray-800',
        };
    }

    /** Le client peut-il annuler ? */
    public function annulableParClient(): bool
    {
        return in_array($this, [self::EnAttente, self::Confirmee], true);
    }

    /** Transitions autorisées côté pharmacie */
    public static function transitionsPharmacie(): array
    {
        return [
            self::EnAttente->value => [self::Confirmee->value, self::Refusee->value],
            self::Confirmee->value => [self::Prete->value, self::Refusee->value],
            self::Prete->value => [self::Assignee->value],
        ];
    }
}

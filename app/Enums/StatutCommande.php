<?php

namespace App\Enums;

enum StatutCommande: string
{
    case EnAttente = 'EN_ATTENTE';
    case Payee = 'PAYEE';
    case Acceptee = 'ACCEPTEE';
    case Prete = 'PRETE';
    case EnLivraison = 'EN_LIVRAISON';
    case Livree = 'LIVREE';
    case Annulee = 'ANNULEE';

    public function label(): string
    {
        return match ($this) {
            self::EnAttente => 'En attente de paiement',
            self::Payee => 'Payée',
            self::Acceptee => 'Acceptée par la pharmacie',
            self::Prete => 'Prête pour livraison',
            self::EnLivraison => 'En livraison',
            self::Livree => 'Livrée',
            self::Annulee => 'Annulée',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EnAttente => 'bg-slate-100 text-slate-800',
            self::Payee => 'bg-sky-100 text-sky-800',
            self::Acceptee => 'bg-indigo-100 text-indigo-800',
            self::Prete => 'bg-violet-100 text-violet-800',
            self::EnLivraison => 'bg-amber-100 text-amber-800',
            self::Livree => 'bg-emerald-100 text-emerald-800',
            self::Annulee => 'bg-red-100 text-red-800',
        };
    }

    /** @return array<int, self> */
    public static function modifiablesParPharmacie(): array
    {
        return [self::Payee, self::Acceptee, self::Prete];
    }
}

<?php

namespace App\Policies;

use App\Models\Livraison;
use App\Models\User;

class LivraisonPolicy
{
    public function view(User $user, Livraison $livraison): bool
    {
        if ($user->estAdmin()) {
            return true;
        }
        if ($user->estLivreur()) {
            return $livraison->livreur_id === $user->livreur?->id || $livraison->livreur_id === null;
        }

        return $livraison->commande
            && $livraison->commande->client
            && $livraison->commande->client->user_id === $user->id;
    }

    public function accepter(User $user, Livraison $livraison): bool
    {
        return $user->estLivreur()
            && $user->livreur
            && in_array($livraison->statut, [\App\Enums\LivraisonStatut::Disponible, \App\Enums\LivraisonStatut::Assignee], true)
            && ($livraison->livreur_id === null || $livraison->livreur_id === $user->livreur->id);
    }

    public function demarrer(User $user, Livraison $livraison): bool
    {
        return $user->estLivreur()
            && $livraison->livreur_id === $user->livreur?->id
            && $livraison->statut === \App\Enums\LivraisonStatut::Acceptee;
    }

    public function marquerArrivee(User $user, Livraison $livraison): bool
    {
        return $user->estLivreur()
            && $livraison->livreur_id === $user->livreur?->id
            && $livraison->statut === \App\Enums\LivraisonStatut::EnRoute;
    }

    public function livrer(User $user, Livraison $livraison): bool
    {
        return $user->estLivreur()
            && $livraison->livreur_id === $user->livreur?->id
            && $livraison->statut === \App\Enums\LivraisonStatut::Arrivee;
    }

    public function signalerPosition(User $user, Livraison $livraison): bool
    {
        return $user->estLivreur()
            && $livraison->livreur_id === $user->livreur?->id
            && in_array($livraison->statut, [\App\Enums\LivraisonStatut::Acceptee, \App\Enums\LivraisonStatut::EnRoute, \App\Enums\LivraisonStatut::Arrivee], true);
    }
}

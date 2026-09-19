<?php

namespace App\Policies;

use App\Models\Commande;
use App\Models\User;

class CommandePolicy
{
    public function view(User $user, Commande $commande): bool
    {
        return $this->implique($user, $commande);
    }

    public function update(User $user, Commande $commande): bool
    {
        return $user->estPharmacie()
            && $commande->pharmacie
            && $commande->pharmacie->user_id === $user->id;
    }

    public function annuler(User $user, Commande $commande): bool
    {
        return $user->estClient()
            && $commande->client
            && $commande->client->user_id === $user->id
            && $commande->peutEtreAnnuleeParClient();
    }

    public function confirmerReception(User $user, Commande $commande): bool
    {
        return $user->estClient()
            && $commande->client
            && $commande->client->user_id === $user->id
            && $commande->statut === \App\Enums\CommandeStatut::EnLivraison;
    }

    public function laisserAvis(User $user, Commande $commande): bool
    {
        return $user->estClient()
            && $commande->client
            && $commande->client->user_id === $user->id
            && $commande->statut === \App\Enums\CommandeStatut::Livree;
    }

    public function payer(User $user, Commande $commande): bool
    {
        return $user->estClient()
            && $commande->client
            && $commande->client->user_id === $user->id
            && ! $commande->paiement;
    }

    public function chatter(User $user, Commande $commande): bool
    {
        return $this->implique($user, $commande);
    }

    protected function implique(User $user, Commande $commande): bool
    {
        if ($user->estAdmin()) {
            return true;
        }

        if ($user->estClient()) {
            return $commande->client && $commande->client->user_id === $user->id;
        }

        if ($user->estPharmacie()) {
            return $commande->pharmacie && $commande->pharmacie->user_id === $user->id;
        }

        if ($user->estLivreur()) {
            return $commande->livreur && $commande->livreur->user_id === $user->id;
        }

        return false;
    }
}

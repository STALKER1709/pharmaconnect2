<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Canaux de diffusion (Laravel Reverb)
|--------------------------------------------------------------------------
*/

// Suivi temps réel d'une commande (client, pharmacie, livreur impliqués)
Broadcast::channel('commande.{commandeId}', function ($user, $commandeId) {
    $commande = \App\Models\Commande::find($commandeId);

    if (! $commande) {
        return false;
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

    return $user->estAdmin();
});

// Messagerie : conversation de présence entre participants
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);

    return $conversation && $conversation->implique($user->id) ? ['id' => $user->id, 'name' => $user->name] : false;
});

// Notifications personnelles
Broadcast::channel('client.{clientId}', fn ($user, $id) => $user->estClient() && $user->client?->id == $id);
Broadcast::channel('pharmacie.{pharmacieId}', fn ($user, $id) => $user->estPharmacie() && $user->pharmacie?->id == $id);
Broadcast::channel('livreur.{livreurId}', fn ($user, $id) => $user->estLivreur() && $user->livreur?->id == $id);

// File des livraisons disponibles (tous les livreurs actifs)
Broadcast::channel('livraisons-disponibles', fn ($user) => $user->estLivreur() && $user->statut === 'actif');

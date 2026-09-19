<?php

namespace App\Listeners;

use App\Events\CommandeStatutChange;
use App\Notifications\CommandeConfirmee;
use App\Notifications\CommandeLivree;
use App\Notifications\CommandePrete;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifierCommandeStatut implements ShouldQueue
{
    public function handle(CommandeStatutChange $event): void
    {
        $commande = $event->commande->withoutRelations();
        $clientUser = $commande->client?->user;

        if (! $clientUser) {
            return;
        }

        $notification = match ($commande->statut) {
            \App\Enums\CommandeStatut::Confirmee => new CommandeConfirmee($commande),
            \App\Enums\CommandeStatut::Prete => new CommandePrete($commande),
            \App\Enums\CommandeStatut::Livree => new CommandeLivree($commande),
            default => null,
        };

        if ($notification) {
            $clientUser->notify($notification);
        }
    }
}

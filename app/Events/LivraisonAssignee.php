<?php

namespace App\Events;

use App\Models\Livraison;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LivraisonAssignee implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Livraison $livraison)
    {
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        if ($this->livraison->livreur_id) {
            return [new PrivateChannel('livreur.'.$this->livraison->livreur_id)];
        }

        return [new PrivateChannel('livraisons-disponibles')];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->livraison->id,
            'commande_numero' => $this->livraison->commande?->numero,
            'statut' => $this->livraison->statut->value,
            'adresse' => $this->livraison->commande?->adresse_livraison,
        ];
    }

    public function broadcastAs(): string
    {
        return 'livraison.assignee';
    }
}

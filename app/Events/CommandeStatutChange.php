<?php

namespace App\Events;

use App\Models\Commande;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommandeStatutChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Commande $commande, public string $ancienStatut)
    {
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        $canaux = [
            new PrivateChannel('commande.'.$this->commande->id),
            new PrivateChannel('pharmacie.'.$this->commande->pharmacie_id),
            new PrivateChannel('client.'.$this->commande->client_id),
        ];

        if ($this->commande->livreur_id) {
            $canaux[] = new PrivateChannel('livreur.'.$this->commande->livreur_id);
        }

        return $canaux;
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->commande->id,
            'numero' => $this->commande->numero,
            'statut' => $this->commande->statut->value,
            'statut_label' => $this->commande->statut->label(),
            'ancien_statut' => $this->ancienStatut,
        ];
    }

    public function broadcastAs(): string
    {
        return 'commande.statut';
    }
}

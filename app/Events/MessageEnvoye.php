<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Un message vient d'être envoyé : diffusé sur le canal privé du
 * destinataire pour l'affichage temps réel de la conversation.
 */
class MessageEnvoye implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $expediteurId,
        public int $destinataireId,
        public string $contenu,
        public string $nomExpediteur,
    ) {}

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('messagerie.'.$this->destinataireId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'expediteur_id' => $this->expediteurId,
            'contenu' => $this->contenu,
            'nom_expediteur' => $this->nomExpediteur,
            'horodatage' => now()->format('H:i'),
        ];
    }
}

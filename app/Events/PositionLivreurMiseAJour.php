<?php

namespace App\Events;

use App\Models\PositionLivreur;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PositionLivreurMiseAJour implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PositionLivreur $position)
    {
    }

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        // Canal privé de suivi : commande accessible au client et à la pharmacie
        return [
            new PrivateChannel('commande.'.$this->position->livraison?->commande?->id),
        ];
    }

    public function broadcastWhen(): bool
    {
        return $this->position->livraison !== null && $this->position->livraison->commande_id !== null;
    }

    public function broadcastWith(): array
    {
        return [
            'livraison_id' => $this->position->livraison_id,
            'latitude' => (float) $this->position->latitude,
            'longitude' => (float) $this->position->longitude,
            'signalee_at' => $this->position->signalee_at?->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'position.mise-a-jour';
    }
}

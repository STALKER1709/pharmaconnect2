<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class CommandePrete extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(public Commande $commande)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->donnees();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->donnees());
    }

    protected function donnees(): array
    {
        return [
            'titre' => 'Commande prête 📦',
            'message' => "Votre commande {$this->commande->numero} est prête, un livreur va la prendre en charge.",
            'url' => route('commandes.show', $this->commande),
            'commande_id' => $this->commande->id,
        ];
    }
}

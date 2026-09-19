<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandeConfirmee extends Notification implements ShouldQueue, ShouldBroadcast
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
            'titre' => 'Commande confirmée ✅',
            'message' => "Votre commande {$this->commande->numero} a été confirmée par la pharmacie {$this->commande->pharmacie?->nom}.",
            'url' => route('commandes.show', $this->commande),
            'commande_id' => $this->commande->id,
        ];
    }
}

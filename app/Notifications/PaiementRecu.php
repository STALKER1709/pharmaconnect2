<?php

namespace App\Notifications;

use App\Models\Paiement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaiementRecu extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Paiement $paiement)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titre' => 'Paiement reçu 💰',
            'message' => "Paiement de {$this->paiement->montantFormate()} reçu pour la commande {$this->paiement->commande?->numero} ({$this->paiement->operateur->label()}).",
            'url' => route('pharmacie.paiements'),
            'paiement_id' => $this->paiement->id,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Paiement reçu — PharmaConnect')
            ->greeting('Bonjour,')
            ->line("Un paiement de {$this->paiement->montantFormate()} a été confirmé.")
            ->line("Commande : {$this->paiement->commande?->numero}")
            ->line('Opérateur : '.$this->paiement->operateur->label())
            ->action('Voir la commande', route('pharmacie.commandes.show', $this->paiement->commande));
    }
}

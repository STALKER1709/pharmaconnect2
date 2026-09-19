<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification générique PharmaConnect : titre, message, lien.
 * Canaux : database (cloche) + broadcast (temps réel via Reverb/Echo).
 */
class NotificationGenerique extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $titre,
        public string $message,
        public ?string $url = null,
        public string $niveau = 'info',
    ) {}

    /** Canaux stockés dans la colonne via (database + broadcast). */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titre' => $this->titre,
            'message' => $this->message,
            'url' => $this->url,
            'niveau' => $this->niveau,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[PharmaConnect] '.$this->titre)
            ->greeting('Bonjour,')
            ->line($this->message);

        if ($this->url !== null) {
            $mail->action('Voir dans l\'application', $this->url);
        }

        return $mail->line('Merci d\'utiliser PharmaConnect.');
    }
}

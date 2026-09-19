<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompteValide extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $role)
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
            'titre' => 'Compte validé ✅',
            'message' => "Votre compte {$this->role} a été validé par l'administrateur. Bienvenue sur PharmaConnect !",
            'url' => route('dashboard'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre compte PharmaConnect est validé')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Votre compte {$this->role} a été validé. Vous pouvez dès à présent utiliser toutes les fonctionnalités.")
            ->action('Accéder à PharmaConnect', route('dashboard'));
    }
}

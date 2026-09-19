<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MessageRecu extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(public Message $message)
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
            'titre' => 'Nouveau message 💬',
            'message' => \Illuminate\Support\Str::limit($this->message->contenu, 60),
            'url' => route('messagerie.index', ['conversation' => $this->message->conversation_id]),
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}

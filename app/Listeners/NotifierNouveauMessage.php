<?php

namespace App\Listeners;

use App\Events\NouveauMessage;
use App\Models\User;
use App\Notifications\MessageRecu;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifierNouveauMessage implements ShouldQueue
{
    public function handle(NouveauMessage $event): void
    {
        $conversation = $event->message->conversation;

        foreach ($conversation->participants() as $participantId) {
            if ($participantId === $event->message->expediteur_id) {
                continue;
            }

            $user = User::find($participantId);
            $user?->notify(new MessageRecu($event->message));
        }
    }
}

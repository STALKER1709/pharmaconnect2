<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
{
    /** Un utilisateur ne peut converser qu'avec les autres utilisateurs de la plateforme. */
    public function converserAvec(User $user, User $destinataire): Response
    {
        if ($user->id === $destinataire->id) {
            return Response::deny('Vous ne pouvez pas vous écrire à vous-même.');
        }

        return Response::allow();
    }

    /** Lecture d'un message : expediteur ou destinataire. */
    public function view(User $user, Message $message): bool
    {
        return $message->expediteur_id === $user->id || $message->destinataire_id === $user->id;
    }
}

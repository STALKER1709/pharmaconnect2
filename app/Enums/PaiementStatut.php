<?php

namespace App\Enums;

enum PaiementStatut: string
{
    case Initie = 'initie';
    case InitieInvalide = 'initie_invalide';
    case EnAttenteConfirmation = 'en_attente_confirmation';
    case Reussi = 'reussi';
    case Echoue = 'echoue';

    public function label(): string
    {
        return match ($this) {
            self::Initie => 'Initialisé',
            self::InitieInvalide => 'Numéro invalide',
            self::EnAttenteConfirmation => 'En attente de confirmation',
            self::Reussi => 'Réussi',
            self::Echoue => 'Échoué',
        };
    }
}

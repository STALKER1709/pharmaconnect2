<?php

namespace App\Contracts;

/**
 * Passerelle SMS (notifications, alertes de livraison).
 * Implémentation mock par défaut : MockSmsGateway.
 */
interface SmsGateway
{
    /**
     * Envoie un SMS.
     *
     * @param  string  $numero  Numéro destinataire (format camerounais 6xxxxxxxx)
     * @param  string  $message  Corps du SMS
     * @return bool Succès de l'envoi
     */
    public function envoyer(string $numero, string $message): bool;
}

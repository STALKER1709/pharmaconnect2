<?php

namespace App\Contracts;

/**
 * Passerelle téléphonique pour les appels entre acteurs.
 * Implémentation par défaut : liens tel: (TelephonyGatewayTel).
 */
interface TelephonyGateway
{
    /**
     * Génère le moyen d'appeler un utilisateur.
     *
     * @return array{type: string, cible: string} ex. ['type' => 'tel', 'cible' => '+237670000000']
     */
    public function appel(string $numero): array;
}

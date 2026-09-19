<?php

namespace App\Services;

use App\Contracts\TelephonyGateway;

/**
 * Téléphonie par défaut : liens tel: cliquables (aucune dépendance).
 */
class TelephonyGatewayTel implements TelephonyGateway
{
    public function appel(string $numero): array
    {
        $digits = preg_replace('/[^\d+]/', '', $numero);

        return ['type' => 'tel', 'cible' => $digits];
    }
}

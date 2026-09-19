<?php

namespace App\Services\Mock;

use App\Contracts\TelephonyGateway;

/**
 * Mock téléphonie : renvoie des liens tel: ouvrant l'appelant natif
 * du téléphone (aucun service externe requis en local).
 */
class MockTelephonyGateway implements TelephonyGateway
{
    public function appelVers(string $telephone): array
    {
        $numero = preg_replace('/[^0-9+]/', '', $telephone) ?: '';

        return [
            'url' => 'tel:'.$numero,
            'libelle' => 'Appeler '.$numero,
            'temps_reel' => false,
        ];
    }
}

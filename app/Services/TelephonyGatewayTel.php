<?php

namespace App\Services;

use App\Contracts\TelephonyGateway;

/**
 * Passerelle téléphonique MOCK (100 % locale) : aucun appel réseau,
 * renvoie un lien tel: que l'interface transforme en bouton d'appel.
 * Une vraie implémentation (API de téléphonie) se brancherait ici.
 */
class TelephonyGatewayTel implements TelephonyGateway
{
    public function appel(string $numero): array
    {
        $cible = '+237'.preg_replace('/\D/', '', $numero);

        return [
            'type' => 'tel',
            'cible' => $cible,
            'label' => 'Appeler le '.$cible,
        ];
    }
}

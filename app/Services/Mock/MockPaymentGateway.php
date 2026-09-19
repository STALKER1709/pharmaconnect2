<?php

namespace App\Services\Mock;

use App\Contracts\PaymentGateway;
use App\Enums\ModePaiement;
use Illuminate\Support\Str;

/**
 * Implémentation mock de la passerelle de paiement Mobile Money.
 * Aucun fournisseur réel n'est codé en dur : la simulation se fait via
 * une page de simulation (boutons « Payer » / « Échec ») et un webhook simulé.
 * Pour brancher MTN MoMo / Orange Money réels : créer une nouvelle
 * implémentation de PaymentGateway et changer PAYMENT_GATEWAY dans .env.
 */
class MockPaymentGateway implements PaymentGateway
{
    public function initierCollecte(int $montant, string $telephone, ModePaiement $mode, string $description): array
    {
        $reference = $mode->prefixeReference().'-'.strtoupper(Str::random(10));

        return [
            'reference' => $reference,
            'url_simulation' => route('paiement.simulation', ['reference' => $reference]),
        ];
    }

    public function verifierTransaction(string $reference): array
    {
        // Le mock délègue l'état réel à la table paiements (voir PaimentService) ;
        // ici on renvoie simplement « EN_ATTENTE » tant que la page de
        // simulation n'a pas confirmé le paiement.
        return ['statut' => 'EN_ATTENTE', 'reference' => $reference];
    }
}

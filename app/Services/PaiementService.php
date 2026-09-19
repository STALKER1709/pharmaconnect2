<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\ModePaiement;
use App\Enums\StatutCommande;
use App\Enums\StatutPaiement;
use App\Models\Commande;
use App\Models\Paiement;
use App\Notifications\NotificationGenerique;

/**
 * Cas d'utilisation « Effectuer un paiement » — spécialisations
 * « via MTN MoMo » / « via Orange Money » (ModePaiement).
 * Passe par le contrat PaymentGateway (mock par défaut).
 */
class PaiementService
{
    public function __construct(private PaymentGateway $passerelle) {}

    /**
     * Initie un paiement Mobile Money pour une commande.
     *
     * @return array{paiement: Paiement, url_simulation: string}
     */
    public function initier(Commande $commande, ModePaiement $mode, string $telephone): array
    {
        if ($commande->paiement?->statut === StatutPaiement::Valide) {
            abort(422, 'Cette commande est déjà payée.');
        }

        $collecte = $this->passerelle->initierCollecte(
            $commande->montantAvecLivraison(),
            $telephone,
            $mode,
            "PharmaConnect — commande #{$commande->id}",
        );

        $paiement = Paiement::query()->updateOrCreate(
            ['commande_id' => $commande->id],
            [
                'montant' => $commande->montantAvecLivraison(),
                'mode' => $mode,
                'statut' => StatutPaiement::EnAttente,
                'reference_transaction' => $collecte['reference'],
            ],
        );

        return ['paiement' => $paiement, 'url_simulation' => $collecte['url_simulation']];
    }

    /**
     * Confirme un paiement (page de simulation locale ou webhook opérateur).
     * Passe la commande en PAYEE et notifie la pharmacie.
     */
    public function confirmer(Paiement $paiement, bool $succes = true): Paiement
    {
        if ($paiement->statut === StatutPaiement::Valide) {
            return $paiement;
        }

        $commande = $paiement->commande;

        if ($succes) {
            $paiement->update(['statut' => StatutPaiement::Valide]);
            $commande->update(['statut' => StatutCommande::Payee]);

            $commande->pharmacie->user?->notify(new NotificationGenerique(
                'Paiement reçu',
                "Paiement de {$paiement->montantFormate()} reçu pour la commande #{$commande->id}.",
                route('pharmacie.commandes.show', $commande),
                'success',
            ));

            $commande->client->user?->notify(new NotificationGenerique(
                'Paiement validé',
                "Votre paiement de {$paiement->montantFormate()} pour la commande #{$commande->id} est confirmé.",
                route('client.commandes.show', $commande),
                'success',
            ));
        } else {
            $paiement->update(['statut' => StatutPaiement::Echoue]);
        }

        return $paiement;
    }
}

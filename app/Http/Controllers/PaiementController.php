<?php

namespace App\Http\Controllers;

use App\Enums\CommandeStatut;
use App\Enums\OperateurMobileMoney;
use App\Enums\PaiementStatut;
use App\Models\Commande;
use App\Models\PharmacieMedicament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Paiement Mobile Money d'une commande (MTN MoMo / Orange Money).
 * Le paiement est INCLUS à la commande (CheckoutController) ; ce
 * contrôleur gère la nouvelle tentative après un échec.
 */
class PaiementController extends Controller
{
    /**
     * Le paiement fait partie du tunnel de commande : le formulaire
     * (opérateur + numéro) vit directement sur la page de la commande.
     */
    public function create(Commande $commande): RedirectResponse
    {
        $this->authorize('view', $commande);

        return redirect()->route('commandes.show', $commande);
    }

    /** Réinitialise le paiement d'une commande annulée après échec. */
    public function store(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('payer', $commande);

        $validated = $request->validate([
            'operateur' => ['required', 'in:mtn_momo,orange_money'],
            'numero_mobile_money' => ['required', 'string', 'regex:/^(237)?6\d{8}$/'],
        ], [
            'numero_mobile_money.regex' => 'Numéro Mobile Money invalide (ex. 690123456).',
        ]);

        $operateur = OperateurMobileMoney::from($validated['operateur']);
        $reference = 'PAY-'.strtoupper(bin2hex(random_bytes(6)));

        $resultat = app(\App\Contracts\PaymentGateway::class)->payer(
            $validated['numero_mobile_money'],
            $commande->total,
            $operateur,
            $reference
        );

        $commande->paiement()->updateOrCreate([], [
            'reference' => $reference,
            'operateur' => $operateur,
            'montant' => $commande->total,
            'statut' => $resultat['succes'] ? PaiementStatut::Reussi : PaiementStatut::Echoue,
            'numero_payeur' => $validated['numero_mobile_money'],
            'numero_paye' => $resultat['details']['numero_paye'] ?? null,
            'reponse_brute' => json_encode($resultat),
            'paye_at' => $resultat['succes'] ? now() : null,
        ]);

        if ($resultat['succes']) {
            // Le stock avait été restitué après l'échec initial : on le re-décrémente,
            // avec garde anti-survente (le stock a pu évoluer entre-temps)
            DB::transaction(function () use ($commande) {
                foreach ($commande->lignes as $ligne) {
                    $decremente = PharmacieMedicament::query()
                        ->where('pharmacie_id', $commande->pharmacie_id)
                        ->where('medicament_id', $ligne->medicament_id)
                        ->where('quantite', '>=', $ligne->quantite)
                        ->decrement('quantite', $ligne->quantite);

                    if ($decremente === 0) {
                        // Paiement accepté mais stock parti : on garde la commande,
                        // la pharmacie s'approvisionne (aucune perte pour le client).
                        break;
                    }
                }
            });

            $commande->update(['statut' => CommandeStatut::EnAttente, 'annulee_at' => null]);

            return redirect()->route('commandes.show', $commande)
                ->with('succes', 'Paiement accepté ✓ Votre commande repart dans le circuit de préparation.');
        }

        return back()->with('erreur', $resultat['message']);
    }

    /** Statut du paiement (polling JSON). */
    public function statut(Commande $commande): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $commande);

        return response()->json([
            'statut' => $commande->paiement?->statut->value,
            'label' => $commande->paiement?->statut->label(),
        ]);
    }
}

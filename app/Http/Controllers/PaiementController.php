<?php

namespace App\Http\Controllers;

use App\Enums\OperateurMobileMoney;
use App\Enums\PaiementStatut;
use App\Models\Commande;
use App\Models\Paiement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaiementController extends Controller
{
    public function create(Commande $commande): View
    {
        $this->authorize('payer', $commande);

        return view('paiement.create', [
            'commande' => $commande->load('pharmacie', 'lignes'),
            'operateurs' => OperateurMobileMoney::cases(),
        ]);
    }

    /** Réinitialise le paiement d'une commande annulée/échouée. */
    public function store(Request $request, Commande $commande): RedirectResponse
    {
        $this->authorize('payer', $commande);

        $validated = $request->validate([
            'operateur' => ['required', 'in:mtn_momo,orange_money'],
            'numero_mobile_money' => ['required', 'string', 'regex:/^(237)?6\d{8}$/'],
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
            $commande->update(['statut' => CommandeStatut::EnAttente, 'annulee_at' => null]);

            return redirect()->route('commandes.show', $commande)->with('succes', 'Paiement accepté ✓');
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

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Enums\ModePaiement;
use App\Models\Commande;
use App\Models\Paiement;
use App\Services\PaiementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PaiementController extends Controller
{
    /** Initie la collecte MTN MoMo ou Orange Money. */
    public function initier(Request $request, Commande $commande, PaiementService $service)
    {
        Gate::authorize('view', $commande);

        $donnees = $request->validate([
            'mode' => ['required', 'in:'.implode(',', array_column(ModePaiement::cases(), 'value'))],
            'telephone' => ['required', 'string', 'max:30', 'regex:/^[+0-9 ().-]{8,}$/'],
        ]);

        $resultat = $service->initier(
            $commande,
            ModePaiement::from($donnees['mode']),
            $donnees['telephone'],
        );

        return redirect()->to($resultat['url_simulation'])
            ->with('succes', 'Collecte initiée : '.($resultat['paiement']->reference_transaction ?? ''));
    }

    /** Page de simulation de l'opérateur (mock) — remplace l'app opérateur réel. */
    public function simulation(string $reference)
    {
        $paiement = Paiement::query()
            ->where('reference_transaction', $reference)
            ->with(['commande.pharmacie', 'commande.client'])
            ->firstOrFail();

        $client = Auth::user()?->client;
        abort_unless($client !== null && $paiement->commande->client_id === $client->id, 403);

        return view('client.paiement-simulation', ['paiement' => $paiement]);
    }

    /** Confirme / échoue le paiement depuis la page de simulation (webhook mock). */
    public function confirmer(Request $request, string $reference, PaiementService $service)
    {
        $paiement = Paiement::query()
            ->where('reference_transaction', $reference)
            ->with(['commande.pharmacie', 'commande.client'])
            ->firstOrFail();

        $client = Auth::user()?->client;
        abort_unless($client !== null && $paiement->commande->client_id === $client->id, 403);

        $succes = $request->boolean('succes', true);
        $service->confirmer($paiement, $succes);

        if ($succes) {
            return redirect()
                ->route('client.commandes.show', $paiement->commande)
                ->with('succes', 'Paiement validé ! Votre commande est transmise à la pharmacie.');
        }

        return redirect()
            ->route('client.commandes.show', $paiement->commande)
            ->with('erreur', 'Le paiement a échoué. Vous pouvez réessayer.');
    }
}

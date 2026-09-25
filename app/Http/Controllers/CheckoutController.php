<?php

namespace App\Http\Controllers;

use App\Enums\CommandeStatut;
use App\Enums\LivraisonStatut;
use App\Enums\OperateurMobileMoney;
use App\Enums\PaiementStatut;
use App\Events\CommandeStatutChange;
use App\Models\Commande;
use App\Models\Pharmacie;
use App\Models\PharmacieMedicament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Passer commande = créer la commande + effectuer le paiement (INCLUS).
 * Étendue : partager la position (adresse via géolocalisation navigateur ou pin carte).
 */
class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);

        if (empty($panier['items'])) {
            return redirect()->route('panier.index')->with('erreur', 'Votre panier est vide.');
        }

        $pharmacie = Pharmacie::findOrFail($panier['pharmacie_id']);
        $client = $request->user()->client;

        return view('panier.checkout', [
            'pharmacie' => $pharmacie,
            'lignes' => $this->lignes($panier),
            'sousTotal' => $this->sousTotal($panier),
            'fraisLivraison' => $pharmacie->frais_livraison,
            'client' => $client,
        ]);
    }

    /** @throws \Throwable */
    public function store(Request $request): RedirectResponse
    {
        $panier = $request->session()->get('panier', ['pharmacie_id' => null, 'items' => []]);

        if (empty($panier['items'])) {
            return redirect()->route('panier.index');
        }

        // « 677 45 88 12 » -> « 677458812 » (saisie avec espaces comme sur la maquette)
        $request->merge(['numero_mobile_money' => preg_replace('/\D/', '', (string) $request->input('numero_mobile_money'))]);

        $validated = $request->validate([
            'adresse_livraison' => ['required', 'string', 'max:255'],
            'ville_livraison' => ['required', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'destinataire' => ['nullable', 'string', 'max:120'],
            'telephone_contact' => ['nullable', 'string', 'max:30'],
            'operateur' => ['required', 'in:mtn_momo,orange_money'],
            'numero_mobile_money' => ['required', 'string', 'regex:/^(237)?6\d{8}$/'],
        ], [
            'numero_mobile_money.regex' => 'Numéro Mobile Money invalide (ex. 690123456).',
        ]);

        $pharmacie = Pharmacie::findOrFail($panier['pharmacie_id']);
        $client = $request->user()->client;
        $operateur = OperateurMobileMoney::from($validated['operateur']);

        // Destinataire et téléphone du coursier (formulaire de livraison) conservés dans les notes
        $contact = collect([
            filled($validated['destinataire'] ?? null) ? 'Destinataire : '.$validated['destinataire'] : null,
            filled($validated['telephone_contact'] ?? null) ? 'Tél. : +237 '.$validated['telephone_contact'] : null,
        ])->filter()->implode(' — ');
        $validated['notes'] = collect([$contact, $validated['notes'] ?? null])->filter()->implode("\n") ?: null;

        $commande = DB::transaction(function () use ($panier, $pharmacie, $client, $validated, $operateur) {
            $items = collect($panier['items']);

            // Verrouillage pessimiste du stock pour éviter la survente
            $stocks = PharmacieMedicament::query()
                ->where('pharmacie_id', $pharmacie->id)
                ->whereIn('id', $items->pluck('stock_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $sousTotal = 0;
            $lignesCommande = [];

            foreach ($items as $item) {
                $stock = $stocks->get($item['stock_id']);

                if (! $stock || ! $stock->estEnStock() || $stock->quantite < $item['quantite']) {
                    throw ValidationException::withMessages([
                        'panier' => 'Stock insuffisant pour un article du panier. Veuillez actualiser votre panier.',
                    ]);
                }

                if ($stock->medicament->ordonnance_obligatoire) {
                    throw ValidationException::withMessages([
                        'panier' => "Le médicament « {$stock->medicament->nom} » nécessite une ordonnance. Contactez la pharmacie via la messagerie.",
                    ]);
                }

                $sousTotal += $stock->prix * $item['quantite'];
                $lignesCommande[] = [
                    'medicament_id' => $stock->medicament_id,
                    'pharmacie_id' => $pharmacie->id,
                    'nom_medicament' => $stock->medicament->nom,
                    'prix_unitaire' => $stock->prix,
                    'quantite' => $item['quantite'],
                    'sous_total' => $stock->prix * $item['quantite'],
                ];
            }

            $total = $sousTotal + $pharmacie->frais_livraison;

            /** @var Commande $commande */
            $commande = Commande::create([
                'client_id' => $client->id,
                'pharmacie_id' => $pharmacie->id,
                'statut' => CommandeStatut::EnAttente,
                'sous_total' => $sousTotal,
                'frais_livraison' => $pharmacie->frais_livraison,
                'total' => $total,
                'adresse_livraison' => $validated['adresse_livraison'],
                'ville_livraison' => $validated['ville_livraison'],
                'latitude_livraison' => $validated['latitude'] ?? $client?->latitude,
                'longitude_livraison' => $validated['longitude'] ?? $client?->longitude,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lignesCommande as $ligne) {
                $commande->lignes()->create($ligne);
            }

            // Décrémenter le stock
            foreach ($items as $item) {
                $stocks->get($item['stock_id'])?->decrement('quantite', $item['quantite']);
            }

            // ─── Paiement Mobile Money (INCLUS dans la commande) ───
            $reference = 'PAY-'.strtoupper(bin2hex(random_bytes(6)));
            $resultat = app(\App\Contracts\PaymentGateway::class)->payer(
                $validated['numero_mobile_money'],
                $total,
                $operateur,
                $reference
            );

            $commande->paiement()->create([
                'reference' => $reference,
                'operateur' => $operateur,
                'montant' => $total,
                'statut' => $resultat['succes'] ? PaiementStatut::Reussi : PaiementStatut::Echoue,
                'numero_payeur' => $validated['numero_mobile_money'],
                'numero_paye' => $resultat['details']['numero_paye'] ?? config('services.payment.'.($operateur === OperateurMobileMoney::MtnMomo ? 'momo_payee' : 'orange_payee')),
                'reponse_brute' => json_encode($resultat),
                'paye_at' => $resultat['succes'] ? now() : null,
            ]);

            // Livraison créée dès la commande (mise à disposition des livreurs)
            $commande->livraison()->create([
                'statut' => LivraisonStatut::Disponible,
                'latitude_arrivee' => $validated['latitude'] ?? $client?->latitude,
                'longitude_arrivee' => $validated['longitude'] ?? $client?->longitude,
            ]);

            if (! $resultat['succes']) {
                $commande->update(['statut' => CommandeStatut::Annulee, 'annulee_at' => now()]);

                // Remettre le stock en place
                foreach ($items as $item) {
                    $stocks->get($item['stock_id'])?->increment('quantite', $item['quantite']);
                }
            }

            return $commande;
        });

        $request->session()->forget('panier');

        if ($commande->statut === CommandeStatut::Annulee) {
            return redirect()->route('commandes.show', $commande)
                ->with('erreur', 'Le paiement a échoué ('.$commande->paiement->statut->label().'). La commande a été annulée.');
        }

        event(new CommandeStatutChange($commande, 'nouvelle'));

        return redirect()->route('commandes.show', $commande)
            ->with('succes', 'Commande créée et payée ✓ Suivez votre livraison en temps réel.');
    }

    /** Lignes du panier avec le stock chargé. */
    protected function lignes(array $panier)
    {
        return collect($panier['items'] ?? [])->map(function ($item) {
            $stock = PharmacieMedicament::with(['medicament', 'pharmacie'])->find($item['stock_id']);

            return $stock
                ? ['stock' => $stock, 'quantite' => $item['quantite'], 'sous_total' => $stock->prix * $item['quantite']]
                : null;
        })->filter()->values();
    }

    protected function sousTotal(array $panier): int
    {
        return (int) collect($panier['items'] ?? [])->sum(function ($item) {
            $stock = PharmacieMedicament::find($item['stock_id']);

            return $stock ? $stock->prix * $item['quantite'] : 0;
        });
    }
}

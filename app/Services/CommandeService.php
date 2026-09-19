<?php

namespace App\Services;

use App\Enums\StatutCommande;
use App\Enums\StatutLivraison;
use App\Enums\StatutOrdonnance;
use App\Models\Client;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Livraison;
use App\Models\Medicament;
use App\Models\Parametre;
use App\Models\Pharmacie;
use App\Notifications\NotificationGenerique;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Cas d'utilisation « Passer commande », « Confirmer la réception »,
 * « Annuler une commande ». Le paiement est inclus dans le cas d'utilisation
 * (voir PaiementService) et le suivi / la confirmation l'étendent.
 */
class CommandeService
{
    /**
     * Transforme le panier du client en commandes (une par pharmacie).
     *
     * @param  array{adresse: string, latitude: ?float, longitude: ?float, ordonnance_path: ?string}  $donnees
     * @return Collection<int, Commande> Les commandes créées.
     */
    public function passerDepuisPanier(Client $client, array $donnees): Collection
    {
        $panier = $client->ouPanier();
        $lignes = $panier->lignes()->with(['medicament.pharmacie.user'])->get();

        if ($lignes->isEmpty()) {
            abort(422, 'Votre panier est vide.');
        }

        $fraisLivraison = Parametre::getInt(Parametre::FRAIS_LIVRAISON, 1000);

        $commandes = DB::transaction(function () use ($client, $lignes, $donnees, $fraisLivraison) {
            $commandes = collect();

            foreach ($lignes->groupBy(fn ($ligne) => $ligne->medicament->pharmacie_id) as $pharmacieId => $lignesPharmacie) {
                /** @var Pharmacie $pharmacie */
                $pharmacie = $lignesPharmacie->first()->medicament->pharmacie;

                // Contient un médicament sur ordonnance ?
                $surOrdonnance = $lignesPharmacie->contains(fn ($ligne) => $ligne->medicament->sur_ordonnance);

                $commande = Commande::create([
                    'client_id' => $client->id,
                    'pharmacie_id' => $pharmacieId,
                    'date' => now(),
                    'adresse_livraison' => $donnees['adresse'],
                    'latitude' => $donnees['latitude'],
                    'longitude' => $donnees['longitude'],
                    'statut' => StatutCommande::EnAttente,
                    'montant_total' => (int) $lignesPharmacie->sum(fn ($ligne) => $ligne->medicament->prix * $ligne->quantite),
                    'frais_livraison' => $fraisLivraison,
                    'ordonnance_path' => $surOrdonnance ? $donnees['ordonnance_path'] : null,
                    'statut_ordonnance' => $surOrdonnance ? StatutOrdonnance::EnAttente : StatutOrdonnance::Validee,
                ]);

                foreach ($lignesPharmacie as $ligne) {
                    LigneCommande::create([
                        'commande_id' => $commande->id,
                        'medicament_id' => $ligne->medicament_id,
                        'quantite' => $ligne->quantite,
                        'prix_unitaire' => $ligne->medicament->prix,
                    ]);
                }

                // Une livraison est ouverte dès la commande (en attente de coursier)
                Livraison::create([
                    'commande_id' => $commande->id,
                    'statut' => StatutLivraison::EnAttente,
                ]);

                $commandes->push($commande);

                // Notification temps réel + base pour la pharmacie
                $pharmacie->user?->notify(new NotificationGenerique(
                    'Nouvelle commande',
                    "Commande #{$commande->id} de {$commande->montantAvecLivraisonFormate()} reçue — à confirmer.",
                    route('pharmacie.commandes.show', $commande),
                    'info',
                ));
            }

            return $commandes;
        });

        // Vide le panier après succès
        $panier->lignes()->delete();

        return $commandes;
    }

    /** Le client confirme la réception : clôture la commande et la livraison. */
    public function confirmerReception(Commande $commande): Commande
    {
        if (! $commande->peutConfirmerReception()) {
            abort(422, 'Cette commande ne peut pas être confirmée.');
        }

        $commande->update([
            'statut' => StatutCommande::Livree,
            'reception_confirmee_at' => now(),
        ]);

        $commande->livraison?->update([
            'statut' => StatutLivraison::Livree,
            'date_livraison_effective' => now(),
        ]);

        // Notifie la pharmacie et le livreur
        $commande->pharmacie->user?->notify(new NotificationGenerique(
            'Commande livrée',
            "Le client a confirmé la réception de la commande #{$commande->id}.",
            route('pharmacie.commandes.show', $commande),
            'success',
        ));

        $commande->livreur?->user?->notify(new NotificationGenerique(
            'Livraison confirmée',
            "La livraison de la commande #{$commande->id} a été confirmée par le client.",
            route('livreur.livraisons.index'),
            'success',
        ));

        return $commande;
    }

    /** Annule une commande encore annulable (avant préparation). */
    public function annuler(Commande $commande): Commande
    {
        if (! $commande->peutEtreAnnulee()) {
            abort(422, 'Cette commande ne peut plus être annulée.');
        }

        // Restitue le stock
        foreach ($commande->lignes as $ligne) {
            Medicament::query()
                ->whereKey($ligne->medicament_id)
                ->increment('quantite_stock', $ligne->quantite);
        }

        $commande->update(['statut' => StatutCommande::Annulee]);

        $commande->pharmacie->user?->notify(new NotificationGenerique(
            'Commande annulée',
            "La commande #{$commande->id} a été annulée par le client.",
            route('pharmacie.commandes.show', $commande),
            'warning',
        ));

        return $commande;
    }

    /** Stocke une ordonnance téléversée et renvoie son chemin (disque public). */
    public function stockerOrdonnance($fichier): ?string
    {
        if ($fichier === null) {
            return null;
        }

        return $fichier->store('ordonnances', 'public');
    }
}

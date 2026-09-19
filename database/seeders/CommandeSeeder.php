<?php

namespace Database\Seeders;

use App\Enums\ModePaiement;
use App\Enums\StatutCommande;
use App\Enums\StatutLivraison;
use App\Enums\StatutPaiement;
use App\Models\Client;
use App\Models\Commande;
use App\Models\LignePanier;
use App\Models\Livraison;
use App\Models\Livreur;
use App\Models\Medicament;
use App\Models\Parametre;
use App\Models\Paiement;
use App\Models\Pharmacie;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommandeSeeder extends Seeder
{
    public function run(): void
    {
        $clients = User::ofRole(\App\Enums\Role::Client)->get();
        $pharmacies = Pharmacie::query()->whereHas('user', fn ($q) => $q->where('statut_compte', 'actif'))->get();
        $livreurs = Livreur::all();
        $frais = Parametre::getInt(Parametre::FRAIS_LIVRAISON, 1000);

        if ($clients->count() < 3 || $pharmacies->count() < 3 || $livreurs->count() < 2) {
            return;
        }

        $scenarios = [
            // [client, pharmacie, statut final]
            [0, 0, StatutCommande::Livree],        // livrée (avec avis)
            [0, 1, StatutCommande::EnAttente],     // panier payé non validé
            [1, 0, StatutCommande::Payee],
            [1, 1, StatutCommande::Acceptee],
            [1, 2, StatutCommande::Prete],
            [2, 0, StatutCommande::EnLivraison],   // suivi temps réel
            [2, 1, StatutCommande::Annulee],
            [2, 2, StatutCommande::Payee],
        ];

        foreach ($scenarios as [$ci, $pi, $statut]) {
            $client = Client::where('user_id', $clients[$ci]->id)->first();
            $pharmacie = $pharmacies[$pi];

            $medicaments = Medicament::query()
                ->where('pharmacie_id', $pharmacie->id)
                ->where('quantite_stock', '>', 0)
                ->inRandomOrder()
                ->take(2)
                ->get();

            if ($medicaments->isEmpty()) {
                continue;
            }

            $montant = 0;
            $lignes = [];
            foreach ($medicaments as $medicament) {
                $qte = random_int(1, min(3, $medicament->quantite_stock));
                $montant += $medicament->prix * $qte;
                $lignes[] = ['medicament' => $medicament, 'qte' => $qte];
            }

            $commande = Commande::create([
                'client_id' => $client->id,
                'pharmacie_id' => $pharmacie->id,
                'date' => now()->subDays(random_int(1, 12)),
                'adresse_livraison' => $client->adresse ?? 'Yaoundé',
                'latitude' => $client->latitude,
                'longitude' => $client->longitude,
                'statut' => $statut,
                'montant_total' => $montant,
                'frais_livraison' => $frais,
                'reception_confirmee_at' => $statut === StatutCommande::Livree ? now()->subDay() : null,
            ]);

            foreach ($lignes as $l) {
                $commande->lignes()->create([
                    'medicament_id' => $l['medicament']->id,
                    'quantite' => $l['qte'],
                    'prix_unitaire' => $l['medicament']->prix,
                ]);
            }

            // Paiement (toutes sauf EN_ATTENTE)
            if ($statut !== StatutCommande::EnAttente) {
                $mode = $pi % 2 === 0 ? ModePaiement::MtnMomo : ModePaiement::OrangeMoney;
                $paiementEchoue = $statut === StatutCommande::Annulee;

                Paiement::create([
                    'commande_id' => $commande->id,
                    'montant' => $montant + $frais,
                    'mode' => $mode,
                    'statut' => $paiementEchoue ? StatutPaiement::Echoue : StatutPaiement::Valide,
                    'reference_transaction' => $mode->prefixeReference().'-'.strtoupper(Str::random(10)),
                ]);
            }

            // Livraison dès PRETE
            if (in_array($statut, [StatutCommande::Prete, StatutCommande::EnLivraison, StatutCommande::Livree], true)) {
                $livreur = $livreurs[$pi % $livreurs->count()];
                $statutLivraison = match ($statut) {
                    StatutCommande::Prete => StatutLivraison::Acceptee,
                    StatutCommande::EnLivraison => StatutLivraison::EnCours,
                    default => StatutLivraison::Livree,
                };

                $commande->update(['livreur_id' => $livreur->id]);

                Livraison::create([
                    'commande_id' => $commande->id,
                    'livreur_id' => $livreur->id,
                    'statut' => $statutLivraison,
                    'latitude' => $livreur->latitude,
                    'longitude' => $livreur->longitude,
                    'date_livraison_effective' => $statut === StatutCommande::Livree ? now()->subDays(1) : null,
                ]);
            }

            // Avis sur la commande livrée
            if ($statut === StatutCommande::Livree) {
                \App\Models\Avis::query()->updateOrCreate([
                    'client_id' => $client->id,
                    'type' => \App\Enums\TypeAvis::Service,
                    'avirable_type' => Pharmacie::class,
                    'avirable_id' => $pharmacie->id,
                ], [
                    'type' => \App\Enums\TypeAvis::Service,
                    'note' => 5,
                    'commentaire' => 'Service rapide et professionnel, médicaments conformes. Je recommande !',
                ]);
            }
        }

        // Messages de démonstration
        $clientUser = $clients[0];
        $pharmacieUser = $pharmacies[0]->user;

        User::query()->whereIn('id', [$clientUser->id, $pharmacieUser->id])->get();

        \App\Models\Message::create([
            'expediteur_id' => $clientUser->id,
            'destinataire_id' => $pharmacieUser->id,
            'contenu' => 'Bonjour, le Paracétamol 500mg est-il disponible en ce moment ?',
        ]);

        \App\Models\Message::create([
            'expediteur_id' => $pharmacieUser->id,
            'destinataire_id' => $clientUser->id,
            'contenu' => 'Bonjour ! Oui, nous en avons en stock. Vous pouvez commander directement depuis la fiche du produit.',
        ]);
    }
}

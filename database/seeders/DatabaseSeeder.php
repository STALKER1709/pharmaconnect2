<?php

namespace Database\Seeders;

use App\Enums\CommandeStatut;
use App\Enums\LivraisonStatut;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Livreur;
use App\Models\Medicament;
use App\Models\Pharmacie;
use App\Models\PharmacieMedicament;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /** Mot de passe commun des comptes de démonstration. */
    public const MOT_DE_PASSE = 'password';

    public function run(): void
    {
        $mdp = Hash::make(self::MOT_DE_PASSE);

        // ─── Admin ──────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin PharmaConnect',
            'email' => 'admin@pharmaconnect.cm',
            'password' => $mdp,
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '699000001',
        ]);

        // ─── Client de démonstration ────────────────────────────
        $userClient = User::create([
            'name' => 'Aïcha Mbarga',
            'email' => 'client@pharmaconnect.cm',
            'password' => $mdp,
            'role' => 'client',
            'statut' => 'actif',
            'telephone' => '677000002',
        ]);

        $client = Client::create([
            'user_id' => $userClient->id,
            'adresse' => 'Rue Kotto, immeuble Zen',
            'ville' => 'Douala',
            'quartier' => 'Akwa',
            'latitude' => 4.0511,
            'longitude' => 9.7679,
        ]);

        // ─── Pharmacies ─────────────────────────────────────────
        $pharmaciesData = [
            ['Pharmacie du Centre', 'Bonanjo', '4.0408', '9.6944', 'fondateur@pharmaconnect.cm', 'Jean-Paul Etoundi'],
            ['Pharmacie Akwa Palace', 'Akwa', '4.0511', '9.7679', 'akwa@pharmaconnect.cm', 'Marie-Claire Ngo Bakang'],
            ['Pharmacie Bonapriso Plus', 'Bonapriso', '4.0386', '9.6975', 'bonapriso@pharmaconnect.cm', 'Eric Nkwain'],
            ['Pharmacie Bonamoussadi', 'Bonamoussadi', '4.0721', '9.7380', 'bonamoussadi@pharmaconnect.cm', 'Solange Ewusi'],
        ];

        $pharmacies = collect();

        foreach ($pharmaciesData as [$nom, $quartier, $lat, $lng, $email, $titulaire]) {
            $user = User::create([
                'name' => $titulaire,
                'email' => $email,
                'password' => $mdp,
                'role' => 'pharmacie',
                'statut' => 'actif',
                'telephone' => '67'.rand(1000000, 9999999),
            ]);

            $pharmacie = Pharmacie::create([
                'user_id' => $user->id,
                'nom' => $nom,
                'description' => "Pharmacie agréée située à {$quartier}, Douala. Livraison rapide à domicile.",
                'adresse' => "Rue principale, {$quartier}",
                'ville' => 'Douala',
                'quartier' => $quartier,
                'latitude' => (float) $lat,
                'longitude' => (float) $lng,
                'statut' => 'actif',
                'frais_livraison' => rand(0, 1) ? 1000 : 1500,
                'on_livraison' => true,
                'note_moyenne' => rand(35, 50) / 10,
                'nb_avis' => rand(5, 40),
            ]);

            foreach ([1, 2, 3, 4, 5, 6] as $jour) {
                $pharmacie->horaires()->create(['jour' => $jour]);
            }

            $pharmacies->push($pharmacie);
        }

        // ─── Livreurs ───────────────────────────────────────────
        $livreurs = collect();

        foreach ([
            ['Aliou Ndiaye', 'livreur@pharmaconnect.cm', 'moto'],
            ['Blaise Kamdem', 'livreur2@pharmaconnect.cm', 'moto'],
            ['Nadia Fotso', 'livreur3@pharmaconnect.cm', 'voiture'],
        ] as [$nom, $email, $vehicule]) {
            $user = User::create([
                'name' => $nom,
                'email' => $email,
                'password' => $mdp,
                'role' => 'livreur',
                'statut' => 'actif',
                'telephone' => '69'.rand(1000000, 9999999),
            ]);

            $livreurs->push(Livreur::create([
                'user_id' => $user->id,
                'ville' => 'Douala',
                'vehicule' => $vehicule,
                'immatriculation' => strtoupper(\Illuminate\Support\Str::random(2).'-'.rand(100, 999).'-'.\Illuminate\Support\Str::random(2)),
                'statut' => 'actif',
                'disponibilite' => 'disponible',
                'latitude' => rand(40200, 40900) / 10000,
                'longitude' => rand(97000, 98200) / 10000,
                'derniere_position_at' => now(),
            ]));
        }

        // ─── Catégories et médicaments ──────────────────────────
        $categories = collect([
            ['Antalgiques', '💊', 'Douleurs et fièvre'],
            ['Antipaludiques', '🦟', 'Paludisme'],
            ['Antibiotiques', '🦠', 'Infections'],
            ['Vitamines', '🍊', 'Compléments et toniques'],
            ['Digestifs', '🫗', 'Estomac et transit'],
            ['Réhydratation', '💧', 'SRO et solutés'],
        ])->map(fn ($c) => \App\Models\Categorie::create([
            'nom' => $c[0], 'slug' => \Illuminate\Support\Str::slug($c[0]), 'icone' => $c[1], 'description' => $c[2],
        ]));

        $catalogue = [
            ['Paracétamol 500 mg', 'Antalgiques', 500, false, 'comprimé', 'Sanofi'],
            ['Ibuprofène 400 mg', 'Antalgiques', 1200, false, 'comprimé', 'TEVA'],
            ['Aspégaic 1000 mg', 'Antalgiques', 900, false, 'comprimé', 'Bayer'],
            ['Coartem 20/120', 'Antipaludiques', 3500, true, 'comprimé', 'Novartis'],
            ['Quinine 300 mg', 'Antipaludiques', 1500, true, 'comprimé', 'Cipla'],
            ['Amoxicilline 500 mg', 'Antibiotiques', 2500, true, 'gélule', 'GSK'],
            ['Ciprofloxacine 500 mg', 'Antibiotiques', 3000, true, 'comprimé', 'Cipla'],
            ['Métronidazole 250 mg', 'Antibiotiques', 800, true, 'comprimé', 'Sanofi'],
            ['Vitamine C 1 g', 'Vitamines', 1000, false, 'comprimé effervescent', 'Bayer'],
            ['Sirop multivitaminé', 'Vitamines', 2200, false, 'sirop', 'Pharma-Douala'],
            ['Oméprazole 20 mg', 'Digestifs', 1800, true, 'gélule', 'TEVA'],
            ['Diosmectite (Smecta)', 'Digestifs', 1600, false, 'sachet', 'Ipsen'],
            ['SRO (sachet réhydratation)', 'Réhydratation', 200, false, 'sachet', 'Unicef'],
        ];

        $medicaments = collect();

        foreach ($catalogue as [$nom, $cat, $prix, $ordonnance, $forme, $fabricant]) {
            $medicaments->push(Medicament::create([
                'categorie_id' => $categories->firstWhere('nom', $cat)->id,
                'nom' => $nom,
                'slug' => \Illuminate\Support\Str::slug($nom).'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4)),
                'description' => "{$nom} — {$forme}. Rester un médicament authentique, conservé au frais.",
                'posologie' => 'Selon la prescription ou l\'avis de votre pharmacien.',
                'ordonnance_obligatoire' => $ordonnance,
                'fabricant' => $fabricant,
                'forme' => $forme,
                'dosage_mg' => rand(0, 1) ? 500 : 1000,
                'actif' => true,
                // prix de référence utile pour la recherche publique
                'reference' => null,
            ]));
        }

        // ─── Stocks par pharmacie ───────────────────────────────
        foreach ($pharmacies as $pharmacie) {
            foreach ($medicaments->random(rand(8, 13)) as $medicament) {
                PharmacieMedicament::create([
                    'pharmacie_id' => $pharmacie->id,
                    'medicament_id' => $medicament->id,
                    'quantite' => rand(0, 60),
                    'prix' => $medicament->forme === 'sirop' ? rand(18, 30) * 100 : rand(2, 40) * 100,
                    'date_peremption' => now()->addMonths(rand(2, 30)),
                    'seuil_stock_bas' => 5,
                ]);
            }
        }

        // ─── Commandes de démonstration ─────────────────────────
        $statutsFlux = [
            CommandeStatut::EnAttente,
            CommandeStatut::Confirmee,
            CommandeStatut::Prete,
            CommandeStatut::EnLivraison,
            CommandeStatut::Livree,
        ];

        for ($i = 0; $i < 12; $i++) {
            $pharmacie = $pharmacies->random();
            $statut = $statutsFlux[$i % count($statutsFlux)];

            $commande = Commande::create([
                'client_id' => $client->id,
                'pharmacie_id' => $pharmacie->id,
                'numero' => 'PC-'.date('Y').'-'.str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'statut' => $statut,
                'sous_total' => 0,
                'frais_livraison' => $pharmacie->frais_livraison,
                'total' => 0,
                'adresse_livraison' => $client->adresse,
                'ville_livraison' => 'Douala',
                'latitude_livraison' => $client->latitude,
                'longitude_livraison' => $client->longitude,
                'livree_at' => $statut === CommandeStatut::Livree ? now()->subDays(rand(0, 20)) : null,
            ]);

            $sousTotal = 0;

            foreach ($pharmacie->stocks->random(rand(1, 3)) as $stock) {
                $qte = rand(1, 3);
                $commande->lignes()->create([
                    'medicament_id' => $stock->medicament_id,
                    'pharmacie_id' => $pharmacie->id,
                    'nom_medicament' => $stock->medicament->nom,
                    'prix_unitaire' => $stock->prix,
                    'quantite' => $qte,
                    'sous_total' => $stock->prix * $qte,
                ]);
                $sousTotal += $stock->prix * $qte;
            }

            $commande->update([
                'sous_total' => $sousTotal,
                'total' => $sousTotal + $pharmacie->frais_livraison,
            ]);

            $commande->paiement()->create([
                'reference' => 'PAY-'.strtoupper(\Illuminate\Support\Str::random(10)),
                'operateur' => rand(0, 1) ? 'mtn_momo' : 'orange_money',
                'montant' => $commande->total,
                'statut' => 'reussi',
                'numero_payeur' => '6'.rand(70000000, 79999999),
                'numero_paye' => config('services.payment.momo_payee'),
                'reponse_brute' => json_encode(['simule' => true]),
                'paye_at' => $commande->created_at,
            ]);

            $commande->livraison()->create([
                'statut' => match ($statut) {
                    CommandeStatut::EnAttente, CommandeStatut::Confirmee => LivraisonStatut::Disponible,
                    CommandeStatut::Prete => LivraisonStatut::Disponible,
                    CommandeStatut::EnLivraison => LivraisonStatut::EnRoute,
                    CommandeStatut::Livree => LivraisonStatut::Livree,
                    default => LivraisonStatut::Disponible,
                },
                'livreur_id' => in_array($statut, [CommandeStatut::EnLivraison, CommandeStatut::Livree], true)
                    ? $livreurs->random()->id : null,
                'latitude_arrivee' => $client->latitude,
                'longitude_arrivee' => $client->longitude,
                'distance_km' => rand(2, 9),
                'duree_estimee_min' => rand(20, 45),
            ]);

            if ($statut === CommandeStatut::Livree) {
                // Un seul avis par client et par pharmacie (contrainte UNIQUE)
                $avisExistant = \App\Models\Avis::where('client_id', $client->id)
                    ->where('pharmacie_id', $pharmacie->id)
                    ->first();

                if (! $avisExistant) {
                    \App\Models\Avis::create([
                        'client_id' => $client->id,
                        'commande_id' => $commande->id,
                        'pharmacie_id' => $pharmacie->id,
                        'note' => rand(4, 5),
                        'commentaire' => 'Livraison rapide et médicaments conformes. Merci !',
                    ]);
                }
            }
        }

        $this->command?->info('Comptes de démo :');
        $this->command?->table(
            ['Rôle', 'E-mail', 'Mot de passe'],
            [
                ['Admin', 'admin@pharmaconnect.cm', self::MOT_DE_PASSE],
                ['Client', 'client@pharmaconnect.cm', self::MOT_DE_PASSE],
                ['Pharmacie', 'fondateur@pharmaconnect.cm', self::MOT_DE_PASSE],
                ['Livreur', 'livreur@pharmaconnect.cm', self::MOT_DE_PASSE],
            ]
        );
    }
}

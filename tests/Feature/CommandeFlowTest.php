<?php

namespace Tests\Feature;

use App\Enums\CommandeStatut;
use App\Models\Commande;
use App\Models\PharmacieMedicament;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandeFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cycle_complet_commande(): void
    {
        $this->seed(DatabaseSeeder::class);

        $clientUser = \App\Models\User::where('email', 'client@pharmaconnect.cm')->first();

        $stock = PharmacieMedicament::where('quantite', '>', 5)
            ->whereHas('medicament', fn ($q) => $q->where('ordonnance_obligatoire', false))
            ->first();

        $this->assertNotNull($stock, 'Il faut au moins un stock disponible dans le seeder.');

        // 1. Ajouter au panier
        $this->actingAs($clientUser)
            ->post(route('panier.ajouter', $stock), ['quantite' => 2])
            ->assertRedirect();

        // 2. Passer la commande (paiement inclus)
        $reponse = $this->actingAs($clientUser)->post(route('commande.store'), [
            'adresse_livraison' => 'Rue Kotto, immeuble Zen',
            'ville_livraison' => 'Douala',
            'latitude' => 4.0511,
            'longitude' => 9.7679,
            'operateur' => 'mtn_momo',
            'numero_mobile_money' => '677123456',
        ]);

        $commande = Commande::where('client_id', $clientUser->client->id)->latest('id')->first();
        $this->assertNotNull($commande);
        $reponse->assertRedirect(route('commandes.show', $commande));

        $this->assertEquals(CommandeStatut::EnAttente, $commande->statut);
        $this->assertNotNull($commande->paiement);
        $this->assertEquals('reussi', $commande->paiement->statut->value);
        $this->assertEquals($stock->prix * 2 + $stock->pharmacie->frais_livraison, $commande->total);

        // 3. La pharmacie accepte puis marque prête
        $pharmaUser = $commande->pharmacie->user;

        $this->actingAs($pharmaUser)
            ->post(route('pharmacie.commandes.statut', $commande), ['statut' => 'confirmee'])
            ->assertRedirect();

        $this->actingAs($pharmaUser)
            ->post(route('pharmacie.commandes.statut', $commande), ['statut' => 'prete'])
            ->assertRedirect();

        $commande->refresh();
        $this->assertEquals(CommandeStatut::Prete, $commande->statut);

        // 4. Livreur accepte et démarre
        $livreurUser = \App\Models\User::where('email', 'livreur@pharmaconnect.cm')->first();

        $this->actingAs($livreurUser)
            ->post(route('livreur.livraisons.accepter', $commande->livraison))
            ->assertRedirect();

        $this->actingAs($livreurUser)
            ->post(route('livreur.livraisons.demarrer', $commande->livraison))
            ->assertRedirect();

        $commande->refresh();
        $this->assertEquals(CommandeStatut::EnLivraison, $commande->statut);

        // 5. Le client confirme la réception
        $this->actingAs($clientUser)
            ->post(route('commandes.confirmer-reception', $commande))
            ->assertRedirect();

        $commande->refresh();
        $this->assertEquals(CommandeStatut::Livree, $commande->statut);
    }

    public function test_stock_decremente_apres_commande(): void
    {
        $this->seed(DatabaseSeeder::class);

        $clientUser = \App\Models\User::where('email', 'client@pharmaconnect.cm')->first();

        $stock = PharmacieMedicament::where('quantite', '>', 5)
            ->whereHas('medicament', fn ($q) => $q->where('ordonnance_obligatoire', false))
            ->first();

        $avant = $stock->quantite;

        $this->actingAs($clientUser)
            ->post(route('panier.ajouter', $stock), ['quantite' => 3]);

        $this->actingAs($clientUser)->post(route('commande.store'), [
            'adresse_livraison' => 'Test Douala',
            'ville_livraison' => 'Douala',
            'operateur' => 'orange_money',
            'numero_mobile_money' => '690123456',
        ]);

        $this->assertEquals($avant - 3, $stock->refresh()->quantite);
    }
}

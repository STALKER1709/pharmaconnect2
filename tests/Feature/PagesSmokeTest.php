<?php

namespace Tests\Feature;

use App\Models\Commande;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_publiques(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/pharmacies')->assertOk();
        $this->get('/medicaments')->assertOk();

        $pharmacie = \App\Models\Pharmacie::first();
        $this->get("/pharmacies/{$pharmacie->id}")->assertOk();

        $medicament = \App\Models\Medicament::first();
        $this->get("/medicaments/{$medicament->id}")->assertOk();

        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_pages_client(): void
    {
        $this->seed(DatabaseSeeder::class);
        $client = \App\Models\User::where('email', 'client@pharmaconnect.cm')->first();

        $this->actingAs($client)->get('/dashboard')->assertOk();
        $this->actingAs($client)->get('/client/dashboard')->assertOk();
        $this->actingAs($client)->get('/commandes')->assertOk();
        $this->actingAs($client)->get('/panier')->assertOk();
        $this->actingAs($client)->get('/messagerie')->assertOk();
        $this->actingAs($client)->get('/chatbot')->assertOk();
        $this->actingAs($client)->get('/profil')->assertOk();

        $commande = Commande::where('client_id', $client->client->id)->first();
        $this->actingAs($client)->get("/commandes/{$commande->id}")->assertOk();
        $this->actingAs($client)->get("/commandes/{$commande->id}/suivi")->assertOk();
    }

    public function test_pages_pharmacie(): void
    {
        $this->seed(DatabaseSeeder::class);
        $pharmacie = \App\Models\User::where('email', 'fondateur@pharmaconnect.cm')->first();

        $this->actingAs($pharmacie)->get('/pharmacie/dashboard')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/medicaments')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/commandes')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/paiements')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/livraisons')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/horaires')->assertOk();
        $this->actingAs($pharmacie)->get('/pharmacie/statistiques')->assertOk();

        $commande = Commande::where('pharmacie_id', $pharmacie->pharmacie->id)->first();
        $this->actingAs($pharmacie)->get("/pharmacie/commandes/{$commande->id}")->assertOk();
    }

    public function test_pages_livreur(): void
    {
        $this->seed(DatabaseSeeder::class);
        $livreur = \App\Models\User::where('email', 'livreur@pharmaconnect.cm')->first();

        $this->actingAs($livreur)->get('/dashboard')->assertRedirect('/livreur/dashboard');
        $this->actingAs($livreur)->get('/livreur/dashboard')->assertOk();

        $livraison = \App\Models\Livraison::where('livreur_id', $livreur->livreur->id)->first();
        if ($livraison) {
            $this->actingAs($livreur)->get("/livreur/livraisons/{$livraison->id}")->assertOk();
        }
    }

    public function test_pages_admin(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = \App\Models\User::where('email', 'admin@pharmaconnect.cm')->first();

        $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin/dashboard');
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $this->actingAs($admin)->get('/admin/utilisateurs')->assertOk();

        $user = \App\Models\User::where('role', 'pharmacie')->first();
        $this->actingAs($admin)->get("/admin/utilisateurs/{$user->id}")->assertOk();
    }
}

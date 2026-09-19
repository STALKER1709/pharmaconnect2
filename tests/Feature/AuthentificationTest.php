<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_accueil_public_accessible(): void
    {
        $this->seed(DatabaseSeeder::class);

        $reponse = $this->get('/');

        $reponse->assertStatus(200)
            ->assertSee('PharmaConnect');
    }

    public function test_inscription_client_actif_immediatement(): void
    {
        $reponse = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'test.client@example.cm',
            'telephone' => '690123456',
            'role' => 'client',
            'password' => 'MotDePasse1!',
            'password_confirmation' => 'MotDePasse1!',
        ]);

        $reponse->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test.client@example.cm')->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role);
        $this->assertEquals('actif', $user->statut);
        $this->assertNotNull($user->client);
    }

    public function test_inscription_pharmacie_en_attente(): void
    {
        $reponse = $this->post('/register', [
            'name' => 'Test Pharma',
            'email' => 'test.pharma@example.cm',
            'telephone' => '677654321',
            'role' => 'pharmacie',
            'nom_pharmacie' => 'Pharmacie Test',
            'adresse_pharmacie' => 'Rue Test, Douala',
            'password' => 'MotDePasse1!',
            'password_confirmation' => 'MotDePasse1!',
        ]);

        $reponse->assertRedirect(route('login', absolute: false));

        $user = User::where('email', 'test.pharma@example.cm')->first();
        $this->assertNotNull($user);
        $this->assertEquals('en_attente', $user->statut);
        $this->assertNotNull($user->pharmacie);
    }

    public function test_middleware_role_bloque_les_autres_roles(): void
    {
        $client = User::factory()->client()->create();
        $client->client()->create();

        $this->actingAs($client)
            ->get(route('pharmacie.dashboard'))
            ->assertForbidden();
    }

    public function test_connexion_compte_valide(): void
    {
        $user = User::factory()->create(['password' => bcrypt('MotDePasse1!')]);

        $reponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'MotDePasse1!',
        ]);

        $reponse->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}

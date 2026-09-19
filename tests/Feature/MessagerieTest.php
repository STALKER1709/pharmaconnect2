<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagerieTest extends TestCase
{
    use RefreshDatabase;

    public function test_pharmacie_et_livreur_partagent_une_seule_conversation(): void
    {
        $pharmacie = User::factory()->pharmacie()->create();
        $livreur = User::factory()->livreur()->create();

        // Premier clic pharmacie → livreur : conversation créée
        $this->actingAs($pharmacie)
            ->post(route('messagerie.demarrer', $livreur))
            ->assertRedirect();

        // Deuxième clic dans l'autre sens : la même conversation doit être retrouvée
        $this->actingAs($livreur)
            ->post(route('messagerie.demarrer', $pharmacie))
            ->assertRedirect();

        $this->assertSame(1, \App\Models\Conversation::count());
    }

    public function test_client_et_pharmacie_partagent_une_seule_conversation(): void
    {
        $client = User::factory()->client()->create();
        $pharmacie = User::factory()->pharmacie()->create();

        $this->actingAs($client)->post(route('messagerie.demarrer', $pharmacie))->assertRedirect();
        $this->actingAs($pharmacie)->post(route('messagerie.demarrer', $client))->assertRedirect();

        $this->assertSame(1, \App\Models\Conversation::count());
    }

    public function test_admin_ne_peut_pas_demarrer_de_conversation(): void
    {
        $admin = User::factory()->admin()->create();
        $client = User::factory()->client()->create();

        $this->actingAs($admin)
            ->post(route('messagerie.demarrer', $client))
            ->assertStatus(422);

        $this->assertSame(0, \App\Models\Conversation::count());
    }
}

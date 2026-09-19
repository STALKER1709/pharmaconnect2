<?php

namespace Database\Factories;

use App\Enums\CommandeStatut;
use App\Models\Client;
use App\Models\Pharmacie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    public function definition(): array
    {
        $sousTotal = fake()->numberBetween(10, 60) * 500;

        return [
            'client_id' => Client::factory(),
            'pharmacie_id' => Pharmacie::factory(),
            'numero' => 'PC-'.date('Y').'-'.fake()->unique()->numerify('#####'),
            'statut' => fake()->randomElement(CommandeStatut::cases()),
            'sous_total' => $sousTotal,
            'frais_livraison' => 1000,
            'total' => $sousTotal + 1000,
            'adresse_livraison' => fake()->streetAddress(),
            'ville_livraison' => 'Douala',
            'latitude_livraison' => fake()->latitude(4.02, 4.09),
            'longitude_livraison' => fake()->longitude(9.70, 9.82),
            'livree_at' => now(),
        ];
    }
}

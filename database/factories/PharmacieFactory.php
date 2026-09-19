<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pharmacie>
 */
class PharmacieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->pharmacie(),
            'nom' => 'Pharmacie '.fake()->randomElement(['Akwa', 'Bonanjo', 'Deido', 'Bonamoussadi', 'Bepanda', 'Makepe', 'la Réunification', 'du Centre']),
            'description' => fake()->sentence(12),
            'adresse' => fake()->streetAddress(),
            'ville' => 'Douala',
            'quartier' => fake()->randomElement(['Akwa', 'Bonapriso', 'Bonamoussadi', 'Deido', 'Bepanda', 'Makepe', 'Bonanjo']),
            'latitude' => fake()->latitude(4.02, 4.09),
            'longitude' => fake()->longitude(9.70, 9.82),
            'statut' => 'actif',
            'frais_livraison' => fake()->randomElement([500, 1000, 1500]),
            'on_livraison' => true,
            'note_moyenne' => fake()->randomFloat(2, 3, 5),
            'nb_avis' => fake()->numberBetween(0, 40),
        ];
    }

    public function enAttente(): static
    {
        return $this->state(fn () => ['statut' => 'en_attente']);
    }
}

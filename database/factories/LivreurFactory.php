<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livreur>
 */
class LivreurFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->livreur(),
            'ville' => 'Douala',
            'vehicule' => fake()->randomElement(['moto', 'moto', 'voiture', 'velo']),
            'immatriculation' => strtoupper(fake()->bothify('??-###-??')),
            'statut' => 'actif',
            'disponibilite' => fake()->randomElement(['disponible', 'hors_ligne']),
            'note_moyenne' => fake()->randomFloat(2, 3, 5),
            'nb_avis' => fake()->numberBetween(0, 60),
            'latitude' => fake()->latitude(4.02, 4.09),
            'longitude' => fake()->longitude(9.70, 9.82),
            'derniere_position_at' => now(),
        ];
    }
}

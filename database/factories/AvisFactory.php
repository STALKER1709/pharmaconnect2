<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Avis>
 */
class AvisFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'note' => fake()->numberBetween(3, 5),
            'commentaire' => fake()->randomElement([
                'Service rapide, médicaments authentiques. Je recommande !',
                'Livraison dans les délais, livreur très courtois.',
                'Bon accueil et conseils pertinents en pharmacie.',
                'Prix corrects mais délai un peu long.',
            ]),
        ];
    }
}

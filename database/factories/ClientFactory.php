<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->client(),
            'adresse' => fake()->streetAddress(),
            'ville' => 'Douala',
            'quartier' => fake()->randomElement(['Akwa', 'Bonapriso', 'Bonamoussadi', 'Deido', 'Bepanda', 'Makepe', 'Ndogbong']),
            'latitude' => fake()->latitude(4.02, 4.09),
            'longitude' => fake()->longitude(9.70, 9.82),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'client',
            'statut' => 'actif',
            'telephone' => '6'.fake()->numberBetween(5, 9).fake()->numerify('#######'),
        ];
    }

    public function client(): static
    {
        return $this->state(fn () => ['role' => 'client']);
    }

    public function pharmacie(): static
    {
        return $this->state(fn () => ['role' => 'pharmacie']);
    }

    public function livreur(): static
    {
        return $this->state(fn () => ['role' => 'livreur']);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function enAttente(): static
    {
        return $this->state(fn () => ['statut' => 'en_attente']);
    }

    public function nonVerifie(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}

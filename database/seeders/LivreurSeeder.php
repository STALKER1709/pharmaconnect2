<?php

namespace Database\Seeders;

use App\Models\Livreur;
use App\Models\User;
use Illuminate\Database\Seeder;

class LivreurSeeder extends Seeder
{
    private const LIVREURS = [
        [
            'nom' => 'Mbarga Serge',
            'email' => 'livreur@pharmaconnect.cm',
            'disponible' => true,
            'latitude' => 3.8700,
            'longitude' => 11.5100,
        ],
        [
            'nom' => 'Tchoumi Alain',
            'email' => 'alain.livreur@pharmaconnect.cm',
            'disponible' => true,
            'latitude' => 3.8400,
            'longitude' => 11.4950,
        ],
    ];

    public function run(): void
    {
        foreach (self::LIVREURS as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['nom'],
                    'password' => 'password',
                    'sexe' => 'M',
                    'date_naissance' => '1995-03-08',
                    'telephone' => '+2376'.random_int(10000000, 99999999),
                    'role' => 'livreur',
                    'statut_compte' => 'actif',
                    'email_verified_at' => now(),
                ]
            );

            Livreur::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'disponible' => $data['disponible'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
        }
    }
}

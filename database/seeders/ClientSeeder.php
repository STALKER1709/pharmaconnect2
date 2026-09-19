<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    private const CLIENTS = [
        [
            'nom' => 'Fotso Jean',
            'email' => 'client@pharmaconnect.cm',
            'sexe' => 'M',
            'adresse' => 'Quartier Melen, face université Yaoundé 1',
            'latitude' => 3.8622,
            'longitude' => 11.5150,
        ],
        [
            'nom' => 'Ngono Marie',
            'email' => 'marie.client@pharmaconnect.cm',
            'sexe' => 'F',
            'adresse' => 'Quartier Briqueterie, Yaoundé',
            'latitude' => 3.8800,
            'longitude' => 11.4900,
        ],
        [
            'nom' => 'Kamdem Eric',
            'email' => 'eric.client@pharmaconnect.cm',
            'sexe' => 'M',
            'adresse' => 'Quartier Mvan, Yaoundé',
            'latitude' => 3.8100,
            'longitude' => 11.5350,
        ],
    ];

    public function run(): void
    {
        foreach (self::CLIENTS as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['nom'],
                    'password' => 'password',
                    'sexe' => $data['sexe'],
                    'date_naissance' => '1993-09-30',
                    'telephone' => '+2376'.random_int(10000000, 99999999),
                    'role' => 'client',
                    'statut_compte' => 'actif',
                    'email_verified_at' => now(),
                ]
            );

            Client::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'adresse' => $data['adresse'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
        }
    }
}

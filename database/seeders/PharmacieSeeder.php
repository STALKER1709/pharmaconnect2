<?php

namespace Database\Seeders;

use App\Enums\StatutPharmacie;
use App\Models\Pharmacie;
use App\Models\User;
use Illuminate\Database\Seeder;

class PharmacieSeeder extends Seeder
{
    /** 3 pharmacies de Yaoundé avec coordonnées réalistes. */
    private const PHARMACIES = [
        [
            'nom' => 'Pharmacie du Centre',
            'email' => 'pharmacie.du.centre@pharmaconnect.cm',
            'responsable' => 'Dr Mballa Alice',
            'adresse' => 'Avenue Kennedy, centre-ville, Yaoundé',
            'latitude' => 3.8667,
            'longitude' => 11.5167,
            'ouverture' => '08:00',
            'fermeture' => '20:00',
            'statut' => StatutPharmacie::Ouverte,
        ],
        [
            'nom' => 'Pharmacie Nsimeyong',
            'email' => 'pharmacie.nsimeyong@pharmaconnect.cm',
            'responsable' => 'Dr Essomba Paul',
            'adresse' => 'Quartier Nsimeyong, route de Mfou, Yaoundé',
            'latitude' => 3.8350,
            'longitude' => 11.4980,
            'ouverture' => '07:30',
            'fermeture' => '21:00',
            'statut' => StatutPharmacie::Ouverte,
        ],
        [
            'nom' => 'Pharmacie de Garde Bastos',
            'email' => 'pharmacie.bastos@pharmaconnect.cm',
            'responsable' => 'Dr Ngo Bell Clarisse',
            'adresse' => 'Quartier Bastos, rue Nlongkak, Yaoundé',
            'latitude' => 3.8900,
            'longitude' => 11.5100,
            'ouverture' => '00:00',
            'fermeture' => '23:59',
            'statut' => StatutPharmacie::DeGarde,
        ],
    ];

    public function run(): void
    {
        foreach (self::PHARMACIES as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    'nom' => $data['responsable'],
                    'password' => 'password',
                    'sexe' => str_contains($data['responsable'], 'Clarisse') ? 'F' : 'M',
                    'date_naissance' => '1988-06-15',
                    'telephone' => '+2376'.random_int(10000000, 99999999),
                    'role' => 'pharmacie',
                    'statut_compte' => 'actif',
                    'email_verified_at' => now(),
                ]
            );

            Pharmacie::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nom_commercial' => $data['nom'],
                    'adresse' => $data['adresse'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'heure_ouverture' => $data['ouverture'],
                    'heure_fermeture' => $data['fermeture'],
                    'statut' => $data['statut'],
                ]
            );
        }

        // Une pharmacie en attente de validation par l'admin (démo du flux de validation)
        $enAttente = User::query()->updateOrCreate(
            ['email' => 'nouvelle.pharmacie@pharmaconnect.cm'],
            [
                'nom' => 'Dr Owona Bertrand',
                'password' => 'password',
                'sexe' => 'M',
                'date_naissance' => '1990-02-20',
                'telephone' => '+237677000099',
                'role' => 'pharmacie',
                'statut_compte' => 'en_attente',
                'email_verified_at' => now(),
            ]
        );

        Pharmacie::query()->updateOrCreate(
            ['user_id' => $enAttente->id],
            [
                'nom_commercial' => 'Pharmacie Odza',
                'adresse' => 'Quartier Odza, route aéroport, Yaoundé',
                'latitude' => 3.7930,
                'longitude' => 11.5310,
                'heure_ouverture' => '08:00',
                'heure_fermeture' => '19:00',
                'statut' => StatutPharmacie::Fermee,
            ]
        );
    }
}

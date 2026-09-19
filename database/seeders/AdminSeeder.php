<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@pharmaconnect.cm'],
            [
                'nom' => 'Admin PharmaConnect',
                'password' => 'password',
                'sexe' => 'M',
                'date_naissance' => '1985-04-12',
                'telephone' => '+237690000001',
                'role' => 'admin',
                'statut_compte' => 'actif',
                'email_verified_at' => now(),
            ]
        );
    }
}

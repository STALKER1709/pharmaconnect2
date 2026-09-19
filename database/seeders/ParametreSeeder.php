<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        Parametre::set(Parametre::FRAIS_LIVRAISON, '1000', 'Frais de livraison par commande (FCFA)');
    }
}

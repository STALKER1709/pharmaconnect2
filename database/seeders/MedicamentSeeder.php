<?php

namespace Database\Seeders;

use App\Models\Medicament;
use App\Models\MedicamentReference;
use App\Models\Pharmacie;
use Illuminate\Database\Seeder;

class MedicamentSeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = Pharmacie::query()
            ->whereHas('user', fn ($q) => $q->where('statut_compte', 'actif'))
            ->get();

        if ($pharmacies->isEmpty()) {
            return;
        }

        $references = MedicamentReference::query()->get();
        if ($references->isEmpty()) {
            return;
        }

        // 30 médicaments : chaque pharmacie active reçoit 10 produits du catalogue
        foreach ($pharmacies as $index => $pharmacie) {
            $catalogue = $references->shuffle();

            for ($i = 0; $i < 10; $i++) {
                $ref = $catalogue->get($i % $catalogue->count());

                Medicament::query()->updateOrCreate(
                    [
                        'pharmacie_id' => $pharmacie->id,
                        'nom' => $ref->nom,
                    ],
                    [
                        'description' => $ref->description,
                        'date_peremption' => $this->datePeremption($index, $i),
                        'prix' => $this->prix($ref->nom, $index),
                        'quantite_stock' => $this->stock($i),
                        'seuil_alerte' => 5,
                        'sur_ordonnance' => $ref->sur_ordonnance,
                    ]
                );
            }
        }
    }

    /** Réaliste : la 1re pharmacie a 2 produits proches péremption, la 2e a 1 périmé. */
    private function datePeremption(int $pharmacieIndex, int $itemIndex): \Illuminate\Support\Carbon
    {
        if ($pharmacieIndex === 0 && $itemIndex === 0) {
            return now()->addDays(20); // alerte péremption proche
        }

        if ($pharmacieIndex === 1 && $itemIndex === 0) {
            return now()->subDays(10); // périmé : masqué de la recherche
        }

        return now()->addDays(random_int(120, 700));
    }

    private function prix(string $nom, int $pharmacieIndex): int
    {
        $base = collect([
            'Paracétamol' => 500,
            'Ibuprofène' => 1000,
            'Amoxicilline' => 2500,
            'Artéméther' => 3500,
            'Coartem' => 4200,
            'Métronidazole' => 1500,
            'Vitamine C' => 1000,
            'SRO' => 300,
            'Oméprazole' => 2800,
            'Métformine' => 3200,
            'Amlodipine' => 3000,
            'Salbutamol' => 5500,
            'Cétirizine' => 1200,
            'Sirop' => 1800,
            'Moustiquaire' => 5000,
            'Test rapide' => 1500,
            'Alcohol' => 700,
            'Compresses' => 500,
            'Férocaramel' => 2000,
        ]);

        $prixBase = $base->first(fn ($p, $prefixe) => str_starts_with($nom, $prefixe), 1500);

        // Variation de ±15 % par pharmacie
        $variation = 1 + (($pharmacieIndex % 3) - 1) * 0.15;

        return max(100, (int) round($prixBase * $variation / 50) * 50);
    }

    private function stock(int $itemIndex): int
    {
        return match (true) {
            $itemIndex === 1 => 3,     // alerte stock bas
            $itemIndex === 2 => 0,     // rupture
            default => random_int(15, 120),
        };
    }
}

<?php

namespace Database\Factories;

use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicament>
 */
class MedicamentFactory extends Factory
{
    public function definition(): array
    {
        $nom = fake()->unique()->randomElement([
            'Paracétamol', 'Ibuprofène', 'Amoxicilline', 'Artéméther-Luméfantrine',
            'Quinine', 'Oméprazole', 'Loratadine', 'Métronidazole', 'Amlodipine',
            'Metformine', 'Vitamine C', 'SRO', 'Sirop antitussif', 'Ciprofloxacine',
        ]);

        return [
            'categorie_id' => Categorie::factory(),
            'nom' => $nom,
            'slug' => Str::slug($nom).'-'.Str::lower(Str::random(5)),
            'description' => fake()->sentence(15),
            'posologie' => fake()->randomElement(['1 comprimé 3 fois par jour après les repas.', '2 gélules matin et soir.', 'Selon prescription médicale.']),
            'ordonnance_obligatoire' => fake()->boolean(25),
            'fabricant' => fake()->randomElement(['Sanofi', 'GSK', 'Pharma-Douala', 'Cipla', 'TEVA']),
            'forme' => fake()->randomElement(['comprimé', 'gélule', 'sirop', 'sachet', 'injectable']),
            'dosage_mg' => fake()->randomElement([100, 250, 500, 1000]),
            'actif' => true,
        ];
    }
}

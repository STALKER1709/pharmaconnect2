<?php

namespace Database\Seeders;

use App\Models\MedicamentReference;
use Illuminate\Database\Seeder;

class MedicamentReferenceSeeder extends Seeder
{
    private const CATALOGUE = [
        ['nom' => 'Paracétamol 500mg', 'description' => 'Antalgique et antipyrétique', 'sur_ordonnance' => false],
        ['nom' => 'Ibuprofène 400mg', 'description' => 'Anti-inflammatoire non stéroïdien', 'sur_ordonnance' => false],
        ['nom' => 'Amoxicilline 500mg', 'description' => 'Antibiotique de la famille des pénicillines', 'sur_ordonnance' => true],
        ['nom' => 'Artéméther-Luméfantrine 20/120', 'description' => 'Traitement du paludisme simple (CTA)', 'sur_ordonnance' => true],
        ['nom' => 'Coartem 20/120', 'description' => 'Traitement du paludisme simple', 'sur_ordonnance' => true],
        ['nom' => 'Métronidazole 250mg', 'description' => 'Antibiotique antiparasitaire', 'sur_ordonnance' => true],
        ['nom' => 'Vitamine C 500mg', 'description' => 'Complément alimentaire antioxydant', 'sur_ordonnance' => false],
        ['nom' => 'SRO (Sels de Réhydratation Orale)', 'description' => 'Réhydratation en cas de diarrhée', 'sur_ordonnance' => false],
        ['nom' => 'Oméprazole 20mg', 'description' => 'Inhibiteur de la pompe à protons (brûlures d\'estomac)', 'sur_ordonnance' => false],
        ['nom' => 'Métformine 850mg', 'description' => 'Antidiabétique oral (diabète de type 2)', 'sur_ordonnance' => true],
        ['nom' => 'Amlodipine 5mg', 'description' => 'Antihypertenseur (calcium-bloquant)', 'sur_ordonnance' => true],
        ['nom' => 'Salbutamol spray', 'description' => 'Bronchodilatateur (asthme)', 'sur_ordonnance' => true],
        ['nom' => 'Cétirizine 10mg', 'description' => 'Antihistaminique (allergies)', 'sur_ordonnance' => false],
        ['nom' => 'Sirop toux Eucalyptus', 'description' => 'Sirop expectorant naturel', 'sur_ordonnance' => false],
        ['nom' => 'Moustiquaire imprégnée', 'description' => 'Prévention du paludisme', 'sur_ordonnance' => false],
        ['nom' => 'Test rapide paludisme (TDR)', 'description' => 'Autodiagnostic du paludisme', 'sur_ordonnance' => false],
        ['nom' => 'Alcohol modifié 70°', 'description' => 'Antiseptique local', 'sur_ordonnance' => false],
        ['nom' => 'Compresses stériles', 'description' => 'Pansements stériles', 'sur_ordonnance' => false],
        ['nom' => 'Férocaramel (Fer + Acide folique)', 'description' => 'Supplément fer (anémies, grossesse)', 'sur_ordonnance' => false],
        ['nom' => 'Paracétamol sirop enfant', 'description' => 'Antalgique pédiatrique', 'sur_ordonnance' => false],
    ];

    public function run(): void
    {
        foreach (self::CATALOGUE as $item) {
            MedicamentReference::query()->updateOrCreate(
                ['nom' => $item['nom']],
                [
                    'description' => $item['description'],
                    'sur_ordonnance' => $item['sur_ordonnance'],
                ]
            );
        }
    }
}

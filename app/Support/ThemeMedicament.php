<?php

namespace App\Support;

/**
 * Thème visuel d'un médicament selon sa catégorie, repris des cartes des
 * maquettes Stitch (fond, bordure, pastille catégorie, pictogramme SVG).
 */
class ThemeMedicament
{
    private const THEMES = [
        'antalg' => ['fond' => 'bg-[#f8fafc] border-slate-100', 'pastille' => 'bg-slate-200 text-slate-700', 'svg' => 'boite', 'icone' => 'pill', 'teinte' => 'bg-slate-100 text-slate-600'],
        'palu' => ['fond' => 'bg-[#fefce8] border-amber-100', 'pastille' => 'bg-amber-100 text-amber-800', 'svg' => 'blister', 'icone' => 'medication', 'teinte' => 'bg-amber-50 text-amber-700'],
        'antibio' => ['fond' => 'bg-[#f0fdf4] border-green-100', 'pastille' => 'bg-[#dcfce7] text-[#15803d]', 'svg' => 'gelule', 'icone' => 'vaccines', 'teinte' => 'bg-[#dcfce7] text-[#15803d]'],
        'vitamin' => ['fond' => 'bg-[#fff7ed] border-orange-100', 'pastille' => 'bg-orange-100 text-orange-800', 'svg' => 'tube', 'icone' => 'nutrition', 'teinte' => 'bg-orange-50 text-orange-700'],
        'digest' => ['fond' => 'bg-[#fdf2f8] border-pink-100', 'pastille' => 'bg-pink-100 text-pink-800', 'svg' => 'lyoc', 'icone' => 'gastroenterology', 'teinte' => 'bg-pink-50 text-pink-700'],
        'hydrat' => ['fond' => 'bg-[#f0fdfa] border-teal-100', 'pastille' => 'bg-teal-100 text-teal-800', 'svg' => 'flacon', 'icone' => 'water_drop', 'teinte' => 'bg-teal-50 text-teal-700'],
        'urgence' => ['fond' => 'bg-[#eff6ff] border-blue-100', 'pastille' => 'bg-blue-100 text-blue-800', 'svg' => 'ampoule', 'icone' => 'syringe', 'teinte' => 'bg-blue-50 text-blue-700'],
        'respir' => ['fond' => 'bg-slate-50 border-slate-200', 'pastille' => 'bg-slate-200 text-slate-700', 'svg' => 'spray', 'icone' => 'pulmonology', 'teinte' => 'bg-slate-100 text-slate-600'],
    ];

    /** @return array{fond: string, pastille: string, svg: string, icone: string, teinte: string} */
    public static function pour(?string $categorie): array
    {
        $nom = mb_strtolower((string) $categorie);

        foreach (self::THEMES as $cle => $theme) {
            if (str_contains($nom, $cle)) {
                return $theme;
            }
        }

        return match (true) {
            str_contains($nom, 'soin'), str_contains($nom, 'hygi') => self::THEMES['hydrat'],
            str_contains($nom, 'spasm') => self::THEMES['digest'],
            str_contains($nom, 'tonus'), str_contains($nom, 'complé') => self::THEMES['vitamin'],
            default => self::THEMES['antalg'],
        };
    }
}

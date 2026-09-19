<?php

namespace App\Support;

/**
 * Helpers monnaie (FCFA / XAF) et géographie.
 */
class Fcfa
{
    /** Formate un montant en FCFA : 12 500 FCFA */
    public static function montant(int|float|null $valeur): string
    {
        $valeur = (int) round((float) $valeur);

        return number_format($valeur, 0, ',', ' ').' FCFA';
    }

    /** Distance haversine en km entre deux points (lat/lon décimaux). */
    public static function distanceKm(
        ?float $lat1,
        ?float $lon1,
        ?float $lat2,
        ?float $lon2,
        int $decimales = 1
    ): ?float {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return null;
        }

        $r = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * asin(min(1.0, sqrt($a)));

        return round($r * $c, $decimales);
    }
}

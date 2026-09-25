<?php

use Illuminate\Support\Facades\Date;

if (! function_exists('format_fcfa')) {
    /** Formate un montant entier en FCFA : 12 500 FCFA. */
    function format_fcfa(int|float|null $montant): string
    {
        return number_format((int) $montant, 0, ',', ' ').' FCFA';
    }
}

if (! function_exists('expression_haversine_sql')) {
    /**
     * Expression SQL de distance (km) entre un point fixe et deux colonnes,
     * formule de Haversine. Les valeurs numériques sont castées en float
     * avant interpolation (aucune entrée utilisateur brute).
     */
    function expression_haversine_sql(string $colLat, string $colLon, float $lat, float $lon): string
    {
        $lat = (float) $lat;
        $lon = (float) $lon;

        return "(6371 * acos(cos(radians({$lat})) * cos(radians({$colLat})) * cos(radians({$colLon})) - radians({$lon})) + sin(radians({$lat})) * sin(radians({$colLat}))))";
    }
}

if (! function_exists('haversine_km')) {
    /** Distance en km entre deux coordonnées (formule de Haversine). */
    function haversine_km(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

if (! function_exists('initiales_sexe')) {
    /** « M » / « F » -> « Homme » / « Femme ». */
    function libelle_sexe(?string $sexe): string
    {
        return match ($sexe) {
            'M' => 'Homme',
            'F' => 'Femme',
            default => 'Non renseigné',
        };
    }
}

if (! function_exists('initiales')) {
    /** « Marc-Aurèle Tchounkeu » -> « MT » (avatars des maquettes). */
    function initiales(?string $nom): string
    {
        $mots = preg_split('/[\s\-]+/u', trim((string) $nom), -1, PREG_SPLIT_NO_EMPTY) ?: ['?'];
        $lettres = count($mots) > 1 ? [reset($mots), end($mots)] : [$mots[0]];

        return mb_strtoupper(implode('', array_map(fn ($m) => mb_substr($m, 0, 1), $lettres)));
    }
}

if (! function_exists('layout_espace')) {
    /** Gabarit Blade de l'espace de l'utilisateur connecté (pages partagées : messagerie, profil…). */
    function layout_espace(): string
    {
        $user = auth()->user();

        return match (true) {
            $user?->estPharmacie() => 'layouts.pharmacie',
            $user?->estAdmin() => 'layouts.admin',
            $user?->estLivreur() => 'layouts.livreur',
            default => 'layouts.app',
        };
    }
}

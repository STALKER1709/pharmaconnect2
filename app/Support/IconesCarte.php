<?php

namespace App\Support;

/**
 * Marqueurs HTML des cartes Leaflet de suivi (pastilles de la maquette
 * suivi_de_livraison) : officine, coursier en mouvement, domicile du patient.
 */
class IconesCarte
{
    /** @return array<string, string> */
    public static function toutes(): array
    {
        return [
            'pharmacie' => '<div class="w-8 h-8 rounded-full bg-surface-container-lowest delivery-shadow text-primary flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">local_pharmacy</span></div>',
            'livreur' => '<div class="relative flex items-center justify-center w-11 h-11"><span class="absolute w-12 h-12 rounded-full bg-primary/20 animate-ping"></span><div class="relative w-11 h-11 rounded-full bg-primary text-on-primary delivery-shadow flex items-center justify-center"><span class="material-symbols-outlined text-[22px]">two_wheeler</span></div></div>',
            'destination' => '<div class="w-9 h-9 rounded-full bg-tertiary text-on-tertiary delivery-shadow flex items-center justify-center"><span class="material-symbols-outlined text-[20px]">home_pin</span></div>',
        ];
    }
}

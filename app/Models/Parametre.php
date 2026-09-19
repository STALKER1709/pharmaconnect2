<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    public const FRAIS_LIVRAISON = 'frais_livraison';

    protected $fillable = ['cle', 'valeur', 'label'];

    /** Récupère une valeur (entier) avec défaut. */
    public static function getInt(string $cle, int $defaut): int
    {
        $parametre = static::query()->where('cle', $cle)->first();

        if ($parametre === null || $parametre->valeur === null || ! is_numeric($parametre->valeur)) {
            return $defaut;
        }

        return (int) $parametre->valeur;
    }

    /** Crée ou met à jour un paramètre. */
    public static function set(string $cle, ?string $valeur, string $label): void
    {
        static::query()->updateOrCreate(['cle' => $cle], ['valeur' => $valeur, 'label' => $label]);
    }
}

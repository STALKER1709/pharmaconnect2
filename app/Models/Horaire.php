<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horaire extends Model
{
    public $timestamps = false;

    protected $fillable = ['pharmacie_id', 'jour', 'ouvert', 'heure_ouverture', 'heure_fermeture'];

    protected function casts(): array
    {
        return [
            'ouvert' => 'boolean',
            'heure_ouverture' => 'datetime:H:i',
            'heure_fermeture' => 'datetime:H:i',
        ];
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    public static function jours(): array
    {
        return [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mundi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
    }

    public function jourLabel(): string
    {
        return self::jours()[$this->jour] ?? '';
    }
}

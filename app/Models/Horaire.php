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
        return [0 => 'Dimanche', 1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi'];
    }

    /** Heure au format des maquettes : « 08h00 ». */
    public static function formater(mixed $heure): ?string
    {
        if ($heure === null || $heure === '') {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($heure)->format('H\\hi');
    }

    public function ouverture(): ?string
    {
        return self::formater($this->heure_ouverture);
    }

    public function fermeture(): ?string
    {
        return self::formater($this->heure_fermeture);
    }

    /** Ouverte 24h/24 ce jour-là (garde continue). */
    public function estContinu(): bool
    {
        return $this->ouvert && $this->ouverture() === '00h00' && in_array($this->fermeture(), ['23h59', '00h00'], true);
    }

    /** « 08h00 – 20h00 », « 24h / 24 » ou « Fermé ». */
    public function plage(): string
    {
        if (! $this->ouvert) {
            return 'Fermé';
        }

        return $this->estContinu() ? '24h / 24' : $this->ouverture().' – '.$this->fermeture();
    }

    public function jourLabel(): string
    {
        return self::jours()[$this->jour] ?? '';
    }
}

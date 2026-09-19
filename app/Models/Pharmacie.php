<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pharmacie extends Model
{
    /** @use HasFactory<\Database\Factories\PharmacieFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'nom', 'description', 'adresse', 'ville', 'quartier',
        'latitude', 'longitude', 'photo', 'document', 'statut',
        'frais_livraison', 'on_livraison', 'note_moyenne', 'nb_avis',
    ];

    protected function casts(): array
    {
        return [
            'on_livraison' => 'boolean',
            'note_moyenne' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function horaires(): HasMany
    {
        return $this->hasMany(Horaire::class);
    }

    public function horaireDuJour(int $jour): ?Horaire
    {
        return $this->horaires->firstWhere('jour', $jour);
    }

    public function medicaments(): BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, 'pharmacie_medicament')
            ->withPivot(['quantite', 'prix', 'date_peremption', 'seuil_stock_bas', 'id'])
            ->withTimestamps();
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(PharmacieMedicament::class);
    }

    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    /** Ouverte maintenant ? (fuseau Africa/Douala, horaires hebdo) */
    public function estOuverte(?\Illuminate\Support\Carbon $a = null): bool
    {
        $a ??= now();
        $h = $this->horaireDuJour($a->dayOfWeek);

        if (! $h || ! $h->ouvert) {
            return false;
        }

        return $a->between($a->copy()->startOfDay()->setTimeFromTimeString($h->heure_ouverture), $a->copy()->startOfDay()->setTimeFromTimeString($h->heure_fermeture));
    }

    /** Statut affiché : Ouvert / Fermé */
    public function statutOuverture(): string
    {
        return $this->estOuverte() ? 'Ouvert' : 'Fermé';
    }
}

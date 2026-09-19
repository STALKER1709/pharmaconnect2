<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Panier extends Model
{
    protected $fillable = ['client_id'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LignePanier::class);
    }

    public function medicaments(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Medicament::class, 'ligne_paniers')
            ->withPivot('quantite')
            ->using(LignePanier::class)
            ->withTimestamps();
    }

    public function nbArticles(): int
    {
        return (int) $this->lignes()->sum('quantite');
    }

    public function sousTotal(): int
    {
        return (int) $this->lignes()
            ->with('medicament')
            ->get()
            ->sum(fn (LignePanier $ligne) => $ligne->medicament->prix * $ligne->quantite);
    }

    /** Regroupe les lignes par pharmacie : une commande = une pharmacie. */
    public function lignesParPharmacie(): \Illuminate\Support\Collection
    {
        return $this->lignes()
            ->with('medicament.pharmacie')
            ->get()
            ->groupBy(fn (LignePanier $ligne) => $ligne->medicament->pharmacie_id);
    }

    public function contientDesPerimes(): bool
    {
        return $this->lignes()->with('medicament')->get()
            ->contains(fn (LignePanier $ligne) => $ligne->medicament->estPerime());
    }
}

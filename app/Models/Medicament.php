<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicament extends Model
{
    /** @use HasFactory<\Database\Factories\MedicamentFactory> */
    use HasFactory;

    protected $fillable = [
        'categorie_id', 'nom', 'slug', 'reference', 'description',
        'posologie', 'ordonnance_obligatoire', 'photo', 'fabricant',
        'forme', 'dosage_mg', 'actif',
    ];

    protected function casts(): array
    {
        return [
            'ordonnance_obligatoire' => 'boolean',
            'actif' => 'boolean',
            'dosage_mg' => 'integer',
        ];
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    /** Pharmacies qui proposent ce médicament (avec stock/prix via pivot). */
    public function pharmacies(): BelongsToMany
    {
        return $this->belongsToMany(Pharmacie::class, 'pharmacie_medicament')
            ->withPivot(['quantite', 'prix', 'date_peremption', 'seuil_stock_bas'])
            ->withTimestamps();
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeLigne::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /** Note moyenne des avis sur ce médicament. */
    public function noteMoyenne(): float
    {
        return round((float) $this->avis()->avg('note'), 1);
    }
}

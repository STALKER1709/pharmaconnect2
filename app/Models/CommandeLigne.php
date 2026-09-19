<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Ligne d'une commande : médicament, quantité, prix unitaire figé. */
class CommandeLigne extends Model
{
    protected $table = 'commande_lignes';

    protected $fillable = [
        'commande_id', 'medicament_id', 'pharmacie_id', 'nom_medicament',
        'prix_unitaire', 'quantite', 'sous_total',
    ];

    protected function casts(): array
    {
        return ['prix_unitaire' => 'integer', 'quantite' => 'integer', 'sous_total' => 'integer'];
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    /** Sous-total de la ligne (fallback sur prix × quantité si non dénormalisé). */
    public function sousTotal(): int
    {
        return $this->sous_total ?? ($this->prix_unitaire * $this->quantite);
    }
}

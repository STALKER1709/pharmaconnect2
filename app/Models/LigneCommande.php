<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneCommande extends Model
{
    protected $fillable = [
        'commande_id',
        'medicament_id',
        'quantite',
        'prix_unitaire',
    ];

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function sousTotal(): int
    {
        return $this->prix_unitaire * $this->quantite;
    }
}

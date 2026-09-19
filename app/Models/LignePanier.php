<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([\App\Observers\LignePanierObserver::class])]
class LignePanier extends Model
{
    protected $table = 'ligne_paniers';

    protected $fillable = [
        'panier_id',
        'medicament_id',
        'quantite',
    ];

    public function panier(): BelongsTo
    {
        return $this->belongsTo(Panier::class);
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function sousTotal(): int
    {
        return $this->medicament->prix * $this->quantite;
    }
}

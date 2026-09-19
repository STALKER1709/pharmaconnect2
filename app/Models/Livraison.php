<?php

namespace App\Models;

use App\Enums\LivraisonStatut;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livraison extends Model
{
    protected $fillable = [
        'commande_id', 'livreur_id', 'statut',
        'latitude_depart', 'longitude_depart',
        'latitude_arrivee', 'longitude_arrivee',
        'distance_km', 'duree_estimee_min',
        'acceptee_at', 'en_route_at', 'arrivee_at', 'livree_at',
    ];

    protected function casts(): array
    {
        return [
            'statut' => LivraisonStatut::class,
            'acceptee_at' => 'datetime',
            'en_route_at' => 'datetime',
            'arrivee_at' => 'datetime',
            'livree_at' => 'datetime',
            'latitude_depart' => 'float',
            'longitude_depart' => 'float',
            'latitude_arrivee' => 'float',
            'longitude_arrivee' => 'float',
        ];
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PositionLivreur::class);
    }

    public function dernierePosition(): ?PositionLivreur
    {
        return $this->positions()->latest('signalee_at')->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Livreur extends Model
{
    /** @use HasFactory<\Database\Factories\LivreurFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'ville', 'vehicule', 'immatriculation', 'document',
        'statut', 'disponibilite', 'note_moyenne', 'nb_avis',
        'latitude', 'longitude', 'derniere_position_at',
    ];

    protected function casts(): array
    {
        return [
            'note_moyenne' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'derniere_position_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function livraisons(): HasMany
    {
        return $this->hasMany(Livraison::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(PositionLivreur::class);
    }

    public function vehiculeLabel(): string
    {
        return match ($this->vehicule) {
            'moto' => 'Moto',
            'voiture' => 'Voiture',
            'velo' => 'Vélo',
            default => ucfirst($this->vehicule),
        };
    }
}

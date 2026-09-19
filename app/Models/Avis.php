<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Avis extends Model
{
    protected $fillable = [
        'client_id', 'commande_id', 'pharmacie_id', 'medicament_id',
        'livreur_id', 'note', 'commentaire',
    ];

    protected function casts(): array
    {
        return ['note' => 'integer'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class);
    }

    public function pieceJointable(): MorphTo
    {
        return $this->morphTo();
    }

    public function cibleLabel(): string
    {
        return match (true) {
            $this->pharmacie_id !== null => 'Pharmacie : '.($this->pharmacie?->nom ?? '—'),
            $this->medicament_id !== null => 'Médicament : '.($this->medicament?->nom ?? '—'),
            $this->livreur_id !== null => 'Livreur : '.($this->livreur?->user?->name ?? '—'),
            default => 'Service',
        };
    }

    /** Étoiles HTML ★★★☆☆ */
    public function etoiles(): string
    {
        return str_repeat('★', $this->note).str_repeat('☆', max(0, 5 - $this->note));
    }
}

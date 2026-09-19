<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionLivreur extends Model
{
    public $timestamps = false;

    protected $fillable = ['livreur_id', 'livraison_id', 'latitude', 'longitude', 'signalee_at'];

    protected function casts(): array
    {
        return [
            'signalee_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class);
    }

    public function livraison(): BelongsTo
    {
        return $this->belongsTo(Livraison::class);
    }
}

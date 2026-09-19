<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PieceJointe extends Model
{
    protected $fillable = ['piece_jointable_id', 'piece_jointable_type', 'chemin', 'nom_original', 'type'];

    public function pieceJointable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return \Storage::disk('public')->url($this->chemin);
    }
}

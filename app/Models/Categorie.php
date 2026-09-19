<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $fillable = ['nom', 'slug', 'icone', 'description'];

    public function medicaments(): HasMany
    {
        return $this->hasMany(Medicament::class);
    }
}

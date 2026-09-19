<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicamentReference extends Model
{
    protected $table = 'medicaments_reference';

    protected $fillable = [
        'nom',
        'description',
        'sur_ordonnance',
    ];

    protected function casts(): array
    {
        return [
            'sur_ordonnance' => 'boolean',
        ];
    }

    public function medicaments(): HasMany
    {
        return $this->hasMany(Medicament::class, 'nom', 'nom');
    }
}

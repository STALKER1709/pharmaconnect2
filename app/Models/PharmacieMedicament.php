<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne de stock d'une pharmacie (pivot enrichi).
 */
class PharmacieMedicament extends Model
{
    protected $table = 'pharmacie_medicament';

    protected $fillable = [
        'pharmacie_id', 'medicament_id', 'quantite', 'prix',
        'date_peremption', 'seuil_stock_bas',
    ];

    protected function casts(): array
    {
        return ['date_peremption' => 'date'];
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    public function medicament(): BelongsTo
    {
        return $this->belongsTo(Medicament::class);
    }

    public function estEnStock(): bool
    {
        return $this->quantite > 0
            && ($this->date_peremption === null || $this->date_peremption->isFuture());
    }

    public function stockBas(): bool
    {
        return $this->quantite > 0 && $this->quantite <= $this->seuil_stock_bas;
    }

    public function peremptionProche(int $mois = 3): bool
    {
        return $this->date_peremption !== null
            && $this->date_peremption->isFuture()
            && $this->date_peremption->lte(now()->addMonths($mois));
    }
}

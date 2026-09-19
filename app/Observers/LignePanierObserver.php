<?php

namespace App\Observers;

use App\Models\LignePanier;
use App\Models\Medicament;

class LignePanierObserver
{
    public function saving(LignePanier $lignePanier): void
    {
        $medicament = Medicament::find($lignePanier->medicament_id);

        if ($medicament !== null && $lignePanier->quantite > $medicament->quantite_stock) {
            $lignePanier->quantite = max(0, $medicament->quantite_stock);
        }
    }

    public function saved(LignePanier $lignePanier): void
    {
        if ((int) $lignePanier->quantite === 0) {
            $lignePanier->delete();
        }
    }
}

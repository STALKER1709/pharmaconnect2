<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Medicament;

class MedicamentPolicy
{
    public function viewAny(Client $user): bool
    {
        return true;
    }

    /** Le client peut consulter la fiche d'un médicament. */
    public function view(Client $user, Medicament $medicament): bool
    {
        return true;
    }
}

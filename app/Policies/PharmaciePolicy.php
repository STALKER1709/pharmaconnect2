<?php

namespace App\Policies;

use App\Models\Pharmacie;
use App\Models\User;

class PharmaciePolicy
{
    public function update(User $user, Pharmacie $pharmacie): bool
    {
        return $user->estPharmacie() && $pharmacie->user_id === $user->id;
    }

    public function manageStock(User $user, Pharmacie $pharmacie): bool
    {
        return $this->update($user, $pharmacie);
    }
}

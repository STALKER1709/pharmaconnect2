<?php

namespace App\Policies;

use App\Enums\TypeAvis;
use App\Models\Avis;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Medicament;
use App\Models\Pharmacie;
use App\Enums\StatutCommande;
use Illuminate\Auth\Access\Response;

class AvisPolicy
{
    /** Un avis ne peut être laissé qu'après une commande livrée. */
    private function commandeLivree(Client $user, int $pharmacieId): bool
    {
        return Commande::query()
            ->where('client_id', $user->id)
            ->where('pharmacie_id', $pharmacieId)
            ->where('statut', StatutCommande::Livree)
            ->exists();
    }

    public function createPharmacie(Client $user, Pharmacie $pharmacie): Response
    {
        if (! $this->commandeLivree($user, $pharmacie->id)) {
            return Response::deny("Vous pouvez laisser un avis sur une pharmacie après avoir reçu au moins une commande d'elle.");
        }

        return Response::allow();
    }

    public function createMedicament(Client $user, Medicament $medicament): Response
    {
        if (! $this->commandeLivree($user, $medicament->pharmacie_id)) {
            return Response::deny('Vous pouvez noter un médicament après avoir reçu une commande la contenant.');
        }

        return Response::allow();
    }

    /** Le client peut modifier/supprimer ses propres avis. */
    public function update(Client $user, Avis $avis): bool
    {
        return $avis->client_id === $user->id;
    }

    public function delete(Client $user, Avis $avis): bool
    {
        return $avis->client_id === $user->id;
    }
}

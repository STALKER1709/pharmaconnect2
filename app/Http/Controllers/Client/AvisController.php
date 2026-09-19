<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Avis;
use App\Models\Commande;
use App\Models\Medicament;
use App\Models\Pharmacie;
use App\Enums\TypeAvis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

class AvisController extends Controller
{
    /**
     * Formulaire de dépôt d'avis : cible pharmacie ou médicament.
     * ?type=pharmacie&cible={id} ou ?type=medicament&cible={id}
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'pharmacie');
        $cibleId = (int) $request->query('cible', 0);

        abort_unless(in_array($type, ['pharmacie', 'medicament'], true), 404);
        abort_if($cibleId === 0, 404, 'Cible de l\'avis manquante.');

        if ($type === 'pharmacie') {
            $cible = Pharmacie::query()->with('user')->findOrFail($cibleId);
            Gate::authorize('createPharmacie', [Avis::class, $cible]);
        } else {
            $cible = Medicament::query()->with('pharmacie')->findOrFail($cibleId);
            Gate::authorize('createMedicament', [Avis::class, $cible]);
        }

        $existant = Avis::query()
            ->where('client_id', Auth::user()->client->id)
            ->where('avirable_type', $type === 'pharmacie' ? Pharmacie::class : Medicament::class)
            ->where('avirable_id', $cibleId)
            ->first();

        return view('client.avis-creer', [
            'type' => $type,
            'cible' => $cible,
            'existant' => $existant,
        ]);
    }

    /** Enregistre (ou met à jour) l'avis avec note 1–5 + commentaire. */
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'type' => ['required', 'in:pharmacie,medicament'],
            'cible_id' => ['required', 'integer'],
            'note' => ['required', 'integer', 'between:1,5'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ]);

        $client = Auth::user()->client;

        if ($donnees['type'] === 'pharmacie') {
            $cible = Pharmacie::findOrFail($donnees['cible_id']);
            Gate::authorize('createPharmacie', [Avis::class, $cible]);
            $typeAvis = TypeAvis::Service;
            $classeCible = Pharmacie::class;
        } else {
            $cible = Medicament::findOrFail($donnees['cible_id']);
            Gate::authorize('createMedicament', [Avis::class, $cible]);
            $typeAvis = TypeAvis::Medicament;
            $classeCible = Medicament::class;
        }

        DB::transaction(function () use ($donnees, $client, $cible, $typeAvis, $classeCible) {
            Avis::query()->updateOrCreate(
                [
                    'client_id' => $client->id,
                    'avirable_type' => $classeCible,
                    'avirable_id' => $cible->id,
                ],
                [
                    'type' => $typeAvis,
                    'note' => (int) $donnees['note'],
                    'commentaire' => $donnees['commentaire'],
                ],
            );
        });

        return back()->with('succes', 'Merci pour votre avis !');
    }

    public function destroy(Avis $avis)
    {
        Gate::authorize('delete', $avis);
        $avis->delete();

        return back()->with('succes', 'Avis supprimé.');
    }
}

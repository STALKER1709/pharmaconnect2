<?php

namespace App\Http\Controllers\Pharmacie;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HoraireController extends Controller
{
    public function edit(Request $request): View
    {
        $pharmacie = $request->user()->pharmacie->load('horaires');

        return view('pharmacie.horaires', [
            'pharmacie' => $pharmacie,
            'jours' => \App\Models\Horaire::jours(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $pharmacie = $request->user()->pharmacie;

        $validated = $request->validate([
            'jours' => ['required', 'array', 'size:7'],
            'jours.*.ouvert' => ['required', 'boolean'],
            'jours.*.heure_ouverture' => ['nullable', 'date_format:H:i'],
            'jours.*.heure_fermeture' => ['nullable', 'date_format:H:i', 'after:jours.*.heure_ouverture'],
        ]);

        foreach ($validated['jours'] as $jour => $data) {
            $pharmacie->horaires()->updateOrCreate(
                ['jour' => $jour],
                [
                    'ouvert' => (bool) $data['ouvert'],
                    'heure_ouverture' => $data['heure_ouverture'] ?? '08:00',
                    'heure_fermeture' => $data['heure_fermeture'] ?? '20:00',
                ]
            );
        }

        // Statut manuel : disponible pour la livraison ou fermé
        $pharmacie->update([
            'on_livraison' => $request->boolean('on_livraison'),
        ]);

        return back()->with('succes', 'Horaires et statut mis à jour.');
    }
}

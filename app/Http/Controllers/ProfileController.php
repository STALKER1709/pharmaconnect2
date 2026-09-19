<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

/**
 * Profil de l'utilisateur connecté (tous rôles) :
 * informations communes + profil métier 1-1 + mot de passe.
 */
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        // Charge le profil métier 1-1 (pas de relation vide pour les admins)
        if ($user->estClient()) {
            $user->load('client');
        } elseif ($user->estPharmacie()) {
            $user->load('pharmacie.horaires');
        } elseif ($user->estLivreur()) {
            $user->load('livreur');
        }

        return view('profil.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'regex:/^(237)?6\d{8}$/'],
            // Client
            'adresse' => ['nullable', 'string', 'max:255'],
            'quartier' => ['nullable', 'string', 'max:120'],
            'ville' => ['nullable', 'string', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            // Pharmacie
            'description' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'frais_livraison' => ['nullable', 'integer', 'min:0', 'max:50000'],
            // Livreur
            'vehicule' => ['nullable', 'in:moto,voiture,velo'],
            'immatriculation' => ['nullable', 'string', 'max:20'],
        ], [
            'telephone.regex' => 'Numéro camerounais attendu (ex. 690123456).',
        ]);

        $user->update([
            'name' => $validated['name'],
            'telephone' => preg_replace('/\D/', '', $validated['telephone'] ?? '') ?: $user->telephone,
        ]);

        $profil = $user->profil();

        if ($profil) {
            $champs = collect($validated)->only([
                'adresse', 'quartier', 'ville', 'latitude', 'longitude',
                'description', 'frais_livraison', 'vehicule', 'immatriculation',
            ])->filter(fn ($v) => $v !== null)->all();

            if ($request->hasFile('photo') && $user->estPharmacie()) {
                if ($profil->photo) {
                    Storage::disk('public')->delete($profil->photo);
                }
                $champs['photo'] = $request->file('photo')->store('pharmacies', 'public');
            }

            $profil->update($champs);
        }

        return back()->with('succes', 'Profil mis à jour.');
    }

    /** Mise à jour du mot de passe (formulaire dédié du profil). */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('succes', 'Mot de passe mis à jour.');
    }
}

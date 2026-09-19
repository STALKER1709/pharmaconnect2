<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Livreur;
use App\Models\Pharmacie;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Inscription : le client est actif immédiatement ; les comptes
     * pharmacie et livreur restent « en_attente » jusqu'à validation admin.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telephone' => ['required', 'string', 'regex:/^(237)?6\d{8}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:client,pharmacie,livreur'],

            // Champs conditionnels pharmacie
            'nom_pharmacie' => ['required_if:role,pharmacie', 'nullable', 'string', 'max:255'],
            'adresse_pharmacie' => ['required_if:role,pharmacie', 'nullable', 'string', 'max:255'],

            // Champs conditionnels livreur
            'vehicule' => ['required_if:role,livreur', 'nullable', 'in:moto,voiture,velo'],
            'immatriculation' => ['nullable', 'string', 'max:20'],
        ], [
            'telephone.regex' => 'Le numéro doit être un numéro camerounais valide (ex. 690123456).',
            'nom_pharmacie.required_if' => 'Le nom de la pharmacie est requis.',
            'adresse_pharmacie.required_if' => 'L\'adresse de la pharmacie est requise.',
            'vehicule.required_if' => 'Le type de véhicule est requis.',
        ]);

        $role = UserRole::from($request->role);

        $user = DB::transaction(function () use ($request, $role) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'telephone' => preg_replace('/\D/', '', $request->telephone),
                'password' => Hash::make($request->password),
                'role' => $role->value,
                // Client actif direct ; pharmacie et livreur attendent la validation admin
                'statut' => $role === UserRole::Client ? 'actif' : 'en_attente',
            ]);

            if ($role === UserRole::Client) {
                Client::create([
                    'user_id' => $user->id,
                    'ville' => 'Douala',
                ]);
            }

            if ($role === UserRole::Pharmacie) {
                $pharmacie = Pharmacie::create([
                    'user_id' => $user->id,
                    'nom' => $request->nom_pharmacie,
                    'adresse' => $request->adresse_pharmacie,
                    'ville' => 'Douala',
                    'statut' => 'en_attente',
                    'frais_livraison' => (int) config('app.frais_livraison_defaut', 1000),
                ]);

                // Horaires par défaut : lun–sam 08h–20h, dimanche fermé
                foreach ([1, 2, 3, 4, 5, 6] as $jour) {
                    $pharmacie->horaires()->create(['jour' => $jour]);
                }
            }

            if ($role === UserRole::Livreur) {
                Livreur::create([
                    'user_id' => $user->id,
                    'ville' => 'Douala',
                    'vehicule' => $request->vehicule,
                    'immatriculation' => $request->immatriculation,
                    'statut' => 'en_attente',
                    'disponibilite' => 'hors_ligne',
                ]);
            }

            return $user;
        });

        event(new Registered($user));

        // Ne connecte pas les comptes en attente : on explique la validation
        if ($user->statut === 'en_attente') {
            return redirect()->route('login')->with('statut', 'inscription_en_attente');
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

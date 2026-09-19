<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    /** Enregistre l'adresse / la position du client depuis son profil. */
    public function __invoke(Request $request)
    {
        $client = Auth::user()->client;

        $donnees = $request->validate([
            'adresse' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $client->update($donnees);

        return back()->with('succes', 'Votre position a été enregistrée.');
    }
}

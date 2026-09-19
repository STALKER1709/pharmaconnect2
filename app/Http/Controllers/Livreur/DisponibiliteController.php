<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisponibiliteController extends Controller
{
    public function update(Request $request)
    {
        $livreur = Auth::user()->livreur;

        $donnees = $request->validate([
            'disponible' => ['required', 'boolean'],
        ]);

        $livreur->update(['disponible' => $donnees['disponible']]);

        return back()->with('succes', $donnees['disponible'] ? 'Vous êtes maintenant disponible.' : 'Vous êtes indisponible.');
    }
}

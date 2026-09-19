<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class CompteEnAttenteController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        if (! $user || ! $user->isEnAttente()) {
            return redirect()->to($user?->homeRoute() ?? route('login'));
        }

        return view('compte.en-attente', ['user' => $user]);
    }
}

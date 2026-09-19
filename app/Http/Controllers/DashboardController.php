<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Redirige chaque rôle vers son tableau de bord. */
    public function index(Request $request)
    {
        return match ($request->user()->role) {
            'pharmacie' => redirect()->route('pharmacie.dashboard'),
            'livreur' => redirect()->route('livreur.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => app(\App\Http\Controllers\Client\DashboardController::class)->index($request),
        };
    }
}

<?php

namespace App\Http\Controllers;

use App\Contracts\TelephonyGateway;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Cas « Recevoir / effectuer un appel » — spécialisation du chat :
 * l'implémentation par défaut (TelephonyGatewayTel) renvoie un lien tel:.
 */
class TelephonieController extends Controller
{
    public function __invoke(Request $request, User $user, TelephonyGateway $telephonie): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 422, 'Vous ne pouvez pas vous appeler.');
        abort_if(empty($user->telephone), 422, 'Cet utilisateur n\'a pas de numéro.');

        $appel = $telephonie->appel($user->telephone ?? '');

        return back()->with('appel', $appel);
    }
}

<?php

namespace App\Http\Controllers;

use App\Contracts\TelephonyGateway;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/** Cas « Recevoir / effectuer un appel » : le mock renvoie un lien tel:. */
class TelephonieController extends Controller
{
    public function __invoke(User $user, TelephonyGateway $telephonie)
    {
        abort_if($user->id === Auth::id(), 422, 'Vous ne pouvez pas vous appeler.');
        abort_if(empty($user->telephone), 422, 'Cet utilisateur n\'a pas de numéro.');

        $appel = $telephonie->appelVers($user->telephone);

        return back()->with('appel', $appel);
    }
}

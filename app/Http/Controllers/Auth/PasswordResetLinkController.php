<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /** @throws \Illuminate\Validation\ValidationException */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // MAIL_MAILER=log en local : le lien est écrit dans storage/logs/laravel.log
        $statut = Password::sendResetLink(
            $request->only('email')
        );

        return $statut == Password::RESET_LINK_SENT
            ? back()->with('statut', __($statut))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($statut)]);
    }
}

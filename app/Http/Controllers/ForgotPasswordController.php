<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Affiche le formulaire "mot de passe oublié".
     */
    public function showLinkRequestForm()
    {
        return view('frontoffice.authentication.forgot-password');
    }

    /**
     * Envoie le lien de réinitialisation.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Envoi du lien via le système interne Laravel
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

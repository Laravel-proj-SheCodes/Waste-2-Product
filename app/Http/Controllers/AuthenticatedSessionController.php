<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function create()
    {
        // Ex: resources/views/frontoffice/authentication/login.blade.php
        return view('frontoffice.authentication.login');
    }

    /**
     * Traite la soumission du formulaire de connexion.
     */
    public function store(Request $request)
    {
        // 1) Valider les champs
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2) Tenter l'authentification
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // 3) Redirection selon le rôle
            $user = Auth::user();

            if (!$user->is_active) {
                $user->update(['is_active' => true]);
                $request->session()->put('welcome_back', true);
            }
            
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return redirect()->route('dashboard');
            }

            // Par défaut (client)
            return redirect()->route('home');
        }

        // 4) Échec de connexion
        return back()->withErrors([
            'email' => 'Identifiants incorrects.',
        ])->onlyInput('email');
    }

    /**
     * Affiche le formulaire d'inscription.
     */
    public function register()
    {
        // Ex: resources/views/frontoffice/authentication/register.blade.php
        return view('frontoffice.authentication.register');
    }

    /**
     * Traite l'inscription.
     */
    public function registerStore(Request $request)
    {
        // 1) Valider
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'min:6', 'confirmed'], // nécessite password_confirmation
            'role'                  => ['sometimes', 'in:client,admin'], // Optionnel, pour valider le rôle caché
        ]);

        // 2) Créer l'utilisateur
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'] ?? 'client', // Utilise le rôle du formulaire ou défaut
        ]);

        // 3) Envoyer l'email de vérification (via événement Laravel)
        event(new Registered($user));

        // 4) Rediriger vers login avec message (sans connecter l'utilisateur)
        return redirect()->route('login')->with('status', 'Un lien de vérification a été envoyé à votre email. Veuillez vérifier avant de vous connecter.');
    }

    /**
     * Déconnexion.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirection vers la page home
        return redirect()->route('home');
    }
}
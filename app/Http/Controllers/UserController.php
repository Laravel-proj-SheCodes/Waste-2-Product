<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of users (admin only).
     */
    public function index()
    {
        

        $users = User::paginate(10); // Paginate with 10 users per page
        return view('backoffice.pages.users.listusers', compact('users'));
    }

    /**
     * Display the specified user's details (admin only).
     */
    public function show(User $user)
    {
        

        return view('backoffice.pages.users.show', compact('user'));
    }

    /**
     * Toggle the user's account active status (admin only).
     */
    public function toggleActive(Request $request, User $user)
    {
        

        // Prevent admins from deactivating their own account
        if ($user->id === Auth::user()->id) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? 'Compte activé avec succès.' : 'Compte désactivé avec succès.';
        return back()->with('success', $message);
    }
}
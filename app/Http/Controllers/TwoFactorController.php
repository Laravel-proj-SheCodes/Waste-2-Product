<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA settings page
     */
    public function show()
    {
        $user = Auth::user();
        return view('backoffice.pages.profile.two-factor', compact('user'));
    }

    /**
     * Enable 2FA - Send verification code
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return back()->with('error', 'Two-factor authentication is already enabled.');
        }

        $user->generateAndSendTwoFactorCode();

        return back()->with('success', 'Verification code sent to your email. Please verify to enable 2FA.');
    }

    /**
     * Verify and enable 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if ($user->verifyTwoFactorCode($request->code)) {
            $user->update(['two_factor_enabled' => true]);
            return back()->with('success', 'Two-factor authentication has been enabled successfully!');
        }

        return back()->with('error', 'Invalid or expired verification code.');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->disableTwoFactor();

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show 2FA verification page during login
     */
    public function showVerify()
    {
        return view('auth.two-factor-verify');
    }

    /**
     * Verify 2FA code during login
     */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        if (!$user || !$user->two_factor_enabled) {
            return back()->with('error', 'Two-factor authentication is not enabled.');
        }

        if ($user->verifyTwoFactorCode($request->code)) {
            session(['two_factor_verified' => true]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Invalid or expired verification code.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    /**
     * Display a listing of donations.
     */
    public function index(Request $request)
    {
        $donations = Donation::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Donation::count(),
            'pending' => Donation::where('status', 'pending')->count(),
            'accepted' => Donation::where('status', 'accepted')->count(),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donations);
        }

        return view('backoffice.pages.donations.index', compact('donations', 'stats'));
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create()
    {
        return view('backoffice.pages.donations.create');
    }

    /**
     * Store a newly created donation.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login')->with('error', 'Authentification requise');
        }

        $request->validate([
            'location' => 'required|string',
            'product_name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:recyclable,renewable',
            'donation_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $donation = Donation::create([
            'user_id' => Auth::id(),
            'location' => $request->location,
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'description' => $request->description,
            'donation_date' => $request->donation_date,
            'status' => 'pending',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'), 201);
        }

        return redirect()->route('donations.index')->with('success', 'Donation submitted successfully!');
    }

    /**
     * Display the specified donation.
     */
    public function show(Donation $donation, Request $request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'));
        }

        return view('backoffice.pages.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            return redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        return view('backoffice.pages.donations.edit', compact('donation'));
    }

    /**
     * Update the specified donation.
     */
    public function update(Request $request, Donation $donation)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        if ($donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        $request->validate([
            'location' => 'sometimes|string',
            'product_name' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1',
            'type' => 'sometimes|in:recyclable,renewable',
            'donation_date' => 'sometimes|date',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:pending,accepted,rejected',
        ]);

        $donation->update($request->only(['location', 'product_name', 'quantity', 'type', 'description', 'donation_date', 'status']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'));
        }

        return redirect()->route('donations.index')->with('success', 'Donation mise à jour avec succès');
    }

    /**
     * Remove the specified donation.
     */
    public function destroy(Donation $donation, Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Authentification requise'], 401);
            }
            return redirect()->route('login');
        }

        if ($donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        $donation->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Donation supprimée avec succès']);
        }

        return redirect()->route('donations.index')->with('success', 'Donation supprimée avec succès');
    }

    /**
     * Display the user's donations.
     */
    public function mesDonations(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentification requise'], 401);
        }

        $donations = Donation::where('user_id', Auth::id())
            ->with('user')
            ->get();

        return response()->json($donations);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationRequest;
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

        // Check if submission came from frontoffice
        if ($request->input('from_front')) {
            return redirect()->route('donate.thankyou')->with('success', 'Donation submitted successfully!');
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
            'status' => 'sometimes|in:pending,accepted,rejected,taken',
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

    /**
     * Frontoffice landing page for donations.
     */
    public function frontLanding()
    {
        $acceptedDonations = $this->getAcceptedDonations();
        return view('frontoffice.pages.donations.donationpage', compact('acceptedDonations'));
    }

    /**
     * Frontoffice form for creating a new donation.
     */
    public function frontCreate()
    {
        return view('frontoffice.pages.donations.create');
    }

    /**
     * Frontoffice thank you page after donation.
     */
    public function frontThankyou()
    {
        return view('frontoffice.pages.donations.thankyou');
    }

    /**
     * Fetch accepted donations for the frontoffice donationpage page.
     */
    protected function getAcceptedDonations()
    {
        return Donation::where('status', 'accepted')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark a donation as taken by updating its status.
     */
    public function takeDonation(Donation $donation)
    {
        if (!Auth::check()) {
            return redirect()->route('donate.donationpage')->with('error', 'Authentification requise');
        }

        if ($donation->status !== 'accepted') {
            return redirect()->route('donate.donationpage')->with('error', 'This donation is not available to take.');
        }

        // Check if there are any accepted requests
        $acceptedRequest = DonationRequest::where('donation_id', $donation->id)
            ->where('status', 'accepted')
            ->first();

        if ($acceptedRequest) {
            return redirect()->route('donate.donationpage')->with('error', 'This donation has already been assigned via a request.');
        }

        $donation->update(['status' => 'taken', 'taken_by_user_id' => Auth::id()]);
        return redirect()->route('donate.donationpage')->with('success', 'Donation taken successfully!');
    }

    /**
     * Request a donation.
     */
    public function requestDonation(Request $request, Donation $donation)
    {
        if (!Auth::check()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Authentification requise'], 401)
                : redirect()->route('login')->with('error', 'Authentification requise');
        }

        if ($donation->user_id === Auth::id()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Vous ne pouvez pas demander votre propre don'], 403)
                : redirect()->route('donate.donationpage')->with('error', 'Vous ne pouvez pas demander votre propre don');
        }

        if ($donation->status !== 'accepted') {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Ce don n\'est pas disponible pour une demande'], 403)
                : redirect()->route('donate.donationpage')->with('error', 'Ce don n\'est pas disponible pour une demande');
        }

        // Check if user already requested this donation
        $existingRequest = DonationRequest::where('donation_id', $donation->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRequest) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Vous avez déjà demandé ce don'], 403)
                : redirect()->route('donate.donationpage')->with('error', 'Vous avez déjà demandé ce don');
        }

        $donationRequest = DonationRequest::create([
            'donation_id' => $donation->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return $request->wantsJson() || $request->ajax()
            ? response()->json($donationRequest->load('donation', 'user'), 201)
            : redirect()->route('donate.donationpage')->with('success', 'Demande de don envoyée avec succès !');
    }

    /**
     * Display donation requests for a donation (for the owner).
     */
    public function showRequests(Donation $donation, Request $request)
    {
        if (!Auth::check() || $donation->user_id !== Auth::id()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Non autorisé'], 403)
                : redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        $requests = DonationRequest::where('donation_id', $donation->id)
            ->with('user')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($requests);
        }

        return view('backoffice.pages.donations.requests', compact('donation', 'requests'));
    }

    /**
     * Accept a donation request.
     */
    public function acceptRequest(DonationRequest $donationRequest, Request $request)
    {
        if (!Auth::check() || $donationRequest->donation->user_id !== Auth::id()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Non autorisé'], 403)
                : redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        if ($donationRequest->status !== 'pending') {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Cette demande a déjà été traitée'], 403)
                : redirect()->route('donations.showRequests', $donationRequest->donation_id)->with('error', 'Cette demande a déjà été traitée');
        }

        $donationRequest->update(['status' => 'accepted']);
        $donationRequest->donation->update(['status' => 'taken', 'taken_by_user_id' => $donationRequest->user_id]);

        // Reject other pending requests for this donation
        DonationRequest::where('donation_id', $donationRequest->donation_id)
            ->where('id', '!=', $donationRequest->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return $request->wantsJson() || $request->ajax()
            ? response()->json(['message' => 'Demande acceptée avec succès'])
            : redirect()->route('donations.showRequests', $donationRequest->donation_id)->with('success', 'Demande acceptée avec succès');
    }

    /**
     * Reject a donation request.
     */
    public function rejectRequest(DonationRequest $donationRequest, Request $request)
    {
        if (!Auth::check() || $donationRequest->donation->user_id !== Auth::id()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Non autorisé'], 403)
                : redirect()->route('donations.index')->with('error', 'Non autorisé');
        }

        if ($donationRequest->status !== 'pending') {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Cette demande a déjà été traitée'], 403)
                : redirect()->route('donations.showRequests', $donationRequest->donation_id)->with('error', 'Cette demande a déjà été traitée');
        }

        $donationRequest->update(['status' => 'rejected']);

        return $request->wantsJson() || $request->ajax()
            ? response()->json(['message' => 'Demande rejetée avec succès'])
            : redirect()->route('donations.showRequests', $donationRequest->donation_id)->with('success', 'Demande rejetée avec succès');
    }

    /**
     * Display the user's donation requests.
     */
    public function myRequests(Request $request)
    {
        if (!Auth::check()) {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['error' => 'Authentification requise'], 401)
                : redirect()->route('login')->with('error', 'Authentification requise');
        }

        $requests = DonationRequest::where('user_id', Auth::id())
            ->with('donation', 'donation.user')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($requests);
        }

        return view('frontoffice.pages.donations.my_requests', compact('requests'));
    }
}
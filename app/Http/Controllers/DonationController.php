<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Services\AIDescriptionEnhancer;

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
            'rejected' => Donation::where('status', 'rejected')->count(),
            'taken' => Donation::where('status', 'taken')->count(),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'donations' => $donations,
                'stats' => $stats
            ]);
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
    public function store(StoreDonationRequest $request)
    {
        $enhancer = new AIDescriptionEnhancer();
        $description = $enhancer->enhance($request->description ?? '');

        $donation = Donation::create([
            'user_id' => Auth::id(),
            'location' => $request->location,
            'product_name' => $request->product_name,
            'quantity' => $request->quantity,
            'type' => $request->type,
            'description' => $description, // Enhanced version
            'donation_date' => $request->donation_date,
            'status' => 'pending',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'), 201);
        }

        if ($request->input('from_front')) {
            return redirect()->route('donate.thankyou')->with('success', 'Donation submitted successfully! Your description has been enhanced by AI.');
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

        if ($request->routeIs('donate.show')) {
            return view('frontoffice.pages.donations.show', compact('donation'));
        }

        return view('backoffice.pages.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation, Request $request)
    {
        if ($donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        if ($request->routeIs('donate.edit')) {
            return view('frontoffice.pages.donations.edit', compact('donation'));
        }

        return view('backoffice.pages.donations.edit', compact('donation'));
    }

    /**
     * Update the specified donation.
     */
    public function update(UpdateDonationRequest $request, Donation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        $donation->update($request->only(['location', 'product_name', 'quantity', 'type', 'description', 'donation_date', 'status']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'));
        }

        return redirect()->route('mes-donations')->with('success', 'Donation mise à jour avec succès');
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
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        $donation->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Donation supprimée avec succès']);
        }

        return redirect()->route('mes-donations')->with('success', 'Donation supprimée avec succès');
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
    public function frontLanding(Request $request)
    {
        $query = Donation::where('status', 'accepted')
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->input('search') . '%');
            Log::info('Applying search filter', [
                'search' => $request->input('search'),
            ]);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
            Log::info('Applying type filter', [
                'type' => $request->input('type'),
            ]);
        }

        $acceptedDonations = $query->get();

        Log::info('Fetched accepted donations', [
            'count' => $acceptedDonations->count(),
            'search' => $request->input('search'),
            'type' => $request->input('type'),
        ]);

        return view('frontoffice.pages.donations.donationpage', compact('acceptedDonations'));
    }

    /**
     * Display all donations created by the authenticated user.
     */
    public function myDonationsFront(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Authentification requise');
        }

        $query = Donation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->input('search') . '%');
            Log::info('Applying search filter for my donations', [
                'search' => $request->input('search'),
            ]);
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
            Log::info('Applying type filter for my donations', [
                'type' => $request->input('type'),
            ]);
        }

        $donations = $query->get();

        return view('frontoffice.pages.donations.my_donations', compact('donations'));
    }


    /**
     * Frontoffice form for creating a new donation.
     */
    public function frontCreate(Request $request)
    {
        $description = old('description', '');

        if ($request->filled('preview') && $request->filled('description')) {
            Log::info('[v0] Enhancement requested', [
                'original_description' => $request->description,
                'request_params' => $request->all()
            ]);
            
            try {
                $enhancer = new AIDescriptionEnhancer();
                $enhancedDescription = $enhancer->enhance($request->description);
                
                Log::info('[v0] Enhancement completed', [
                    'original' => $request->description,
                    'enhanced' => $enhancedDescription,
                    'changed' => $request->description !== $enhancedDescription
                ]);
                
                if ($request->description === $enhancedDescription) {
                    return redirect()->route('donate.create')
                        ->with('enhanced_description', $enhancedDescription)
                        ->with('warning', 'AI returned the same text. Try providing more details in your original description (e.g., "Used plastic bottles - clean, various sizes" instead of just "Used bottles plastic").');
                }
                
                $originalLength = strlen($request->description);
                $enhancedLength = strlen($enhancedDescription);
                $growthRatio = $enhancedLength / $originalLength;
                
                if ($growthRatio < 1.2) {
                    Log::warning('[v0] Enhancement was minimal', [
                        'growth_ratio' => $growthRatio,
                        'original_length' => $originalLength,
                        'enhanced_length' => $enhancedLength
                    ]);
                    
                    return redirect()->route('donate.create')
                        ->with('enhanced_description', $enhancedDescription)
                        ->with('warning', 'AI enhancement was minimal. Try adding more context to your description for better results.');
                }
                
                // Flash the enhanced description to session
                return redirect()->route('donate.create')
                    ->with('enhanced_description', $enhancedDescription)
                    ->with('success', 'Description enhanced successfully!');
                    
            } catch (\Exception $e) {
                Log::error('[v0] Enhancement failed in controller', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('donate.create')
                    ->with('error', 'Failed to enhance description: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // Check if we have an enhanced description from session
        if (session()->has('enhanced_description')) {
            $description = session('enhanced_description');
            Log::info('[v0] Using enhanced description from session', [
                'description' => $description
            ]);
        }

        return view('frontoffice.pages.donations.create', compact('description'));
    }


    /**
     * Frontoffice thank you page after donation.
     */
    public function frontThankyou()
    {
        return view('frontoffice.pages.donations.thankyou');
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
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        $requests = DonationRequest::where('donation_id', $donation->id)
            ->with('user')
            ->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($requests);
        }

        if ($request->routeIs('donate.showRequests')) {
            return view('frontoffice.pages.donations.requests', compact('donation', 'requests'));
        }

        return view('backoffice.pages.donations.requests', compact('donation', 'requests'));
    }

    /**
     * Accept a donation request.
     */
    public function acceptRequest(DonationRequest $donationRequest, Request $request)
    {
        if (!Auth::check() || $donationRequest->donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        if ($donationRequest->status !== 'pending') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Cette demande a déjà été traitée'], 403);
            }
            return redirect()->route('donate.showRequests', $donationRequest->donation_id)->with('error', 'Cette demande a déjà été traitée');
        }

        $donationRequest->update(['status' => 'accepted']);
        $donationRequest->donation->update(['status' => 'taken', 'taken_by_user_id' => $donationRequest->user_id]);

        DonationRequest::where('donation_id', $donationRequest->donation_id)
            ->where('id', '!=', $donationRequest->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Demande acceptée avec succès']);
        }

        return redirect()->route('donate.showRequests', $donationRequest->donation_id)->with('success', 'Demande acceptée avec succès');
    }

    /**
     * Reject a donation request.
     */
    public function rejectRequest(DonationRequest $donationRequest, Request $request)
    {
        if (!Auth::check() || $donationRequest->donation->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }
            return redirect()->route('mes-donations')->with('error', 'Non autorisé');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['error' => 'Cette demande a déjà été traitée'], 403);
        }
        return redirect()->route('donate.showRequests', $donationRequest->donation_id)->with('error', 'Cette demande a déjà été traitée');
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

        $query = DonationRequest::where('user_id', Auth::id())
            ->with('donation', 'donation.user')
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $query->whereHas('donation', function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->input('search') . '%');
            });
            Log::info('Applying search filter for my requests', [
                'search' => $request->input('search'),
            ]);
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->whereHas('donation', function ($q) use ($request) {
                $q->where('type', $request->input('type'));
            });
            Log::info('Applying type filter for my requests', [
                'type' => $request->input('type'),
            ]);
        }

        $requests = $query->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($requests);
        }

        return view('frontoffice.pages.donations.my_requests', compact('requests'));
    }
}
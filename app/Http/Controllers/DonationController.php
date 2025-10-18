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
use Barryvdh\DomPDF\Facade\Pdf;

class DonationController extends Controller
{
    /**
     * Display a listing of donations.
     */
    public function index(Request $request)
    {
        try {
            // Base query for all donations (for stats and advanced stats)
            $baseQuery = Donation::with('user')->orderBy('created_at', 'desc');

            // Apply filters to the base query
            if ($request->filled('search')) {
                $baseQuery->where('product_name', 'like', '%' . $request->input('search') . '%');
                Log::info('Applying search filter for donations index', [
                    'search' => $request->input('search'),
                ]);
            }

            if ($request->filled('type') && strtolower($request->input('type')) !== 'all') {
                $type = strtolower(trim($request->input('type')));
                if (in_array($type, ['recucable', 'recycle', 'recyclable'])) $type = 'recyclable';
                elseif (in_array($type, ['renewable', 'renouvelable'])) $type = 'renewable';
                $baseQuery->whereRaw('LOWER(type) = ?', [$type]);
            }

            // Fetch all matching donations for stats (no pagination)
            $allDonations = $baseQuery->get();

            // Calculate stats based on all matching donations
            $stats = [
                'total' => $allDonations->count(),
                'pending' => $allDonations->where('status', 'pending')->count(),
                'accepted' => $allDonations->where('status', 'accepted')->count(),
                'rejected' => $allDonations->where('status', 'rejected')->count(),
                'taken' => $allDonations->where('status', 'taken')->count(),
            ];

            // Calculate advanced stats
            $advancedStats = [
                'total_quantity' => $allDonations->sum('quantity'),
                'average_quantity' => $allDonations->count() > 0 ? round($allDonations->avg('quantity'), 2) : 0,
                'unique_donors' => $allDonations->unique('user_id')->count(),
                'recyclable_count' => $allDonations->where('type', 'recyclable')->count(),
                'renewable_count' => $allDonations->where('type', 'renewable')->count(),
                'completion_rate' => $stats['total'] > 0 ? round((($stats['accepted'] + $stats['taken']) / $stats['total']) * 100, 1) : 0,
                'most_common_type' => $allDonations->groupBy('type')->map->count()->sortDesc()->keys()->first() ?? 'N/A',
                'average_days_to_completion' => $this->calculateAverageDaysToCompletion($allDonations),
            ];

            // Separate query for paginated donations (for the table)
            $tableQuery = clone $baseQuery;
            $donations = $tableQuery->paginate(2);

            if ($request->wantsJson() || $request->ajax()) {
                Log::info('AJAX response', [
                    'donations_count' => $donations->count(),
                    'stats' => $stats,
                    'advancedStats' => $advancedStats
                ]);
                return response()->json([
                    'donations' => $donations,
                    'stats' => $stats,
                    'advancedStats' => $advancedStats
                ]);
            }

            return view('backoffice.pages.donations.index', compact('donations', 'stats', 'advancedStats'));
        } catch (\Exception $e) {
            Log::error('Error in donations index: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'An error occurred. Please try again.'], 500);
            }
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    private function calculateAverageDaysToCompletion($donations)
    {
        $completedDonations = $donations->whereIn('status', ['accepted', 'taken']);
        
        if ($completedDonations->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($completedDonations as $donation) {
            $createdDate = \Carbon\Carbon::parse($donation->created_at);
            $updatedDate = \Carbon\Carbon::parse($donation->updated_at);
            $days = $createdDate->diffInDays($updatedDate);
            $totalDays += $days;
            $count++;
        }

        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation, Request $request)
    {
        Log::info('[v0] Edit method called', [
            'route_name' => $request->route()->getName(),
            'donation_id' => $donation->id,
            'donation_user_id' => $donation->user_id,
            'auth_id' => Auth::id(),
        ]);

        if ($request->route()->getName() === 'donations.backedit') {
            return view('backoffice.pages.donations.edit', compact('donation'));
        }

        // Determine the context based on the route name
        if ($request->route()->getName() === 'donate.edit') {
            return view('frontoffice.pages.donations.edit', compact('donation'));
        }

        return view('backoffice.pages.donations.edit', compact('donation'));
    }

    /**
     * Update the specified donation (frontoffice - with authorization check).
     */
    public function update(UpdateDonationRequest $request, Donation $donation)
    {
        if ($request->route()->getName() === 'donations.backupdate') {
            $donation->update($request->only(['location', 'product_name', 'quantity', 'type', 'description', 'donation_date', 'status']));

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($donation->load('user'));
            }

            return redirect()->route('donations.index')->with('success', 'Donation mise à jour avec succès');
        }

        // Frontoffice update - authorization check required
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
     * New method for backoffice updates - no authorization check needed for admins
     * Update the specified donation (backoffice - without authorization check).
     */
    public function backupdate(UpdateDonationRequest $request, Donation $donation)
    {
        Log::info('[v0] Backupdate method called', [
            'donation_id' => $donation->id,
            'donation_user_id' => $donation->user_id,
            'auth_id' => Auth::id(),
        ]);

        $donation->update($request->only(['location', 'product_name', 'quantity', 'type', 'description', 'donation_date', 'status']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($donation->load('user'));
        }

        return redirect()->route('donations.index')->with('success', 'Donation mise à jour avec succès');
    }

    /**
     * Update the specified donation (frontoffice - with authorization check).
     */
    public function updatefront(UpdateDonationRequest $request, Donation $donation)
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

        $donation->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Donation supprimée avec succès']);
        }

        return redirect()->route('donations.index')->with('success', 'Donation supprimée avec succès');
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

    public function exportPdf(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $donations = Donation::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('product_name', 'like', "%{$search}%");
            })
            ->when($type && $type !== 'all', function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->get();

        $data = [
            'donations' => $donations,
            'search' => $search,
            'type' => $type,
            'date' => now()->format('Y-m-d H:i:s'),
        ];

        $pdf = Pdf::loadView('pdf.donations', $data);

        return $pdf->download('donations_list_' . now()->format('YmdHis') . '.pdf');
    }
}

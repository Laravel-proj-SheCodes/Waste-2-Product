<?php

namespace App\Http\Controllers;

use App\Models\PropositionTransformation;
use App\Models\ProcessusTransformation;
use App\Models\Proposition;
use App\Models\PostDechet;
use App\Models\User;
use App\Http\Requests\StorePropositionTransformationRequest;
use App\Http\Requests\UpdatePropositionTransformationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PropositionTransformationController extends Controller
{
    /* =========================
    |   BACKOFFICE METHODS
    * ========================= */
    
    public function index(){
        $propositions = PropositionTransformation::with(['proposition.postDechet','transformateur'])
            ->latest()
            ->paginate(10);

        return view('backoffice.pages.transformation.propositions.index', compact('propositions'));
    }

    public function create(){
        $posts = Proposition::with('postDechet')->get();
        $transformateurs = User::where('role','transformateur')->pluck('name','id');

        return view('backoffice.pages.transformation.propositions.create', compact('posts','transformateurs'));
    }

    public function store(StorePropositionTransformationRequest $request){
        $data = $request->validated();
        $data['transformateur_id'] = $data['transformateur_id'] ?? 1;

        PropositionTransformation::create($data);
        return redirect()->route('proposition-transformations.index')->with('ok','Proposition Transformation créée');
    }

    public function show(PropositionTransformation $propositionTransformation){
        $propositionTransformation->load(['proposition.postDechet','transformateur']);
        return view('backoffice.pages.transformation.propositions.show', compact('propositionTransformation'));
    }

    public function edit(PropositionTransformation $propositionTransformation){
        $posts = Proposition::with('postDechet')->get();
        $transformateurs = User::where('role','Transformateur')->pluck('name','id');
        return view('backoffice.pages.transformation.propositions.edit', compact('propositionTransformation','posts','transformateurs'));
    }

    public function update(UpdatePropositionTransformationRequest $request, PropositionTransformation $propositionTransformation){
        $data = $request->validated();
        $propositionTransformation->update($data);
        return redirect()->route('proposition-transformations.index')->with('ok','Proposition Transformation mise à jour');
    }

    public function destroy(PropositionTransformation $propositionTransformation){
        $propositionTransformation->delete();
        return back()->with('ok','Proposition Transformation supprimée');
    }


     /* =========================
    |   FRONTOFFICE METHODS
    * ========================= */

    /**
     * FEATURE 1: Advanced Dashboard with Analytics
     * Front index - Professional Dashboard
     */
    public function indexFront()
    {
        $userId = Auth::id();
        
        // Get all propositions for this transformer
        $propositions = PropositionTransformation::where('transformateur_id', $userId)
            ->with(['proposition.postDechet', 'transformateur', 'processus'])
            ->latest()
            ->paginate(10);

        // Calculate Statistics
        $totalPropositions = PropositionTransformation::where('transformateur_id', $userId)->count();
        $pendingCount = PropositionTransformation::where('transformateur_id', $userId)
            ->where('statut', 'en_attente')->count();
        $acceptedCount = PropositionTransformation::where('transformateur_id', $userId)
            ->where('statut', 'accepté')->count();

        // Calculate Total Revenue from accepted propositions with processus costs
        $totalRevenue = ProcessusTransformation::whereIn('proposition_transformation_id',
            PropositionTransformation::where('transformateur_id', $userId)
                ->where('statut', 'accepté')
                ->pluck('id')
        )->sum('cout');

        // ADVANCED FEATURE 1: Revenue by Month (Last 3 months)
        $revenueByMonth = [];
        for ($i = 2; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->locale('fr')->translatedFormat('F');
            
            $monthRevenue = ProcessusTransformation::whereIn('proposition_transformation_id',
                PropositionTransformation::where('transformateur_id', $userId)
                    ->where('statut', 'accepté')
                    ->pluck('id')
            )
            ->whereMonth('created_at', $monthDate->month)
            ->whereYear('created_at', $monthDate->year)
            ->sum('cout');
            
            $revenueByMonth[$monthName] = $monthRevenue;
        }

        $maxRevenue = max($revenueByMonth) ?: 5000;

        // Calculate Performance Metrics
        $acceptanceRate = $totalPropositions > 0 
            ? round(($acceptedCount / $totalPropositions) * 100) 
            : 0;

        // Average days to acceptance
        $avgDays = PropositionTransformation::where('transformateur_id', $userId)
            ->where('statut', 'accepté')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days') ?? 8.2;

        // Average rating (if you have a rating system)
        $avgRating = 4.8;

        // Get recommendations (FEATURE 2: AI-Powered)
        $recommendations = $this->getRecommendations($userId, 3);
        \Log::info('Recommendations in indexFront: ', $recommendations->toArray());

        return view('frontoffice.pages.transformation.propositions.index', compact(
            'propositions',
            'totalPropositions',
            'pendingCount',
            'acceptedCount',
            'totalRevenue',
            'revenueByMonth',
            'maxRevenue',
            'acceptanceRate',
            'avgDays',
            'avgRating',
            'recommendations'
        ));
    }

    /**
     * FRONTOFFICE: Create form (DIFFERENT from backoffice create)
     */
    public function createFront(){
        $posts = Proposition::with('postDechet')->get();

        return view('frontoffice.pages.transformation.propositions.create', compact('posts'));
    }

    /**
     * FRONTOFFICE: Store new proposition
     */
    public function storeFront(StorePropositionTransformationRequest $request){
        $data = $request->validated();
        // Automatically set transformer to current user
        $data['transformateur_id'] = Auth::id();

        PropositionTransformation::create($data);
        
        return redirect()->route('front.transformation.propositions.index')
            ->with('success', 'Proposition créée avec succès!');
    }

    /**
     * FEATURE 2: AI-Powered Recommendations
     * Generate recommendations based on transformer's history
     */
 private function getRecommendations($userId, $limit = 3)
{
    // Get categories of propositions the transformer has worked with
    $userCategories = PropositionTransformation::where('transformateur_id', $userId)
        ->with('proposition.postDechet')
        ->get()
        ->pluck('proposition.postDechet.categorie')
        ->unique()
        ->toArray();

    // If no history, recommend popular categories
    if (empty($userCategories)) {
        $userCategories = ['Plastique', 'Métaux', 'Textile'];
    }

    // Base query for eligible posts
    $query = PostDechet::whereIn('categorie', $userCategories)
        ->where('type_post', 'transformation')
        ->where('statut', 'en_attente')
        ->orderBy('created_at', 'desc');

    // Only apply whereNotIn if the user has proposals
    $existingProposalIds = PropositionTransformation::where('transformateur_id', $userId)->pluck('proposition_id')->toArray();
    if (!empty($existingProposalIds)) {
        $query->whereNotIn('id', 
            Proposition::whereIn('id', $existingProposalIds)->pluck('post_dechet_id')
        );
    }

    $posts = $query->get(); // Debug: Get all matches first (optional, remove in production)
    foreach ($posts as $post) {
        \Log::info('Available Post: ', ['id' => $post->id, 'title' => $post->titre, 'category' => $post->categorie]);
    }

    $recommendations = $query->limit($limit)->get()->map(function ($post) {
        return (object)[
            'post_id' => $post->id,
            'postDechet' => $post,
            'match_score' => $this->calculateMatchScore($post),
        ];
    });

    return $recommendations;
}

    /**
     * Calculate match score based on various factors
     */
private function calculateMatchScore($post)
{
    $score = 50;
    if ($post->categorie) $score += 20;
    if (strlen($post->description) > 100) $score += 15;
    // Remove or adjust the recency check
    return min(100, $score);
}
    // Front show
    public function showFront(PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.transformation.propositions.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $propositionTransformation->load(['proposition.postDechet', 'transformateur', 'processus']);
        return view('frontoffice.pages.transformation.propositions.show', 
            compact('propositionTransformation'));
    }

    // Front edit
    public function editFront(PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.transformation.propositions.index')
                ->with('error', 'Action non autorisée.');
        }

        $posts = Proposition::with('postDechet')->get();

        return view('frontoffice.pages.transformation.propositions.edit', 
            compact('propositionTransformation', 'posts'));
    }

    // Front update
    public function updateFront(UpdatePropositionTransformationRequest $request, 
        PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.transformation.propositions.index')
                ->with('error', 'Action non autorisée.');
        }

        $data = $request->validated();
        $propositionTransformation->update($data);

        return redirect()->route('front.transformation.propositions.show', $propositionTransformation->id)
            ->with('success', 'Proposition mise à jour avec succès.');
    }

    // Front destroy
    public function destroyFront(PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return back()->with('error', 'Action non autorisée.');
        }

        $propositionTransformation->delete();
        return back()->with('success', 'Proposition supprimée avec succès.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PostDechet;
use App\Http\Requests\StorePostDechetRequest;
use App\Http\Requests\UpdatePostDechetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostDechetController extends Controller
{
    /**
     * Index backoffice : stats + table sur la même page
     */
    public function index()
    {
        // Liste paginée (table)
        $posts = PostDechet::latest()->paginate(10);

        // ===== KPIs & Stats =====
        $totalPosts = PostDechet::count();
        $totalQuant = (int) PostDechet::sum('quantite');

        // CO2 évité (kg) – facteurs simples par catégorie (à ajuster selon ton besoin)
        $co2Factors = [
            'plastique' => 2.5, 'papier' => 1.3, 'verre' => 0.8,
            'métal' => 4.0, 'metal' => 4.0, 'meubles' => 1.0,
            'vetement' => 1.1, 'véhicule' => 5.0, 'vehicule' => 5.0,
        ];
        $co2SavedKg = 0.0;
        foreach (PostDechet::select('categorie','quantite')->get() as $p) {
            $cat    = strtolower((string) $p->categorie);
            $factor = $co2Factors[$cat] ?? 1.0;
            $co2SavedKg += ((int) ($p->quantite ?? 0)) * $factor;
        }
        $co2SavedKg = round($co2SavedKg, 1);

        // Répartition par statut & catégorie (pour graphiques)
        $byStatus = PostDechet::select('statut', DB::raw('count(*) c'))
            ->groupBy('statut')->pluck('c', 'statut');

        $byCategory = PostDechet::select('categorie', DB::raw('count(*) c'))
            ->groupBy('categorie')->orderByDesc('c')->get();

        // Créations par mois (12 derniers mois)
        $monthly = PostDechet::selectRaw('DATE_FORMAT(created_at, "%Y-%m") m, COUNT(*) c')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('m')->orderBy('m')->get();

        // Données prêtes pour Chart.js
        $chartStatusLabels  = $byStatus->keys()->map(fn($v) => $v ?: '—')->values();
        $chartStatusCounts  = $byStatus->values();
        $chartCatLabels     = $byCategory->pluck('categorie')->map(fn($v) => $v ?: '—');
        $chartCatCounts     = $byCategory->pluck('c');
        $chartMonthlyLabels = $monthly->pluck('m');
        $chartMonthlyCounts = $monthly->pluck('c');

        return view('backoffice.pages.postdechets.index', compact(
            'posts',
            'totalPosts',
            'totalQuant',
            'co2SavedKg',
            'chartStatusLabels',
            'chartStatusCounts',
            'chartCatLabels',
            'chartCatCounts',
            'chartMonthlyLabels',
            'chartMonthlyCounts'
        ));
    }

    public function indexTroc()
    {
        $posts = PostDechet::where('type_post', 'troc')->latest()->paginate(10);
        return view('backoffice.pages.postdechets.troc-index', compact('posts'));
    }

    public function indexTrocFront()
    {
        $posts = PostDechet::where('type_post', 'troc')->latest()->paginate(10);
        return view('frontoffice.pages.postdechets.troc-index', compact('posts'));
    }

    public function create()
    {
        return view('backoffice.pages.postdechets.create');
    }

    public function store(StorePostDechetRequest $r)
    {
        $data = $r->validated();
        $data['user_id'] = auth()->id() ?? 1;
        $data['date_publication'] = now();

        // Upload multiple (optionnel)
        $files = [];
        if ($r->hasFile('photos')) {
            foreach ($r->file('photos') as $f) {
                $files[] = $f->store('posts', 'public');
            }
        }
        if ($files) $data['photos'] = $files;

        PostDechet::create($data);
        return redirect()->route('postdechets.index')->with('ok', 'Post créé');
    }

    public function show(PostDechet $postdechet)
    {
        $postdechet->load('propositions.user');
        return view('backoffice.pages.postdechets.show', compact('postdechet'));
    }

    public function edit(PostDechet $postdechet)
    {
        return view('backoffice.pages.postdechets.edit', compact('postdechet'));
    }

    public function update(UpdatePostDechetRequest $r, PostDechet $postdechet)
    {
        $data = $r->validated();

        if ($r->hasFile('photos')) {
            $files = $postdechet->photos ?? [];
            foreach ($r->file('photos') as $f) {
                $files[] = $f->store('posts', 'public');
            }
            $data['photos'] = $files;
        }

        $postdechet->update($data);
        return redirect()->route('postdechets.index')->with('ok', 'Post mis à jour');
    }

    public function destroy(PostDechet $postdechet)
    {
        $postdechet->delete();
        return back()->with('ok', 'Post supprimé');
    }

    public function showOffres($post)
    {
        $post = PostDechet::with('offreTrocs')->findOrFail($post);
        $offres = $post->offreTrocs; // Relation avec OffreTroc
        return view('backoffice.pages.offres-troc.post-offres', compact('post', 'offres'));
    }

    public function showOffresFront($postId)
    {
        $post = PostDechet::with('offreTrocs')->findOrFail($postId);
        $offres = $post->offreTrocs;
        return view('frontoffice.pages.offres-troc.post-offres', compact('post', 'offres'));
    }

    // Nouvelle méthode pour toggle favori
    public function toggleFavorite(PostDechet $post)
    {
        $user = Auth::user();
        if ($user->favorites()->where('post_dechet_id', $post->id)->exists()) {
            $user->favorites()->detach($post->id);
            return redirect()->back()->with('success', 'Post supprimé des favoris.');
        } else {
            $user->favorites()->attach($post->id);
            return redirect()->back()->with('success', 'Post ajouté aux favoris.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\OffreTroc;
use App\Models\PostDechet;
use App\Models\TransactionTroc;
use Illuminate\Http\Request;

class OffreTrocController extends Controller
{
    public function index()
    {
        $offres = OffreTroc::with('postDechet', 'user')->latest()->paginate(10);
        return view('backoffice.pages.offres-troc.index', compact('offres'));
    }

    public function create($postId)
    {
        $post = PostDechet::findOrFail($postId);
        if ($post->type_post !== 'troc') {
            return redirect()->back()->with('error', 'Ce post n\'est pas un troc.');
        }
        return view('backoffice.pages.offres-troc.create', compact('post'));
    }

    public function store(Request $request, $postId)
    {
        $validated = $request->validate([
            'categorie' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'unite_mesure' => 'required|string',
            'etat' => 'required|string',
            'localisation' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
            'description' => 'required|string|max:500',
        ]);

        $post = PostDechet::findOrFail($postId);

        $files = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $f) {
                $files[] = $f->store('offres', 'public');
            }
        }

        OffreTroc::create([
            'categorie' => $validated['categorie'],
            'quantite' => $validated['quantite'],
            'unite_mesure' => $validated['unite_mesure'],
            'etat' => $validated['etat'],
            'localisation' => $validated['localisation'],
            'photos' => !empty($files) ? json_encode($files) : null,
            'description' => $validated['description'],
            'user_id' => auth()->id() ?? 1,
            'post_dechet_id' => $postId,
            'status' => 'en_attente',
        ]);

        return redirect()->route('offres-troc.index')->with('success', 'Offre créée avec succès');
    }

    public function show($postId)
    {
        $post = PostDechet::findOrFail($postId);
        
        // Vérifier s'il y a une offre acceptée pour ce post - vérifier différentes orthographes
        $hasAcceptedOffer = OffreTroc::where('post_dechet_id', $postId)
            ->where(function($query) {
                $query->where('status', 'accepted')
                    ->orWhere('status', 'Accepted')
                    ->orWhere('status', 'ACCEPTED');
            })
            ->exists();

        // Récupérer toutes les offres triées
        $offres = OffreTroc::where('post_dechet_id', $postId)
            ->orderByRaw("CASE WHEN LOWER(status) = 'accepted' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        // Debug: vérifions ce qui se passe
        \Log::info('Offres pour le post ' . $postId, [
            'hasAcceptedOffer' => $hasAcceptedOffer,
            'offres_count' => $offres->count(),
            'offres_status' => $offres->pluck('status')
        ]);

        return view('backoffice.pages.offres-troc.show', compact('post', 'offres', 'hasAcceptedOffer'));
    }

    public function updateStatut(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,en_attente',
        ]);

        $offre = OffreTroc::findOrFail($id);
        
        // Si on accepte une offre, rejeter toutes les autres offres du même post
        if ($validated['status'] === 'accepted') {
            OffreTroc::where('post_dechet_id', $offre->post_dechet_id)
                ->where('id', '!=', $id)
                ->update(['status' => 'rejected']);
        }

        $oldStatus = $offre->status;
        $offre->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted' && $oldStatus !== 'accepted') {
            TransactionTroc::create([
                'offre_troc_id' => $id,
                'utilisateur_acceptant_id' => $offre->postDechet->user_id,
                'date_accord' => now(),
                'statut_livraison' => 'en_cours',
            ]);
        }

        return redirect()->back()->with('success', 'Statut mis à jour');
    }
}
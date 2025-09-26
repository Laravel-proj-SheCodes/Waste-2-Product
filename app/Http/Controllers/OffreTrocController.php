<?php

namespace App\Http\Controllers;

use App\Models\OffreTroc;
use App\Models\PostDechet;
use App\Models\TransactionTroc;
use Illuminate\Http\Request;

class OffreTrocController extends Controller
{
    // Backoffice methods
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
        
        $hasAcceptedOffer = OffreTroc::where('post_dechet_id', $postId)
            ->where(function($query) {
                $query->where('status', 'accepted')
                    ->orWhere('status', 'Accepted')
                    ->orWhere('status', 'ACCEPTED');
            })
            ->exists();

        $offres = OffreTroc::where('post_dechet_id', $postId)
            ->orderByRaw("CASE WHEN LOWER(status) = 'accepted' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backoffice.pages.offres-troc.show', compact('post', 'offres', 'hasAcceptedOffer'));
    }

    public function edit($id)
    {
        $offre = OffreTroc::findOrFail($id);
        return view('backoffice.pages.offres-troc.edit', compact('offre'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'categorie' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'unite_mesure' => 'required|string',
            'etat' => 'required|string',
            'localisation' => 'required|string',
            'description' => 'required|string|max:500',
        ]);

        $offre = OffreTroc::findOrFail($id);
        $offre->update($validated);

        return redirect()->route('offres-troc.index')->with('success', 'Offre mise à jour avec succès');
    }

    public function destroy($id)
    {
        $offre = OffreTroc::findOrFail($id);
        $offre->delete();

        return redirect()->route('offres-troc.index')->with('success', 'Offre supprimée avec succès');
    }

    public function updateStatut(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,en_attente',
        ]);

        $offre = OffreTroc::findOrFail($id);
        
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

    // Frontoffice methods
    public function indexFront()
    {
        $offres = OffreTroc::with('postDechet', 'user')->latest()->paginate(10);
        return view('frontoffice.pages.offres-troc.index', compact('offres'));
    }

    public function createFront($postId)
    {
        $post = PostDechet::findOrFail($postId);
        if ($post->type_post !== 'troc') {
            return redirect()->back()->with('error', 'Ce post n\'est pas un troc.');
        }
        return view('frontoffice.pages.offres-troc.create', compact('post'));
    }

    public function storeFront(Request $request, $postId)
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

        return redirect()->route('offres-troc.index.front')->with('success', 'Offre créée avec succès');
    }

    public function showFront($postId)
    {
        $post = PostDechet::findOrFail($postId);
        
        $hasAcceptedOffer = OffreTroc::where('post_dechet_id', $postId)
            ->where(function($query) {
                $query->where('status', 'accepted')
                    ->orWhere('status', 'Accepted')
                    ->orWhere('status', 'ACCEPTED');
            })
            ->exists();

        $offres = OffreTroc::where('post_dechet_id', $postId)
            ->orderByRaw("CASE WHEN LOWER(status) = 'accepted' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontoffice.pages.offres-troc.show', compact('post', 'offres', 'hasAcceptedOffer'));
    }

    public function updateStatutFront(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected,en_attente',
        ]);

        $offre = OffreTroc::findOrFail($id);
        
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
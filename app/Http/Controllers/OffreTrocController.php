<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\NouvelleOffreTrocMail;
use App\Models\OffreTroc;
use App\Models\PostDechet;
use App\Models\TransactionTroc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OffreTrocController extends Controller
{
    /* ==================== BACKOFFICE ==================== */

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
        $validated = $this->validateOffer($request);

        $post = PostDechet::findOrFail($postId);
        $files = $this->handleFiles($request);

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
            ->whereIn('status', ['accepted', 'Accepted', 'ACCEPTED'])
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
        if (!$this->canModify($offre)) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier cette offre.');
        }
        return view('backoffice.pages.offres-troc.edit', compact('offre'));
    }

    public function update(Request $request, $id)
    {
        $offre = OffreTroc::findOrFail($id);
        if (!$this->canModify($offre)) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier cette offre.');
        }

        $validated = $this->validateOffer($request);
        $files = $this->updateFiles($request, $offre);

        $offre->update([
            'categorie' => $validated['categorie'],
            'quantite' => $validated['quantite'],
            'unite_mesure' => $validated['unite_mesure'],
            'etat' => $validated['etat'],
            'localisation' => $validated['localisation'],
            'photos' => !empty($files) ? json_encode($files) : null,
            'description' => $validated['description'],
        ]);

        return redirect()->route('postdechets.offres', $offre->post_dechet_id)
            ->with('success', 'Offre mise à jour avec succès');
    }

    public function destroy($id)
    {
        $offre = OffreTroc::findOrFail($id);
        if (!$this->canModify($offre)) {
            return redirect()->route('offres-troc.index')->with('error', 'Vous ne pouvez pas supprimer cette offre.');
        }

        $this->deleteFiles($offre);
        $offre->delete();

        return redirect()->route('offres-troc.index')->with('success', 'Offre supprimée avec succès');
    }

    public function updateStatut(Request $request, $id)
    {
        $validated = $request->validate(['status' => 'required|in:accepted,rejected,en_attente']);

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

    /* ==================== FRONT OFFICE ==================== */

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
    if (!Auth::check()) {
        return $request->wantsJson()
            ? response()->json(['error' => 'Authentification requise'], 401)
            : redirect()->route('login')->with('error', 'Authentification requise');
    }

    $validated = $this->validateOffer($request);
    $files = $this->handleFiles($request);

    $offre = OffreTroc::create([
        'categorie' => $validated['categorie'],
        'quantite' => $validated['quantite'],
        'unite_mesure' => $validated['unite_mesure'],
        'etat' => $validated['etat'],
        'localisation' => $validated['localisation'],
        'photos' => !empty($files) ? json_encode($files) : null,
        'description' => $validated['description'],
        'user_id' => Auth::id(),
        'post_dechet_id' => $postId,
        'status' => 'en_attente',
    ]);

    // ===== LOGIQUE D'ENVOI D'EMAIL =====
    $post = PostDechet::findOrFail($postId);
    $owner = $post->user; // Assure-toi que la relation user est définie dans PostDechet
    if ($owner && $owner->email) {
        Mail::to($owner->email)->send(new NouvelleOffreTrocMail($offre));
    }

    if ($request->wantsJson()) {
        return response()->json($offre->load('user', 'postDechet'), 201);
    }

    $route = $request->input('from_front') ? 'offres-troc.thankyou' : 'offres-troc.index.front';
    return redirect()->route($route)->with('success', 'Offre créée avec succès !');
}


    public function showFront($postId)
    {
        $post = PostDechet::findOrFail($postId);
        $hasAcceptedOffer = OffreTroc::where('post_dechet_id', $postId)
            ->whereIn('status', ['accepted', 'Accepted', 'ACCEPTED'])
            ->exists();

        $offres = OffreTroc::where('post_dechet_id', $postId)
            ->orderByRaw("CASE WHEN LOWER(status) = 'accepted' THEN 1 ELSE 2 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontoffice.pages.offres-troc.show', compact('post', 'offres', 'hasAcceptedOffer'));
    }

    public function editFront($id)
    {
        $offre = OffreTroc::findOrFail($id);
        if (!$this->canModify($offre)) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier cette offre.');
        }
        return view('frontoffice.pages.offres-troc.edit', compact('offre'));
    }

    public function updateFront(Request $request, $id)
    {
        $offre = OffreTroc::findOrFail($id);
        if (!$this->canModify($offre)) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas modifier cette offre.');
        }

        $validated = $this->validateOffer($request, true); // true = front, photos nullable
        $files = $this->updateFiles($request, $offre);

        $offre->update([
            'categorie' => $validated['categorie'],
            'quantite' => $validated['quantite'],
            'unite_mesure' => $validated['unite_mesure'],
            'etat' => $validated['etat'],
            'localisation' => $validated['localisation'],
            'photos' => !empty($files) ? json_encode($files) : null,
            'description' => $validated['description'],
        ]);

        return redirect()->route('postdechets.offres.front', $offre->post_dechet_id)
            ->with('success', 'Offre mise à jour avec succès');
    }

    public function destroyFront($id)
    {
        $offre = OffreTroc::findOrFail($id);
        if (!$this->canModify($offre)) {
            return redirect()->route('postdechets.offres.front', $offre->post_dechet_id)
                ->with('error', 'Vous ne pouvez pas supprimer cette offre.');
        }

        $this->deleteFiles($offre);
        $offre->delete();

        return redirect()->route('postdechets.offres.front', $offre->post_dechet_id)
            ->with('success', 'Offre supprimée avec succès.');
    }

   public function updateStatutFront(Request $request, $id)
{
    $validated = $request->validate(['status' => 'required|in:accepted,rejected,en_attente']);
    $offre = OffreTroc::findOrFail($id);
    $post = $offre->postDechet;

    if ($validated['status'] === 'accepted') {

        // Rejeter les autres offres
        $otherOffres = OffreTroc::where('post_dechet_id', $post->id)
            ->where('id', '!=', $id)
            ->get();

        foreach ($otherOffres as $o) {
            $o->update(['status' => 'rejected']);

            if ($o->user && $o->user->email) {
                \Mail::to($o->user->email)->send(new \App\Mail\OffreStatusMail($o, $post, 'rejected'));
            }
        }

        // Mettre à jour l'offre acceptée
        $oldStatus = $offre->status;
        $offre->update(['status' => 'accepted']);

        // Créer la transaction si ce n'était pas déjà accepté
        if ($oldStatus !== 'accepted') {
            TransactionTroc::create([
                'offre_troc_id' => $offre->id,
                'utilisateur_acceptant_id' => $post->user_id,
                'date_accord' => now(),
                'statut_livraison' => 'en_cours',
            ]);
        }

        // Envoyer email d'acceptation
        if ($offre->user && $offre->user->email) {
            \Mail::to($offre->user->email)->send(new \App\Mail\OffreStatusMail($offre, $post, 'accepted'));
        }

    } else {
        // Offre rejetée ou en attente
        $offre->update(['status' => $validated['status']]);

        if ($offre->user && $offre->user->email) {
            \Mail::to($offre->user->email)->send(new \App\Mail\OffreStatusMail($offre, $post, $validated['status']));
        }
    }

    return redirect()->back()->with('success', 'Statut mis à jour et emails envoyés.');
}


    /* ==================== HELPER FUNCTIONS ==================== */

    private function validateOffer(Request $request, $front = false)
    {
        $rules = [
            'categorie' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'unite_mesure' => 'required|string',
            'etat' => 'required|string',
            'localisation' => 'required|string',
            'description' => 'required|string|max:500',
            'photos' => 'nullable|array',
            'photos.*' => 'image|max:2048',
        ];

        return $request->validate($rules);
    }

    private function handleFiles(Request $request)
    {
        $files = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $files[] = $file->store('offres', 'public');
            }
        }
        return $files;
    }

    private function updateFiles(Request $request, OffreTroc $offre)
    {
        $files = $offre->photos ? json_decode($offre->photos, true) : [];
        if ($request->hasFile('photos')) {
            foreach ($files as $photo) {
                Storage::disk('public')->delete($photo);
            }
            $files = $this->handleFiles($request);
        }
        return $files;
    }

    private function deleteFiles(OffreTroc $offre)
    {
        if ($offre->photos) {
            foreach (json_decode($offre->photos, true) as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
    }

    private function canModify(OffreTroc $offre)
    {
        return Auth::id() === $offre->user_id && strtolower($offre->status) !== 'accepted';
    }
}

<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StorePropositionFrontRequest;
use App\Http\Requests\Front\UpdatePropositionFrontRequest;
use App\Models\PostDechet;
use App\Models\Proposition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;              // ✅ manquait
use Illuminate\Http\Request;                   // ✅ manquait
use App\Notifications\ProposalAccepted;        // ✅ pour notifier à l’acceptation

class PropositionFrontController extends Controller
{
    /** Mes propres propositions (celles que j’ai envoyées) */
    public function index()
    {
        $propositions = Proposition::with('postDechet:id,titre')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Si tes vues sont dans resources/views/frontoffice/propositions/*
        return view('frontoffice.propositions.index', compact('propositions'));
    }

    /** Formulaire de création d’une proposition pour un post donné */
    public function create(PostDechet $postDechet)
    {
        abort_if($postDechet->user_id === Auth::id(), 403);
        return view('frontoffice.propositions.create', compact('postDechet'));
    }

    /** Enregistrer la proposition */
    public function store(StorePropositionFrontRequest $request, PostDechet $postDechet)
    {
        abort_if($postDechet->user_id === Auth::id(), 403);

        Proposition::create([
            'post_dechet_id'   => $postDechet->id,
            'user_id'          => Auth::id(),
            'description'      => $request->validated()['description'],
            'date_proposition' => now(),
            'statut'           => 'en_attente',
        ]);

        return redirect()->route('front.propositions.index')
            ->with('success', 'Proposition envoyée.');
    }

    /** Modifier MA proposition */
    public function edit(Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);
        return view('frontoffice.propositions.edit', compact('proposition'));
    }

    /** Mettre à jour MA proposition */
    public function update(UpdatePropositionFrontRequest $request, Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);

        $proposition->update($request->validated());

        return redirect()->route('front.propositions.index')
            ->with('success', 'Proposition mise à jour.');
    }

    /** Supprimer MA proposition */
    public function destroy(Proposition $proposition)
    {
        abort_if($proposition->user_id !== Auth::id(), 403);

        $proposition->delete();

        return back()->with('success', 'Proposition supprimée.');
    }

    /** Afficher un post côté front (et les propositions reçues si je suis le propriétaire) */
    public function show(PostDechet $postDechet)
    {
        $received = collect();
        if (Auth::check() && Auth::id() === $postDechet->user_id) {
            $received = $postDechet->propositions()
                ->with('user:id,name')
                ->latest()
                ->get();
        }

        // ✅ ta vue est sous frontoffice/pages/postdechets/show.blade.php
        return view('frontoffice.pages.postdechets.show', compact('postDechet', 'received'));
    }

    /** Propositions reçues sur MES posts + marquer les notifs en lues */
    public function received(Request $request)
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        // Marquer comme lues les notifications de "ProposalReceived"
        DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->where('type', \App\Notifications\ProposalReceived::class)
            ->update(['read_at' => now()]);

        // Lister les propositions reçues (sur les posts dont JE suis owner)
        $propositions = Proposition::query()
            ->with(['postDechet:id,titre,user_id', 'user:id,name'])
            ->whereHas('postDechet', fn ($q) => $q->where('user_id', $userId))
            ->latest('id')
            ->paginate(10);

        // ✅ received.blade.php est dans frontoffice/pages/propositions/
        return view('frontoffice.pages.propositions.received', compact('propositions'));
    }

    /** Accepter une proposition (owner only) */
// Accepter une proposition (owner only)
public function accept(Proposition $proposition)
{
    // Seul le propriétaire du post peut accepter/refuser
    abort_unless($proposition->postDechet?->user_id === Auth::id(), 403);

    DB::transaction(function () use ($proposition) {
        // 1) Si pas déjà acceptée -> l'accepter
        if ($proposition->statut !== 'accepte') {
            $proposition->update(['statut' => 'accepte']);
        }

        // 2) Refuser toutes les autres propositions du même post
        Proposition::where('post_dechet_id', $proposition->post_dechet_id)
            ->where('id', '!=', $proposition->id)
            ->where('statut', '!=', 'refusee')
            ->update(['statut' => 'refusee']);

        // 3) Notifier l’auteur de la proposition acceptée
        $proposition->loadMissing('postDechet', 'user');
        $proposition->user?->notify(new \App\Notifications\ProposalAccepted(
            postId:        $proposition->post_dechet_id,
            propositionId: $proposition->id,
            postTitle:     $proposition->postDechet->titre ?? 'Post'
        ));
    });

    // Redirection vers la page front avec highlight
    $url = route('front.waste-posts.show', $proposition->post_dechet_id)
         . '?highlight=' . $proposition->id . '#propositions';

    return redirect($url)->with('ok', 'Proposition acceptée.');
}


// Refuser une proposition (owner only)
public function reject(Proposition $proposition)
{
    abort_unless($proposition->postDechet?->user_id === Auth::id(), 403);

    // si ta BDD a 'refusee', laisse 'refusee'. Si elle a 'refuse', mets 'refuse'.
    if ($proposition->statut !== 'refusee') {
        $proposition->update(['statut' => 'refusee']);
    }

    $url = route('front.waste-posts.show', $proposition->post_dechet_id)
         . '?highlight=' . $proposition->id . '#propositions';

    return redirect($url)->with('ok', 'Proposition refusée.');
}

}

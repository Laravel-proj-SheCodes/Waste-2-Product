<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use App\Models\PostDechet;
use App\Http\Requests\StorePropositionRequest;
use App\Http\Requests\UpdatePropositionRequest;

class PropositionController extends Controller
{
    public function index()
    {
        $propositions = Proposition::with(['postDechet:id,titre','user:id,name'])
            ->latest()
            ->paginate(10);

        return view('backoffice.pages.propositions.index', compact('propositions'));
    }

    public function create()
    {
        $posts = PostDechet::orderBy('titre')->pluck('titre', 'id'); // pour le select
        return view('backoffice.pages.propositions.create', compact('posts'));
    }

    public function store(StorePropositionRequest $request)
    {
        $data = $request->validated();

        // si pas d’auth, on force un user_id temporaire
        $data['user_id'] = $data['user_id'] ?? 1;

        Proposition::create($data);
        return redirect()->route('propositions.index')->with('ok', 'Proposition créée.');
    }

    public function show(Proposition $proposition)
    {
        $proposition->load(['postDechet','user']);
        return view('backoffice.pages.propositions.show', compact('proposition'));
    }

    public function edit(Proposition $proposition)
    {
        $posts = PostDechet::orderBy('titre')->pluck('titre', 'id');
        return view('backoffice.pages.propositions.edit', compact('proposition','posts'));
    }

    public function update(UpdatePropositionRequest $request, Proposition $proposition)
    {
        $data = $request->validated();
        $proposition->update($data);
        return redirect()->route('propositions.index')->with('ok', 'Proposition mise à jour.');
    }

    public function destroy(Proposition $proposition)
    {
        $proposition->delete();
        return back()->with('ok', 'Proposition supprimée.');
    }
}

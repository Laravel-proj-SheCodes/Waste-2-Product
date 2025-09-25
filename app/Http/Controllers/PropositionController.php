<?php

namespace App\Http\Controllers;

use App\Models\Proposition;
use App\Http\Requests\StorePropositionRequest;
use App\Http\Requests\UpdatePropositionRequest;

class PropositionController extends Controller
{
    public function index(){
        $props = Proposition::with(['postDechet','user'])->latest()->paginate(10);
        return view('backoffice.pages.propositions.index', compact('props'));
    }

    public function create(){
        return view('backoffice.pages.propositions.create');
    }

    public function store(StorePropositionRequest $r){
        Proposition::create([
            'post_dechet_id' => $r->post_dechet_id,
            'user_id' => auth()->id() ?? 1,
            'description' => $r->description,
            'date_proposition' => now(),
            'statut' => $r->statut ?? 'en_attente',
        ]);
        return redirect()->route('propositions.index')->with('ok','Proposition créée');
    }

    public function edit(Proposition $proposition){
        return view('backoffice.pages.propositions.edit', compact('proposition'));
    }

    public function update(UpdatePropositionRequest $r, Proposition $proposition){
        $proposition->update($r->validated());
        return redirect()->route('propositions.index')->with('ok','Proposition mise à jour');
    }

    public function destroy(Proposition $proposition){
        $proposition->delete();
        return back()->with('ok','Proposition supprimée');
    }
}

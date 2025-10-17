<?php

namespace App\Http\Controllers;

use App\Models\PropositionTransformation;
use App\Models\Proposition;
use App\Models\User;
use App\Http\Requests\StorePropositionTransformationRequest;
use App\Http\Requests\UpdatePropositionTransformationRequest;
use Illuminate\Support\Facades\Auth;

class PropositionTransformationController extends Controller
{
    public function index(){
        $propositions = PropositionTransformation::with(['proposition.postDechet','transformateur'])
            ->latest()
            ->paginate(10);

        return view('backoffice.pages.transformation.propositions.index', compact('propositions'));
    }

    public function create(){
        $posts = Proposition::with('postDechet')->get(); // pour select
        $transformateurs = User::where('role','Transformateur')->pluck('name','id');
       // $transformateurs = User::pluck('name', 'id');

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
    |   Frontoffice Methods
    * ========================= */

    // Liste des propositions de l’utilisateur connecté
    public function indexFront()
    {
        $propositions = PropositionTransformation::where('transformateur_id', Auth::id())
            ->with(['proposition.postDechet','transformateur'])
            ->latest()
            ->paginate(10);

        return view('frontoffice.pages.transformation.propositions.index', compact('propositions'));
    }

    // Détails d’une proposition (front)
    public function showFront(PropositionTransformation $propositionTransformation)
    {
        // Vérifie autorisation
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.propositions.index')->with('error', 'Vous n\'êtes pas autorisé à voir cette proposition.');
        }

        $propositionTransformation->load(['proposition.postDechet','transformateur']);
        return view('frontoffice.pages.transformation.propositions.show', compact('propositionTransformation'));
    }

    // Formulaire d’édition côté front
    public function editFront(PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.propositions.index')->with('error', 'Action non autorisée.');
        }

        return view('frontoffice.pages.transformation.propositions.edit', compact('propositionTransformation'));
    }

    // Mise à jour côté front (statut ou description)
    public function updateFront(UpdatePropositionTransformationRequest $request, PropositionTransformation $propositionTransformation)
    {
        if (Auth::id() !== $propositionTransformation->transformateur_id) {
            return redirect()->route('front.propositions.index')->with('error', 'Action non autorisée.');
        }

        $data = $request->validated();
        $propositionTransformation->update($data);

        return redirect()->route('front.propositions.show', $propositionTransformation->id)->with('success', 'Proposition mise à jour avec succès.');
    }

}



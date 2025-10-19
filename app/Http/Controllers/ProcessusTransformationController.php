<?php
namespace App\Http\Controllers;

use App\Models\ProcessusTransformation;
use App\Models\PropositionTransformation;
use App\Models\PostDechet;
use App\Http\Requests\StoreProcessusTransformationRequest;
use App\Http\Requests\UpdateProcessusTransformationRequest;
use Illuminate\Support\Facades\Auth;

class ProcessusTransformationController extends Controller
{
    /* =========================
    |   BACKOFFICE METHODS (Keep as is)
    * ========================= */
    
    public function index(){
        $processus = ProcessusTransformation::with(['propositionTransformation.proposition.postDechet','dechetEntrant'])->latest()->paginate(10);
        return view('backoffice.pages.transformation.processus.index', compact('processus'));
    }

    public function create() {
        $propositions = PropositionTransformation::with('proposition.postDechet')->get();
        $dechets = PostDechet::all();
        return view('backoffice.pages.transformation.processus.create', compact('propositions','dechets'));
    }

    public function store(StoreProcessusTransformationRequest $request){
        $data = $request->validated();
        ProcessusTransformation::create([
            'proposition_transformation_id' => $data['proposition_transformation_id'],
            'dechet_entrant_id'             => $data['dechet_entrant_id'],
            'duree_estimee'                 => $data['duree_estimee'],
            'cout'                          => $data['cout'],
            'equipements'                   => $data['equipements'] ?? null,
            'statut'                        => $data['statut'] ?? 'en_cours',
        ]);
        return redirect()->route('processus-transformations.index')->with('ok','Processus Transformation créé');
    }

    public function show(ProcessusTransformation $processusTransformation){
        $processusTransformation->load(['propositionTransformation.proposition.postDechet','dechetEntrant']);
        $processus = $processusTransformation;
        return view('backoffice.pages.transformation.processus.show', compact('processus'));
    }

    public function edit(ProcessusTransformation $processusTransformation) {
        $propositions = PropositionTransformation::with('proposition.postDechet')->get();
        $dechets = PostDechet::all();
        $processus = $processusTransformation;
        return view('backoffice.pages.transformation.processus.edit', compact('processus','propositions','dechets'));
    }

    public function update(UpdateProcessusTransformationRequest $request, ProcessusTransformation $processusTransformation){
        $data = $request->validated();
        $processusTransformation->update([
            'proposition_transformation_id' => $data['proposition_transformation_id'] ?? $processusTransformation->proposition_transformation_id,
            'dechet_entrant_id' => $data['dechet_entrant_id'] ?? $processusTransformation->dechet_entrant_id,
            'duree_estimee' => $data['duree_estimee'] ?? $processusTransformation->duree_estimee,
            'cout' => $data['cout'] ?? $processusTransformation->cout,
            'equipements' => $data['equipements'] ?? $processusTransformation->equipements,
            'statut' => $data['statut'] ?? $processusTransformation->statut,
        ]);
        return redirect()->route('processus-transformations.index')->with('ok','Processus Transformation mis à jour');
    }

    public function destroy(ProcessusTransformation $processusTransformation){
        $processusTransformation->delete();
        return back()->with('ok','Processus Transformation supprimé');
    }

    /* =========================
    |   FRONTOFFICE METHODS (NEW)
    * ========================= */

    /**
     * FRONTOFFICE: List all processus for current transformer
     */
    public function indexFront(){
        $processus = ProcessusTransformation::whereIn('proposition_transformation_id',
            PropositionTransformation::where('transformateur_id', Auth::id())
                ->pluck('id')
        )
        ->with(['propositionTransformation.proposition.postDechet', 'dechetEntrant', 'produits'])
        ->latest()
        ->paginate(10);

        return view('frontoffice.pages.transformation.processus.index', compact('processus'));
    }

    /**
     * FRONTOFFICE: Show single processus details
     */
    public function showFront(ProcessusTransformation $processusTransformation){
        // Verify ownership
        if ($processusTransformation->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.processus.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $processusTransformation->load(['propositionTransformation.proposition.postDechet', 'dechetEntrant', 'produits']);
        return view('frontoffice.pages.transformation.processus.show', compact('processusTransformation'));
    }

    /**
     * FRONTOFFICE: Edit form
     */
    public function editFront(ProcessusTransformation $processusTransformation){
        // Verify ownership
        if ($processusTransformation->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.processus.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        return view('frontoffice.pages.transformation.processus.edit', compact('processusTransformation'));
    }

    /**
     * FRONTOFFICE: Update processus
     */
    public function updateFront(UpdateProcessusTransformationRequest $request, ProcessusTransformation $processusTransformation){
        // Verify ownership
        if ($processusTransformation->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.processus.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $data = $request->validated();
        $processusTransformation->update($data);

        return redirect()->route('front.transformation.processus.index')
            ->with('success', 'Processus mis à jour avec succès.');
    }
}
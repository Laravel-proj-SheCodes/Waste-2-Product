<?php

namespace App\Http\Controllers;

use App\Models\ProcessusTransformation;
use App\Models\PropositionTransformation;
use App\Models\PostDechet;
use App\Http\Requests\StoreProcessusTransformationRequest;
use App\Http\Requests\UpdateProcessusTransformationRequest;

class ProcessusTransformationController extends Controller
{
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
    
    $processus = $processusTransformation; // rename for blade
    return view('backoffice.pages.transformation.processus.show', compact('processus'));
}

public function edit(ProcessusTransformation $processusTransformation) {
    $propositions = PropositionTransformation::with('proposition.postDechet')->get();
    $dechets = PostDechet::all();
    
    $processus = $processusTransformation; // Fix variable name for blade
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
}

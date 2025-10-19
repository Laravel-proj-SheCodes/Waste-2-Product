<?php
namespace App\Http\Controllers;

use App\Models\ProduitTransforme;
use App\Models\ProcessusTransformation;
use App\Models\PropositionTransformation;
use App\Http\Requests\StoreProduitTransformeRequest;
use App\Http\Requests\UpdateProduitTransformeRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProduitTransformeController extends Controller
{
    /* =========================
    |   BACKOFFICE METHODS
    * ========================= */
    
    public function index()
    {
        $produits = ProduitTransforme::with('processus')->latest()->paginate(10);
        return view('backoffice.pages.transformation.produits.index', compact('produits'));
    }

    public function create()
    {
        $processus = ProcessusTransformation::pluck('id', 'id');
        return view('backoffice.pages.transformation.produits.create', compact('processus'));
    }

    public function store(StoreProduitTransformeRequest $request)
    {
        $data = $request->validated();
        ProduitTransforme::create($data);
        return redirect()->route('produit-transformes.index')->with('ok', 'Produit Transformé créé');
    }

    public function show(ProduitTransforme $produitTransforme)
    {
        $produitTransforme->load('processus.propositionTransformation.proposition.postDechet');
        return view('backoffice.pages.transformation.produits.show', compact('produitTransforme'));
    }

    public function edit(ProduitTransforme $produitTransforme)
    {
        $processus = ProcessusTransformation::pluck('id', 'id');
        return view('backoffice.pages.transformation.produits.edit', compact('produitTransforme', 'processus'));
    }

    public function update(UpdateProduitTransformeRequest $request, ProduitTransforme $produitTransforme)
    {
        $data = $request->validated();
        
        if ($request->hasFile('photo')) {
            if ($produitTransforme->photo) {
                Storage::disk('public')->delete($produitTransforme->photo);
            }
            $data['photo'] = $request->file('photo')->store('produits', 'public');
        }
        
        $produitTransforme->update($data);
        return redirect()->route('produit-transformes.index')->with('ok', 'Produit Transformé mis à jour');
    }

    public function destroy(ProduitTransforme $produitTransforme)
    {
        if ($produitTransforme->photo) {
            Storage::disk('public')->delete($produitTransforme->photo);
        }
        $produitTransforme->delete();
        return back()->with('ok', 'Produit Transformé supprimé');
    }

    /* =========================
    |   FRONTOFFICE METHODS
    * ========================= */

    public function indexFront()
    {
        $produits = ProduitTransforme::whereIn('processus_id',
            ProcessusTransformation::whereIn('proposition_transformation_id',
                PropositionTransformation::where('transformateur_id', Auth::id())
                    ->pluck('id')
            )->pluck('id')
        )
        ->with('processus')
        ->latest()
        ->paginate(12);

        return view('frontoffice.pages.transformation.produits.index', compact('produits'));
    }

    public function createFront()
    {
        $processus = ProcessusTransformation::whereIn('proposition_transformation_id',
            PropositionTransformation::where('transformateur_id', Auth::id())
                ->pluck('id')
        )
        ->with('propositionTransformation')
        ->get();

        return view('frontoffice.pages.transformation.produits.create', compact('processus'));
    }

    public function storeFront(StoreProduitTransformeRequest $request)
    {
        $processus = ProcessusTransformation::find($request->input('processus_id'));
        if (!$processus || $processus->propositionTransformation->transformateur_id !== Auth::id()) {
            return back()->with('error', 'Processus non trouvé ou non autorisé.');
        }

        $data = $request->validated();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('produits', 'public');
        }

        ProduitTransforme::create($data);

        return redirect()->route('front.transformation.produits.index')
            ->with('success', 'Produit créé avec succès!');
    }

    public function showFront(ProduitTransforme $produitTransforme)
    {
        if ($produitTransforme->processus->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $produitTransforme->load('processus.propositionTransformation.proposition.postDechet');
        return view('frontoffice.pages.transformation.produits.show', compact('produitTransforme'));
    }

    public function editFront(ProduitTransforme $produitTransforme)
    {
        if ($produitTransforme->processus->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $processus = ProcessusTransformation::whereIn('proposition_transformation_id',
            PropositionTransformation::where('transformateur_id', Auth::id())
                ->pluck('id')
        )
        ->with('propositionTransformation')
        ->get();

        return view('frontoffice.pages.transformation.produits.edit', compact('produitTransforme', 'processus'));
    }

    public function updateFront(UpdateProduitTransformeRequest $request, ProduitTransforme $produitTransforme)
    {
        if ($produitTransforme->processus->propositionTransformation->transformateur_id !== Auth::id()) {
            return redirect()->route('front.transformation.produits.index')
                ->with('error', 'Vous n\'êtes pas autorisé.');
        }

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($produitTransforme->photo) {
                Storage::disk('public')->delete($produitTransforme->photo);
            }
            $data['photo'] = $request->file('photo')->store('produits', 'public');
        }

        $produitTransforme->update($data);

        return redirect()->route('front.transformation.produits.index')
            ->with('success', 'Produit mis à jour avec succès!');
    }

    public function destroyFront(ProduitTransforme $produitTransforme)
    {
        if ($produitTransforme->processus->propositionTransformation->transformateur_id !== Auth::id()) {
            return back()->with('error', 'Vous n\'êtes pas autorisé.');
        }

        if ($produitTransforme->photo) {
            Storage::disk('public')->delete($produitTransforme->photo);
        }

        $produitTransforme->delete();

        return back()->with('success', 'Produit supprimé avec succès!');
    }
}
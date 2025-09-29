<?php

namespace App\Http\Controllers;

use App\Models\ProduitTransforme;
use App\Models\ProcessusTransformation;
use App\Http\Requests\StoreProduitTransformeRequest;
use App\Http\Requests\UpdateProduitTransformeRequest;

class ProduitTransformeController extends Controller
{
    public function index(){
        $produits = ProduitTransforme::with('processus')->latest()->paginate(10);
        return view('backoffice.pages.transformation.produits.index', compact('produits'));
    }

    public function create(){
        $processus = ProcessusTransformation::pluck('id','id');
        return view('backoffice.pages.transformation.produits.create', compact('processus'));
    }

    public function store(StoreProduitTransformeRequest $request){
        $data = $request->validated();
        ProduitTransforme::create($data);
        return redirect()->route('produit-transformés.index')->with('ok','Produit Transformé créé');
    }

    public function show(ProduitTransforme $produitTransforme){
        $produitTransforme->load('processus.propositionTransformation.proposition.postDechet');
        return view('backoffice.pages.transformation.produits.show', compact('produitTransforme'));
    }

    public function edit(ProduitTransforme $produitTransforme){
        $processus = ProcessusTransformation::pluck('id','id');
        return view('backoffice.pages.transformation.produits.edit', compact('produitTransforme','processus'));
    }

    public function update(UpdateProduitTransformeRequest $request, ProduitTransforme $produitTransforme){
        $data = $request->validated();
        $produitTransforme->update($data);
        return redirect()->route('produit-transformés.index')->with('ok','Produit Transformé mis à jour');
    }

    public function destroy(ProduitTransforme $produitTransforme){
        $produitTransforme->delete();
        return back()->with('ok','Produit Transformé supprimé');
    }
}

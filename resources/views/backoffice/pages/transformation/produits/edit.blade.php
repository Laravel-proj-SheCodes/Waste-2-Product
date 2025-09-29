@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Détails Produit #{{ $produitTransforme->id }}</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nom Produit</dt>
                <dd class="col-sm-9">{{ $produitTransforme->nom_produit }}</dd>

                <dt class="col-sm-3">Processus ID</dt>
                <dd class="col-sm-9">{{ $produitTransforme->processus_id }}</dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $produitTransforme->description ?? '—' }}</dd>

                <dt class="col-sm-3">Quantité Produite</dt>
                <dd class="col-sm-9">{{ $produitTransforme->quantite_produite }}</dd>

                <dt class="col-sm-3">Valeur Ajoutée</dt>
                <dd class="col-sm-9">{{ $produitTransforme->valeur_ajoutee }}</dd>

                <dt class="col-sm-3">Prix Vente</dt>
                <dd class="col-sm-9">{{ $produitTransforme->prix_vente ?? '—' }}</dd>
            </dl>

            <div class="mt-4">
                <a href="{{ route('produit-transformes.index') }}" class="btn btn-outline-secondary">← Retour</a>
                <a href="{{ route('produit-transformes.edit', $produitTransforme) }}" class="btn btn-warning">Modifier</a>
            </div>
        </div>
    </div>
</div>
@endsection

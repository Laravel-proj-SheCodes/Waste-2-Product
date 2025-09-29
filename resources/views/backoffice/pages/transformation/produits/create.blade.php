@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
    <h3>Ajouter un Produit Transformé</h3>

    <form action="{{ route('produit-transformes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="processus_id" class="form-label">Processus ID</label>
            <select name="processus_id" id="processus_id" class="form-control">
                @foreach($processus as $id)
                    <option value="{{ $id }}">{{ $id }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nom_produit" class="form-label">Nom Produit</label>
            <input type="text" name="nom_produit" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label for="quantite_produite" class="form-label">Quantité Produite</label>
            <input type="number" name="quantite_produite" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="valeur_ajoutee" class="form-label">Valeur Ajoutée</label>
            <input type="number" name="valeur_ajoutee" step="0.01" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="prix_vente" class="form-label">Prix Vente</label>
            <input type="number" name="prix_vente" step="0.01" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Créer</button>
        <a href="{{ route('produit-transformes.index') }}" class="btn btn-secondary">Retour</a>
    </form>
</div>
@endsection

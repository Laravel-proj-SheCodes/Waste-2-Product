@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h3 class="mb-0">Ajouter un Produit Transformé</h3>
        </div>
        <div class="card-body">
            @if(session('ok'))
                <div class="alert alert-success">{{ session('ok') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('produit-transformes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="processus_id" class="form-label">Processus Associé</label>
                    <select name="processus_id" id="processus_id" class="form-control" required>
                        <option value="">Sélectionner un processus</option>
                        @foreach($processus as $id => $process)
                            <option value="{{ $id }}">{{ "Processus #{$id}" }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nom_produit" class="form-label">Nom du Produit</label>
                    <input type="text" name="nom_produit" id="nom_produit" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="quantite_produite" class="form-label">Quantité Produite</label>
                    <input type="number" name="quantite_produite" id="quantite_produite" class="form-control" min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="valeur_ajoutee" class="form-label">Valeur Ajoutée (DT)</label>
                    <input type="number" name="valeur_ajoutee" id="valeur_ajoutee" class="form-control" min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="prix_vente" class="form-label">Prix de Vente (DT)</label>
                    <input type="number" name="prix_vente" id="prix_vente" class="form-control" min="0" step="0.01">
                </div>

                <div class="mb-3">
                    <label for="photo" class="form-label">Photo du Produit</label>
                    <input type="file" name="photo" id="photo" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Créer le Produit</button>
                    <a href="{{ route('produit-transformes.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
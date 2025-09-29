@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Produits Transformés</h3>
        <a href="{{ route('produit-transformes.create') }}" class="btn btn-primary">Ajouter un Produit</a>
    </div>

    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom Produit</th>
                <th>Processus ID</th>
                <th>Quantité Produite</th>
                <th>Valeur Ajoutée</th>
                <th>Prix Vente</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
                <tr>
                    <td>{{ $produit->id }}</td>
                    <td>{{ $produit->nom_produit }}</td>
                    <td>{{ $produit->processus_id }}</td>
                    <td>{{ $produit->quantite_produite }}</td>
                    <td>{{ $produit->valeur_ajoutee }}</td>
                    <td>{{ $produit->prix_vente ?? '—' }}</td>
                    <td>
                        <a href="{{ route('produit-transformes.show', $produit) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('produit-transformes.edit', $produit) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('produit-transformes.destroy', $produit) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $produits->links() }}
</div>
@endsection

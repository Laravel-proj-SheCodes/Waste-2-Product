@extends('frontoffice.layouts.layoutfront')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4" style="color: #2a5d3a;">Offres de Troc</h1>

        @if ($offres->isEmpty())
            <p class="text-gray-600">Aucune offre de troc pour le moment.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($offres as $offre)
                    <div class="troc-card">
                        @php
                            $photoPaths = $offre->photos ?? [];
                        @endphp
                        @if (!empty($photoPaths))
                            <img src="{{ asset('storage/' . (is_array($photoPaths) ? $photoPaths[0] : json_decode($photoPaths)[0])) }}" alt="{{ $offre->description }}" class="troc-image">
                        @else
                            <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
                        @endif
                        <h2>{{ $offre->description }}</h2>
                        <p>Catégorie : {{ $offre->categorie }}</p>
                        <p>Quantité : {{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
                        <p>État : {{ ucfirst($offre->etat) }}</p>
                        <p>Localisation : {{ $offre->localisation }}</p>
                        <p>Statut : {{ ucfirst($offre->status) }}</p>
                        <div class="mt-4">
                            <a href="{{ route('offres-troc.show.front', $offre->post_dechet_id) }}" class="troc-btn troc-btn-details">Voir Détails</a>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $offres->links() }}
        @endif
    </div>
@endsection
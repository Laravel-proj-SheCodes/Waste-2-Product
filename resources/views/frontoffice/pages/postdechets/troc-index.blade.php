@extends('frontoffice.layouts.layoutfront')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
@endsection

@section('content')
    <div class="container mx-auto p-3">
        <h1 class="text-xl font-bold mb-3" style="color: #2a5d3a;">Posts de Troc</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($posts->isEmpty())
            <p class="text-gray-600">Aucun post de troc disponible pour le moment.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($posts as $post)
                    <div class="troc-card">
                        @php
                            $photoPaths = $post->photos ?? [];
                        @endphp
                        @if (!empty($photoPaths))
                            <img src="{{ asset('storage/' . $photoPaths[0]) }}" alt="{{ $post->titre }}" class="troc-image">
                        @else
                            <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
                        @endif
                        <h2>{{ $post->titre }}</h2>
                        <p>Description : {{ $post->description }}</p>
                        <p>Quantité : {{ $post->quantite }} {{ $post->unite_mesure }}</p>
                        <p>État : {{ ucfirst($post->etat) }}</p>
                        <p>Statut : {{ ucfirst($post->statut) }}</p>
                        <div class="mt-3 space-x-1">
                            <a href="{{ route('postdechets.show', $post->id) }}" class="troc-btn troc-btn-details">Voir Détails</a>
                            <a href="{{ route('offres-troc.create.front', $post->id) }}" class="troc-btn troc-btn-propose">Proposer une Offre</a>
                            <a href="{{ route('offres-troc.show.front', $post->id) }}" class="troc-btn" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); border: 2px solid #9b59b6; box-shadow: 0 3px 10px rgba(155, 89, 182, 0.3);">Voir Offres</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection
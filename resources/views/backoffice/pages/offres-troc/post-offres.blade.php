@extends('backoffice.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">

@endsection

@section('content')
    <div class="container mx-auto p-3">
        <!-- Détails du post en haut -->
        <div class="troc-card mb-4">
            <h2 class="text-lg font-bold" style="color: #2a5d3a;">Détails du Post : {{ $post->titre }}</h2>
            @php
                $photoPaths = $post->photos ?? [];
            @endphp
            @if (!empty($photoPaths))
                <img src="{{ asset('storage/' . $photoPaths[0]) }}" alt="{{ $post->titre }}" class="troc-image">
            @else
                <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
            @endif
            <p><strong>Description :</strong> {{ $post->description }}</p>
            <p><strong>Quantité :</strong> {{ $post->quantite }} {{ $post->unite_mesure }}</p>
            <p><strong>État :</strong> {{ ucfirst($post->etat) }}</p>
            <p><strong>Localisation :</strong> {{ $post->localisation }}</p>
            <p><strong>Statut :</strong> {{ ucfirst($post->statut) }}</p>
            <div class="mt-3">
                <a href="{{ route('postdechets.troc') }}" class="troc-btn troc-btn-details">Retour à la Liste</a>
            </div>
        </div>

        <!-- Liste des offres en bas -->
        <h2 class="text-lg font-bold mb-3" style="color: #2a5d3a;">Offres Associées</h2>
        
        @php
            // VÉRIFICATION DIRECTE DANS LA VUE - CORRECTION ICI
            $hasAcceptedOffer = $offres->contains(function($offre) {
                return strtolower($offre->status) === 'accepted';
            });
        @endphp

        <!-- Message si une offre est acceptée -->
        @if($hasAcceptedOffer)
            <div class="offre-accepted-message">
                ✓ Une offre a été acceptée pour ce post. Les boutons d'action sont désactivés.
            </div>
        @endif

        @if ($offres->isEmpty())
            <p class="text-gray-600">Aucune offre associée pour ce post.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($offres as $offre)
                    <div class="troc-card relative @if(strtolower($offre->status) === 'accepted') offre-accepted @endif">
                        <!-- Menu déroulant avec trois points -->
                        <div class="dropdown absolute top-2 left-2">
                            <span class="dropdown-btn">⋮</span>
                            <div class="dropdown-content">
                                <a href="{{ route('offres-troc.edit', $offre->id) }}">Modifier</a>
                                <form action="{{ route('offres-troc.destroy', $offre->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" style="border: none; background: none; padding: 0; width: 100%; text-align: left;">Supprimer</button>
                                </form>
                            </div>
                        </div>

                        @php
                            $photoPaths = $offre->photos ?? [];
                            $offreStatus = strtolower($offre->status);
                            $isRejected = $offreStatus === 'rejected';
                            $showButtons = !$hasAcceptedOffer && !$isRejected;
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

                        <!-- Boutons Accepter et Refuser -->
                        @if($showButtons)
                            <div class="action-btns">
                                <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST" style="width: 100%;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="accept-btn">Accepter</button>
                                </form>
                                <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST" style="width: 100%;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="reject-btn">Refuser</button>
                                </form>
                            </div>
                        @elseif($offreStatus === 'accepted')
                            <div class="text-center mt-3 p-2 bg-green-100 text-green-700 rounded-lg">
                                <strong>✓ Cette offre a été acceptée</strong>
                            </div>
                        @elseif($offreStatus === 'rejected')
                            <div class="text-center mt-3 p-2 bg-red-100 text-red-700 rounded-lg">
                                <strong>✗ Cette offre a été refusée</strong>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
@extends('frontoffice.layouts.layoutfront')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
    <style>
        .action-btns {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .accept-btn, .reject-btn {
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            border: none;
            cursor: pointer;
        }

        .accept-btn {
            background-color: #2ecc71;
            color: #fff;
        }

        .accept-btn:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }

        .reject-btn {
            background-color: #e74c3c;
            color: #fff;
        }

        .reject-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .offre-accepted {
            background: linear-gradient(135deg, #e8f5e8 0%, #d0f0d0 100%);
            border: 2px solid #2ecc71;
            box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);
        }

        .offre-accepted-message {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 1rem;
            font-weight: bold;
        }
    </style>
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
                <a href="{{ route('postdechets.troc.front') }}" class="troc-btn troc-btn-details">Retour à la Liste</a>
            </div>
        </div>

        <!-- Liste des offres en bas -->
        <h2 class="text-lg font-bold mb-3" style="color: #2a5d3a;">Offres Associées</h2>
        
        @php
            $hasAcceptedOffer = $offres->contains(function($offre) {
                return strtolower($offre->status) === 'accepted';
            });
        @endphp

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

                        <!-- Boutons Accepter et Refuser - SEULEMENT si l'utilisateur est le propriétaire du post -->
                        @if(auth()->check() && auth()->id() == $post->user_id && $showButtons)
                            <div class="action-btns">
                                <form action="{{ route('offres-troc.update-statut.front', $offre->id) }}" method="POST" style="width: 100%;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="accept-btn">Accepter</button>
                                </form>
                                <form action="{{ route('offres-troc.update-statut.front', $offre->id) }}" method="POST" style="width: 100%;">
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
@extends('frontoffice.layouts.layoutfront')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
    <style>
        .troc-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .troc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .troc-image {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1rem;
            background: #f0fff0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2a5d3a;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .troc-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1f4d2f;
        }

        .troc-card p {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            color: #555;
        }

        .troc-btn {
            display: inline-block;
            text-decoration: none;
            background: #2a5d3a;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: background 0.2s;
        }

        .troc-btn:hover {
            background: #1f4d2f;
        }

        /* Pagination styling */
        .pagination {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .pagination li a,
        .pagination li span {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            color: #2a5d3a;
        }

        .pagination li.active span {
            background-color: #2a5d3a;
            color: #fff;
            border-color: #2a5d3a;
        }

        .pagination li a:hover {
            background-color: #e6ffe6;
        }
    </style>
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
                        $photoSrc = asset('images/placeholder.jpg');
                        if(!empty($photoPaths)) {
                            $photoSrc = asset('storage/' . (is_array($photoPaths) ? $photoPaths[0] : json_decode($photoPaths)[0]));
                        }
                    @endphp

                    @if (!empty($photoPaths))
                        <img src="{{ $photoSrc }}" alt="{{ $offre->description }}" class="troc-image">
                    @else
                        <div class="troc-image">Pas d'image</div>
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

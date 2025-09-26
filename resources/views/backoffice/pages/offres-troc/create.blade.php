@extends('backoffice.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
    <style>
        /* Style spécifique pour le formulaire */
        .troc-form {
            background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e6ffe6;
        }

        .troc-form label {
            color: #3c6e4d;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .troc-form input,
        .troc-form select,
        .troc-form textarea {
            border-color: #e6ffe6;
            background: #f9fffb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-size: 0.875rem;
        }

        .troc-form input:focus,
        .troc-form select:focus,
        .troc-form textarea:focus {
            border-color: #2ecc71;
            box-shadow: 0 0 5px rgba(46, 204, 113, 0.3);
            outline: none;
        }

        .troc-form .error {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endsection

@section('content')
    <div class="container mx-auto p-3">
        <!-- Carte pour présenter le post -->
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
        </div>

        <h1 class="text-xl font-bold mb-3" style="color: #2a5d3a;">Créer une Offre de Troc</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="troc-form">
            <form action="{{ route('offres-troc.store', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <input type="text" name="categorie" id="categorie" value="{{ old('categorie') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('categorie')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                    <input type="number" name="quantite" id="quantite" value="{{ old('quantite') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required min="1">
                    @error('quantite')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="unite_mesure" class="block text-sm font-medium text-gray-700">Unité de Mesure</label>
                    <select name="unite_mesure" id="unite_mesure" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="kg" {{ old('unite_mesure') == 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="litres" {{ old('unite_mesure') == 'litres' ? 'selected' : '' }}>litres</option>
                        <option value="unités" {{ old('unite_mesure') == 'unités' ? 'selected' : '' }}>unités</option>
                    </select>
                    @error('unite_mesure')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="etat" class="block text-sm font-medium text-gray-700">État</label>
                    <select name="etat" id="etat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="neuf" {{ old('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                        <option value="usagé" {{ old('etat') == 'usagé' ? 'selected' : '' }}>Usagé</option>
                        <option value="endommagé" {{ old('etat') == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
                    </select>
                    @error('etat')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="localisation" class="block text-sm font-medium text-gray-700">Localisation</label>
                    <input type="text" name="localisation" id="localisation" value="{{ old('localisation') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    @error('localisation')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="photos" class="block text-sm font-medium text-gray-700">Photos</label>
                    <input type="file" name="photos[]" id="photos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple>
                    @error('photos')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="troc-btn troc-btn-propose">Créer l'Offre</button>
            </form>
        </div>
    </div>
@endsection

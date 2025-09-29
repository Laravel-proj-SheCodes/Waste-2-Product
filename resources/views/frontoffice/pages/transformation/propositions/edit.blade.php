@extends('frontoffice.layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-6">Modifier Proposition</h2>

    <form action="{{ route('front.propositions.update', $proposition->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-semibold">Description</label>
            <textarea name="description" class="w-full border rounded-lg p-2" rows="4">{{ old('description', $proposition->description) }}</textarea>
            @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block font-semibold">Statut</label>
            <select name="statut" class="w-full border rounded-lg p-2">
                <option value="en_attente" {{ $proposition->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="accepte" {{ $proposition->statut == 'accepte' ? 'selected' : '' }}>Accepté</option>
                <option value="refuse" {{ $proposition->statut == 'refuse' ? 'selected' : '' }}>Refusé</option>
            </select>
            @error('statut') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Sauvegarder
        </button>
    </form>
</div>
@endsection

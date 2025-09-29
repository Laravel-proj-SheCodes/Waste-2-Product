@extends('frontoffice.layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-6">Détails Proposition</h2>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="font-semibold text-lg">{{ $proposition->postDechet->titre ?? 'Post supprimé' }}</h3>
        <p class="text-gray-600 mt-2">{{ $proposition->description }}</p>

        <div class="mt-4">
            <span class="px-2 py-1 text-xs rounded-full 
                @if($proposition->statut === 'en_attente') bg-yellow-100 text-yellow-800 
                @elseif($proposition->statut === 'accepte') bg-green-100 text-green-800 
                @else bg-red-100 text-red-800 @endif">
                {{ ucfirst($proposition->statut) }}
            </span>
        </div>
    </div>

    <div class="mt-6 flex gap-4">
        <a href="{{ route('front.propositions.edit', $proposition->id) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Modifier</a>
        <form action="{{ route('front.propositions.destroy', $proposition->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Supprimer</button>
        </form>
    </div>
</div>
@endsection

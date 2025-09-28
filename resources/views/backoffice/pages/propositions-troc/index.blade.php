@extends('backoffice.layouts.layout')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Propositions de Troc</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($posts->isEmpty())
            <p class="text-gray-600">Aucun post de troc disponible pour le moment.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($posts as $post)
                    <div class="bg-white p-4 rounded shadow hover:shadow-md transition">
                        <h2 class="text-xl font-semibold">{{ $post->titre }}</h2>
                        <p class="text-gray-600">Description : {{ $post->description }}</p>
                        <p class="text-gray-600">Quantité : {{ $post->quantite }} {{ $post->unite_mesure }}</p>
                        <p class="text-gray-600">État : {{ ucfirst($post->etat) }}</p>
                        <p class="text-gray-600">Statut : {{ ucfirst($post->statut) }}</p>
                        <p class="text-gray-600">Propositions existantes : {{ $post->propositions->count() }}</p>
                        <div class="mt-4 space-x-2">
                            <a href="{{ route('postdechets.show', $post->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded">Détails</a>
                            <a href="{{ route('propositions-troc.create', $post->id) }}" class="bg-green-500 text-white px-3 py-1 rounded">Proposer</a>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $posts->links() }} <!-- Pagination -->
        @endif
    </div>
@endsection
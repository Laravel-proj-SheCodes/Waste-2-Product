@extends('frontoffice.layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <h2 class="text-2xl font-bold mb-6">Mes Propositions</h2>

    @if($propositions->isEmpty())
        <p class="text-gray-600">Aucune proposition trouvée.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($propositions as $proposition)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg">{{ $proposition->postDechet->titre ?? 'Post supprimé' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($proposition->description, 80) }}</p>

                        <div class="mt-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($proposition->statut === 'en_attente') bg-yellow-100 text-yellow-800 
                                @elseif($proposition->statut === 'accepte') bg-green-100 text-green-800 
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($proposition->statut) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4 border-t flex justify-between">
                        <a href="{{ route('front.propositions.show', $proposition->id) }}" class="text-blue-600 hover:underline">Voir</a>
                        <a href="{{ route('front.propositions.edit', $proposition->id) }}" class="text-green-600 hover:underline">Modifier</a>
                        <form action="{{ route('front.propositions.destroy', $proposition->id) }}" method="POST" onsubmit="return confirm('Supprimer ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $propositions->links() }}
        </div>
    @endif
</div>
@endsection

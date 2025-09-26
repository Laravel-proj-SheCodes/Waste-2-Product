@extends('backoffice.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-troc.css') }}">
@endsection

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4" style="color: #2a5d3a;">Créer une Proposition pour : {{ $post->titre }}</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('propositions-troc.store', $post->id) }}" method="POST" class="max-w-lg">
            @csrf
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-#2a5d3a">Description de la Proposition</label>
                <textarea name="description" id="description" rows="4" class="w-full p-2 border border-gray-300 rounded mt-1" required>{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="troc-btn troc-btn-propose">Soumettre la Proposition</button>
        </form>
    </div>
@endsection
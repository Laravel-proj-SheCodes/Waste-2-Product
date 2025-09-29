@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Éditer Proposition #{{ $propositionTransformation->id }}</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('proposition-transformations.update', $propositionTransformation) }}">
        @csrf
        @method('PUT')

        @include('backoffice.pages.transformation.propositions.form', [
            'p' => $propositionTransformation,
            'posts' => $posts,
            'transformateurs' => $transformateurs
        ])

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('proposition-transformations.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button class="btn btn-primary">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Détails Proposition #{{ $propositionTransformation->id }}</h5>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Post Déchet</dt>
        <dd class="col-sm-9">{{ $propositionTransformation->proposition->postDechet->titre ?? '—' }}</dd>

        <dt class="col-sm-3">Transformateur</dt>
        <dd class="col-sm-9">{{ $propositionTransformation->transformateur->name ?? '—' }}</dd>

        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">{{ $propositionTransformation->description ?? '—' }}</dd>

        <dt class="col-sm-3">Statut</dt>
        <dd class="col-sm-9">
          <span class="badge 
            {{ $propositionTransformation->statut=='accepté'?'bg-success':
               ($propositionTransformation->statut=='refusé'?'bg-danger':'bg-secondary') }}">
            {{ ucfirst($propositionTransformation->statut) }}
          </span>
        </dd>
      </dl>

      <div class="mt-4">
        <a href="{{ route('proposition-transformations.index') }}" class="btn btn-outline-secondary">← Retour</a>
        <a href="{{ route('proposition-transformations.edit', $propositionTransformation) }}" class="btn btn-warning">Modifier</a>
      </div>
    </div>
  </div>
</div>
@endsection

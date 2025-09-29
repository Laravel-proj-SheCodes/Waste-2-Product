@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Détails Processus #{{ $processus->id }}</h5>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Proposition</dt>
        <dd class="col-sm-9">
          {{ $processus->propositionTransformation->proposition->description ?? '—' }}
        </dd>

        <dt class="col-sm-3">Déchet Entrant</dt>
        <dd class="col-sm-9">
          {{ $processus->dechetEntrant->libelle ?? '—' }}
        </dd>

        <dt class="col-sm-3">Durée Estimée</dt>
        <dd class="col-sm-9">
          {{ $processus->duree_estimee ?? '—' }} jours
        </dd>

        <dt class="col-sm-3">Coût</dt>
        <dd class="col-sm-9">
          {{ $processus->cout ?? '—' }} €
        </dd>

        <dt class="col-sm-3">Équipements</dt>
        <dd class="col-sm-9">
          {{ $processus->equipements ?? '—' }}
        </dd>

        <dt class="col-sm-3">Statut</dt>
        <dd class="col-sm-9">
          <span class="badge {{ $processus->statut=='terminé'?'bg-success':($processus->statut=='en_cours'?'bg-warning':'bg-secondary') }}">
            {{ ucfirst($processus->statut) }}
          </span>
        </dd>

        <dt class="col-sm-3">Date Création</dt>
        <dd class="col-sm-9">
          {{ $processus->created_at->format('d/m/Y H:i') ?? '—' }}
        </dd>
      </dl>

      <div class="mt-4">
        <a href="{{ route('processus-transformations.index') }}" class="btn btn-outline-secondary">← Retour</a>
        <a href="{{ route('processus-transformations.edit', $processus) }}" class="btn btn-warning">Modifier</a>
      </div>
    </div>
  </div>
</div>
@endsection

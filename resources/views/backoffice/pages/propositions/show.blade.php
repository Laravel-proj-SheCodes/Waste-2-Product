@extends('backoffice.layouts.layout')
@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Détails de la proposition</h5>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Post</dt>
        <dd class="col-sm-9">{{ $proposition->postDechet->titre ?? '—' }}</dd>

        <dt class="col-sm-3">Utilisateur</dt>
        <dd class="col-sm-9">{{ $proposition->user->name ?? '—' }}</dd>

        <dt class="col-sm-3">Statut</dt>
        <dd class="col-sm-9">
          @if($proposition->statut == 'accepte')
            <span class="badge bg-success">Acceptée</span>
          @elseif($proposition->statut == 'refuse')
            <span class="badge bg-danger">Refusée</span>
          @else
            <span class="badge bg-secondary">En attente</span>
          @endif
        </dd>

        <dt class="col-sm-3">Date de proposition</dt>
<dd class="col-sm-9">{{ \Carbon\Carbon::parse($proposition->date_proposition)->format('d/m/Y') }}</dd>


        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">{{ $proposition->description }}</dd>
      </dl>

      <div class="mt-4">
        <a href="{{ route('propositions.index') }}" class="btn btn-outline-secondary">
          ← Retour à la liste
        </a>
        <a href="{{ route('propositions.edit', $proposition) }}" class="btn btn-warning">
          Modifier
        </a>
      </div>
    </div>
  </div>
</div>
@endsection

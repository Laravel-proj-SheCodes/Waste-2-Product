@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Détails du post déchet</h5>
    </div>

    <div class="card-body">
      <dl class="row mb-0">

        <dt class="col-sm-3">Titre</dt>
        <dd class="col-sm-9">{{ $postdechet->titre }}</dd>

        <dt class="col-sm-3">Type</dt>
        <dd class="col-sm-9 text-capitalize">{{ $postdechet->type_post }}</dd>

        <dt class="col-sm-3">Catégorie</dt>
        <dd class="col-sm-9">{{ $postdechet->categorie }}</dd>

        <dt class="col-sm-3">Quantité</dt>
        <dd class="col-sm-9">
          {{ rtrim(rtrim(number_format($postdechet->quantite,2,'.',' '), '0'), '.') }}
          {{ $postdechet->unite_mesure }}
        </dd>

        <dt class="col-sm-3">État</dt>
        <dd class="col-sm-9">
          @php($etat = $postdechet->etat)
          <span class="badge {{ $etat === 'neuf' ? 'bg-success' : ($etat === 'usagé' ? 'bg-warning' : 'bg-secondary') }}">
            {{ ucfirst($etat) }}
          </span>
        </dd>

        <dt class="col-sm-3">Localisation</dt>
        <dd class="col-sm-9">{{ $postdechet->localisation }}</dd>

        <dt class="col-sm-3">Producteur</dt>
        <dd class="col-sm-9">{{ $postdechet->user->name ?? '—' }}</dd>

        <dt class="col-sm-3">Date de publication</dt>
        <dd class="col-sm-9">{{ optional($postdechet->date_publication)->format('d/m/Y') }}</dd>

        <dt class="col-sm-3">Statut</dt>
        <dd class="col-sm-9">
          @php($st = $postdechet->statut ?? 'en_attente')
          <span class="badge
            {{ $st==='terminé' ? 'bg-success' : ($st==='en_cours' ? 'bg-warning' : 'bg-secondary') }}">
            {{ str_replace('_',' ', ucfirst($st)) }}
          </span>
        </dd>

        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">{{ $postdechet->description }}</dd>

        @if(!empty($postdechet->photos))
          <dt class="col-sm-3">Photos</dt>
          <dd class="col-sm-9">
            <div class="d-flex gap-2 flex-wrap">
              @foreach((array)$postdechet->photos as $path)
                <img src="{{ asset('storage/'.$path) }}" alt="photo" class="rounded border" style="height:88px">
              @endforeach
            </div>
          </dd>
        @endif

      </dl>

      <div class="mt-4">
        <a href="{{ route('postdechets.index') }}" class="btn btn-outline-secondary">← Retour à la liste</a>
        <a href="{{ route('postdechets.edit', $postdechet) }}" class="btn btn-warning">Modifier</a>
      </div>
    </div>
  </div>

  {{-- Bloc Propositions reliées --}}
  <div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
      <h6 class="mb-0">Propositions</h6>
    </div>
    <div class="card-body">
      @forelse($postdechet->propositions as $pr)
        <div class="list-group mb-2">
          <div class="list-group-item d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">{{ $pr->user->name ?? '—' }}</div>
              <div class="text-muted small">
                {{ $pr->date_proposition?->format('d/m/Y') }} —
                {{ $pr->description }}
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              @php($pst = $pr->statut ?? 'en_attente')
              <span class="badge
                {{ $pst==='accepte' ? 'bg-success' : ($pst==='refuse' ? 'bg-danger' : 'bg-secondary') }}">
                {{ strtoupper($pst) }}
              </span>
              <a href="{{ route('propositions.show',$pr) }}" class="btn btn-sm btn-info">Voir</a>
              <form method="POST" action="{{ route('propositions.destroy',$pr) }}"
                    onsubmit="return confirm('Supprimer cette proposition ?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-muted">Aucune proposition pour ce post.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection

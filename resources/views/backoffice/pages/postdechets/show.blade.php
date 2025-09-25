@extends('backoffice.layouts.layout')
@section('content')
<div class="container py-4">
  <h4>{{ $postdechet->titre }}</h4>
  <p class="text-muted">{{ $postdechet->categorie }} • {{ $postdechet->type_post }} • {{ $postdechet->quantite }} {{ $postdechet->unite_mesure }}</p>
  <p>{{ $postdechet->description }}</p>

  <h5 class="mt-4">Propositions</h5>
  <ul class="list-group">
    @forelse($postdechet->propositions as $pr)
      <li class="list-group-item d-flex justify-content-between">
        <span>{{ $pr->description }} — <em>{{ $pr->statut }}</em></span>
        <form method="POST" action="{{ route('propositions.destroy',$pr) }}">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Supprimer</button>
        </form>
      </li>
    @empty
      <li class="list-group-item">Aucune proposition.</li>
    @endforelse
  </ul>
</div>
@endsection

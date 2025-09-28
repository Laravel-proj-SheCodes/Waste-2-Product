@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
  <h1 class="h4 mb-4">Propositions reçues</h1>

  @forelse($propositions as $p)
    <div class="border rounded p-3 mb-3 d-flex justify-content-between">
      <div>
        <div class="fw-semibold">{{ $p->postDechet->titre }}</div>
        <div class="text-muted small">de {{ $p->user->name }} — {{ $p->created_at->format('d/m/Y H:i') }}</div>
        <div class="mt-2">{{ $p->description }}</div>
      </div>
      <span class="badge bg-secondary align-self-start text-capitalize">{{ str_replace('_',' ', $p->statut) }}</span>
    </div>
  @empty
    <p class="text-muted">Aucune proposition reçue pour le moment.</p>
  @endforelse

  {{ $propositions->links() }}
</div>
@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
  <h1 class="h4 mb-4">Propositions reçues</h1>

  @php
    $highlightId = (int) request('highlight');
  @endphp

  @forelse($propositions as $p)
    @php
      // On prend created_at s'il existe, sinon date_proposition
      $when = $p->created_at ?: $p->date_proposition;
      $whenStr = $when ? \Illuminate\Support\Carbon::parse($when)->format('d/m/Y H:i') : '';
    @endphp

    <div id="prop-{{ $p->id }}"
         class="border rounded p-3 mb-3 d-flex justify-content-between align-items-start proposition-item @if($highlightId === (int) $p->id) glow-target @endif">

      <div>
        <div class="fw-semibold">{{ $p->postDechet?->titre ?? '—' }}</div>
        <div class="text-muted small">
          de {{ $p->user?->name ?? 'Utilisateur' }} @if($whenStr) — {{ $whenStr }} @endif
        </div>
        <div class="mt-2">{{ $p->description }}</div>
      </div>

      <div class="text-end">
        <span class="badge bg-secondary text-capitalize">
          {{ str_replace('_',' ', $p->statut ?? 'en_attente') }}
        </span>

        <div class="mt-2">
          {{-- Boutons accepter / refuser --}}
          <form action="{{ route('front.propositions.accept', $p) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Accepter</button>
          </form>

          <form action="{{ route('front.propositions.reject', $p) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">Refuser</button>
          </form>
        </div>
      </div>

    </div>
  @empty
    <p class="text-muted">Aucune proposition reçue pour le moment.</p>
  @endforelse

  {{ $propositions->links() }}
</div>

{{-- Surbrillance 5s + scroll --}}
<style>
  .glow-target {
    animation: glowPulse 1s ease-in-out 5 alternate;
    box-shadow: 0 0 0 rgba(255, 193, 7, 0);
  }
  @keyframes glowPulse {
    0%   { box-shadow: 0 0 0 rgba(255, 193, 7, 0); background-color: transparent; }
    50%  { box-shadow: 0 0 18px rgba(255, 193, 7, .8); background-color: rgba(255, 243, 205, .6); }
    100% { box-shadow: 0 0 0 rgba(255, 193, 7, 0); background-color: transparent; }
  }
</style>

<script>
  (function () {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('highlight');
    if (!id) return;

    const el = document.getElementById('prop-' + id);
    if (!el) return;

    // scroll dans la page jusqu'à l’élément
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // retirer la classe après ~5 secondes
    setTimeout(() => el.classList.remove('glow-target'), 5200);
  })();
</script>
@endsection

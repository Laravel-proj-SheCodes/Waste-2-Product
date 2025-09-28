@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
  <h1 class="h4 mb-4">Mes propositions</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @forelse($propositions as $p)
    <div class="card mb-3">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-semibold">
            {{ $p->postDechet?->titre ?? '—' }}
          </div>
          <div class="text-muted small mt-1">{{ $p->description }}</div>
        </div>
        <div class="text-end">
          <span class="badge {{ $p->statut === 'acceptee' ? 'bg-success' : ($p->statut === 'refusee' ? 'bg-danger' : 'bg-secondary') }}">
            {{ str_replace('_',' ', $p->statut) }}
          </span>
          <div class="mt-2">
            <a class="btn btn-outline-secondary btn-sm"
               href="{{ route('front.propositions.edit', $p) }}">Modifier</a>
            <form class="d-inline" method="POST"
                  action="{{ route('front.propositions.destroy', $p) }}"
                  onsubmit="return confirm('Supprimer cette proposition ?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">Supprimer</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @empty
    <p class="text-muted">Vous n’avez pas encore de propositions.</p>
  @endforelse

  <div class="mt-3">
    {{ $propositions->links() }}
  </div>
</div>
@endsection

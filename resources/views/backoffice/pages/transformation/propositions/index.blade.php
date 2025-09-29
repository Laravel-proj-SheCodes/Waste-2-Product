@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Propositions de Transformation</h5>
    </div>
    <div class="card-body">
      <a href="{{ route('proposition-transformations.create') }}" class="btn btn-primary mb-3">Créer</a>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Post Déchet</th>
            <th>Transformateur</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
  @forelse($propositions as $p)
  <tr>
    <td>{{ $p->id }}</td>
    <td>{{ $p->proposition->postDechet->titre ?? '—' }}</td>
    <td>{{ $p->transformateur->name ?? '—' }}</td>
    <td>
      <span class="badge {{ $p->statut=='accepté'?'bg-success':($p->statut=='refusé'?'bg-danger':'bg-secondary') }}">
        {{ ucfirst($p->statut) }}
      </span>
    </td>
    <td class="d-flex gap-1">
      <a href="{{ route('proposition-transformations.show',$p) }}" class="btn btn-sm btn-info">Voir</a>
      <a href="{{ route('proposition-transformations.edit',$p) }}" class="btn btn-sm btn-warning">Modifier</a>
      <form method="POST" action="{{ route('proposition-transformations.destroy',$p) }}" onsubmit="return confirm('Supprimer cette proposition ?')">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-outline-danger">Supprimer</button>
      </form>
    </td>
  </tr>
  @empty
  <tr><td colspan="5" class="text-center">Aucune proposition trouvée.</td></tr>
  @endforelse
</tbody>

      </table>
      {{ $propositions->links() }}
    </div>
  </div>
</div>
@endsection

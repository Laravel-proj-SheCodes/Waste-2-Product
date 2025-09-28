@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Processus de Transformation</h5>
        </div>
        <div class="card-body">
            <a href="{{ route('processus-transformations.create') }}" class="btn btn-primary mb-3">Créer</a>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proposition</th>
                        <th>Durée estimée (jours)</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processus as $proc)
                    <tr>
                        <td>{{ $proc->id }}</td>
                        <td>{{ $proc->propositionTransformation->description ?? '—' }}</td>
                        <td>{{ $proc->duree_estimee ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $proc->statut=='terminé'?'bg-success':($proc->statut=='en_cours'?'bg-warning':'bg-secondary') }}">
                                {{ ucfirst($proc->statut) }}
                            </span>
                        </td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('processus-transformations.show', $proc) }}" class="btn btn-sm btn-info">Voir</a>
                            <a href="{{ route('processus-transformations.edit', $proc) }}" class="btn btn-sm btn-warning">Modifier</a>
                            <form method="POST" action="{{ route('processus-transformations.destroy', $proc) }}" onsubmit="return confirm('Supprimer ce processus ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Aucun processus trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $processus->links() }}
        </div>
    </div>
</div>
@endsection

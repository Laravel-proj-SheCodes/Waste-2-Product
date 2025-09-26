@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Propositions</h4>
        <a href="{{ route('propositions.create') }}" class="btn btn-primary">Nouvelle proposition</a>
      </div>

      @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
      @endif

      <div class="card my-4">
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Post</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Par</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Statut</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date proposition</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-3">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($propositions as $pr)
                  <tr>
                    <td class="px-3">{{ $pr->postDechet->titre ?? '—' }}</td>
                    <td class="px-3">{{ $pr->user->name ?? '—' }}</td>
                    <td class="px-3 text-truncate" style="max-width:360px">{{ $pr->description }}</td>
                    <td class="px-3">
                      <span class="badge bg-secondary text-uppercase">
                        {{ $pr->statut ?? 'en_attente' }}
                      </span>
                    </td>
<td class="px-3">
  {{ \Carbon\Carbon::parse($pr->date_proposition)->format('d/m/Y') }}
</td>


                    <td class="text-end pe-3">
                      <a class="btn btn-sm btn-info" href="{{ route('propositions.show', $pr) }}">Voir</a>
                      <a class="btn btn-sm btn-warning" href="{{ route('propositions.edit', $pr) }}">Éditer</a>
                      <form class="d-inline" method="POST" action="{{ route('propositions.destroy', $pr) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Suppr.</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Aucune proposition.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="mt-3">
        {{ $propositions->links() }}
      </div>

    </div>
  </div>
</div>
@endsection

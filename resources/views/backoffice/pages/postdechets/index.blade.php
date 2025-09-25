@extends('backoffice.layouts.layout')
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between mb-3">
    <h4>Posts Déchets</h4>
    <a href="{{ route('postdechets.create') }}" class="btn btn-primary">Nouveau</a>
  </div>
  @if(session('ok')) 
    <div class="alert alert-success">{{ session('ok') }}</div> 
  @endif

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Titre</th>
        <th>Type</th>
        <th>Catégorie</th>
        <th>Qté</th>
        <th>Statut</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>
    @foreach($posts as $p)
      <tr>
        <td>{{ $p->titre }}</td>
        <td>{{ $p->type_post }}</td>
        <td>{{ $p->categorie }}</td>
        <td>{{ $p->quantite }} {{ $p->unite_mesure }}</td>
        <td>{{ $p->statut }}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-info" href="{{ route('postdechets.show',$p) }}">Voir</a>
          <a class="btn btn-sm btn-warning" href="{{ route('postdechets.edit',$p) }}">Éditer</a>
          <form class="d-inline" method="POST" action="{{ route('postdechets.destroy',$p) }}">
            @csrf 
            @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Suppr.</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>

  {{ $posts->links() }}
</div>
@endsection

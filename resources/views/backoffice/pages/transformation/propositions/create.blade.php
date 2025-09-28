@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Créer une Proposition de Transformation</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('proposition-transformations.store') }}">
        @csrf
        @include('backoffice.pages.transformation.propositions.form')
        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('proposition-transformations.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

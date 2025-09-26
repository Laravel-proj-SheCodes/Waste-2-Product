@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Créer un Post Déchet</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('postdechets.store') }}" enctype="multipart/form-data">
        @csrf
        @include('backoffice.pages.postdechets.form')
        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('postdechets.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

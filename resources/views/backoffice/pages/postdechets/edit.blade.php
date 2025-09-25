@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Éditer Post #{{ $postdechet->id }}</h5>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('postdechets.update', $postdechet) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('backoffice.pages.postdechets.form')
        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('postdechets.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button class="btn btn-primary">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

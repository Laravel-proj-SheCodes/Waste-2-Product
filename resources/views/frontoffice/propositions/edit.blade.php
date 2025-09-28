@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
  <h1 class="h4 mb-4">Modifier ma proposition</h1>

  @if($errors->any())
    <div class="alert alert-danger">
      Veuillez corriger les champs ci-dessous.
    </div>
  @endif

  <div class="mb-3">
    <div class="small text-muted">Post ciblé</div>
    <div class="fw-semibold">{{ $proposition->postDechet?->titre }}</div>
  </div>

  <form method="POST" action="{{ route('front.propositions.update', $proposition) }}" class="row g-3">
    @csrf @method('PUT')

    <div class="col-12">
      <label class="form-label">Proposition *</label>
      <textarea name="description" rows="5" class="form-control" required>{{ old('description', $proposition->description) }}</textarea>
      @error('description') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-12">
      <button class="btn btn-success">Enregistrer</button>
      <a href="{{ route('front.propositions.index') }}" class="btn btn-outline-secondary">Retour</a>
    </div>
  </form>
</div>
@endsection

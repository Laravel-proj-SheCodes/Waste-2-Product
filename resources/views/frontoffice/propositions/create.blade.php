@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
  <h1 class="h4 mb-4">Proposer une offre</h1>

  @if($errors->any())
    <div class="alert alert-danger">
      Veuillez corriger les champs ci-dessous.
    </div>
  @endif

  <div class="mb-3">
    <div class="small text-muted">Post ciblé</div>
    <div class="fw-semibold">{{ $postDechet->titre }}</div>
  </div>

  <form method="POST" action="{{ route('front.propositions.store', $postDechet) }}">
  @csrf

  <div class="mb-3">
    <label class="form-label">Votre proposition *</label>
    <textarea name="description" class="form-control" rows="6">{{ old('description') }}</textarea>
    @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
  </div>

  <button class="btn btn-success">Envoyer</button>
  <a href="{{ route('front.waste-posts.show', $postDechet) }}" class="btn btn-outline-secondary">Annuler</a>
</form>

</div>
@endsection

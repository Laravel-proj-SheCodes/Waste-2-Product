@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
      <h5 class="mb-0">Créer une proposition</h5>
    </div>

    <div class="card-body">
      {{-- Récapitulatif des erreurs --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <strong>Veuillez corriger les champs en rouge :</strong>
          <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('propositions.store') }}" autocomplete="off">
        @csrf

        {{-- Le formulaire principal (tes champs) --}}
        @include('backoffice.pages.propositions.form')

        <div class="mt-4 d-flex gap-2">
          <a href="{{ route('propositions.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-3">
  <h3 class="mb-4">Profil administrateur</h3>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <div class="row g-3">
    {{-- Colonne gauche : aperçu --}}
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Aperçu</h6></div>
        <div class="card-body">
          <p class="mb-2"><i class="material-symbols-rounded me-2">person</i><strong>Nom :</strong> {{ $user->name }}</p>
          <p class="mb-2"><i class="material-symbols-rounded me-2">mail</i><strong>Email :</strong> {{ $user->email }}</p>
          <p class="mb-2"><i class="material-symbols-rounded me-2">shield_person</i><strong>Rôle :</strong> {{ ucfirst($user->role) }}</p>
          <p class="mb-2"><i class="material-symbols-rounded me-2">verified_user</i>
            <strong>Statut :</strong>
            @if($user->is_active)
              <span class="badge bg-success">Actif</span>
            @else
              <span class="badge bg-secondary">Inactif</span>
            @endif
          </p>
          <p class="mb-0"><i class="material-symbols-rounded me-2">key</i><strong>2FA :</strong>
            @if($user->two_factor_enabled)
              <span class="badge bg-success me-2">Activée</span>
              <form class="d-inline" method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Désactiver</button>
              </form>
            @else
              <span class="badge bg-danger me-2">Désactivée</span>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('two-factor.show') }}">Activer</a>
            @endif
          </p>
        </div>
      </div>
    </div>

    {{-- Colonne droite : formulaires --}}
    <div class="col-lg-8">
      {{-- Modifier infos --}}
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Modifier les informations</h6></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.profile.update') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
              <label class="form-label">Nom complet</label>
              <input
                type="text"
                name="name"
                class="form-control input-visible @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                placeholder="Entrez votre nom"
                required
              >
              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Adresse email</label>
              <input
                type="email"
                name="email"
                class="form-control input-visible @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                placeholder="exemple@domaine.com"
                required
              >
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 text-end">
              <button class="btn btn-success">Mettre à jour</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Modifier mot de passe --}}
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Modifier le mot de passe</h6></div>
        <div class="card-body">
          <form method="POST" action="{{ route('admin.profile.updatePassword') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
              <label class="form-label">Mot de passe actuel</label>
              <input
                type="password"
                name="current_password"
                class="form-control input-visible @error('current_password') is-invalid @enderror"
                placeholder="••••••••"
                required
              >
              @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Nouveau mot de passe</label>
              <input
                type="password"
                name="password"
                class="form-control input-visible @error('password') is-invalid @enderror"
                placeholder="••••••••"
                required
              >
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">Confirmer</label>
              <input
                type="password"
                name="password_confirmation"
                class="form-control input-visible"
                placeholder="••••••••"
                required
              >
            </div>

            <div class="col-12 text-end">
              <button class="btn btn-primary">Mettre à jour le mot de passe</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Force l’apparence visible des champs même avec le thème Material */
  .input-visible{
    background:#fff !important;
    border:1px solid #d1d5db !important;   /* gris clair */
    border-radius:.5rem !important;
    padding:.5rem .75rem !important;
    box-shadow:none !important;
  }
  .input-visible:focus{
    border-color:#4caf50 !important;       /* vert */
    outline:0;
    box-shadow:0 0 0 .2rem rgba(76,175,80,.15) !important;
  }
</style>
@endpush

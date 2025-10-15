@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold">Mon Profil</h2>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    {{-- Formulaire infos personnelles --}}
    <div class="card mb-4 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Informations du profil</h5>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-success w-100">Mettre à jour</button>
            </form>
        </div>
    </div>

    {{-- Formulaire mot de passe --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Modifier le mot de passe</h5>
            <form action="{{ route('profile.updatePassword') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password" name="current_password"
                           class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-dark w-100">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>
@endsection

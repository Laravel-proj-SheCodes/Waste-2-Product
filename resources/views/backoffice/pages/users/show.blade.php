@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Détails de l'utilisateur</h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-2">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible text-white mx-4" role="alert">
                            <span class="text-sm">{{ session('success') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Informations</h5>
                            <p><strong>Nom :</strong> {{ $user->name }}</p>
                            <p><strong>Email :</strong> {{ $user->email }}</p>
                            <p><strong>Rôle :</strong> {{ $user->isAdmin() ? 'Administrateur' : 'Client' }}</p>
                            <p><strong>Statut :</strong>
                                <span class="badge badge-sm {{ $user->is_active ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                            <p><strong>Date de création :</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dernière mise à jour :</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Actions</h5>
                            <form action="{{ route('users.toggleActive', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn {{ $user->is_active ? 'bg-gradient-danger' : 'bg-gradient-success' }} mb-0">
                                    {{ $user->is_active ? 'Désactiver le compte' : 'Activer le compte' }}
                                </button>
                            </form>
                            <a href="{{ route('users.index') }}" class="btn bg-gradient-secondary mb-0">Retour à la liste</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
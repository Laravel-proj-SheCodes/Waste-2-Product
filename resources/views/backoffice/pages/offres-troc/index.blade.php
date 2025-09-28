@extends('backoffice.layouts.layout')

@section('styles')
<style>
    .troc-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .troc-image-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        background: #e6ffe6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2a5d3a;
        font-size: 0.8rem;
        text-align: center;
    }
    .table th, .table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }
    .table th {
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .table td {
        color: #1f2937;
        font-size: 0.875rem;
    }
    .btn-link {
        padding: 0;
        margin: 0 0.5rem;
        font-size: 0.875rem;
    }
    .badge {
        padding: 0.5em 0.75em;
        font-size: 0.75rem;
        border-radius: 0.5rem;
    }
    .bg-gradient-dark {
        background: linear-gradient(87deg, #2a5d3a 0, #1e3f2a 100%) !important;
    }
    .pagination .page-link {
        border-radius: 8px;
        border: 2px solid #e6ffe6;
        color: #2a5d3a;
        margin: 0 0.25rem;
        transition: all 0.3s ease;
    }
    .pagination .page-link:hover {
        background-color: #198754;
        color: #ffffff;
        border-color: #198754;
    }
    .pagination .page-item.active .page-link {
        background-color: #2a5d3a;
        border-color: #2a5d3a;
        color: #ffffff;
    }
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        border-color: #e6ffe6;
    }
    .troc-select {
        border: 2px solid #e6ffe6;
        background: #f9fffb;
        border-radius: 8px;
        padding: 0.5rem;
        font-size: 0.875rem;
        color: #3c6e4d;
        transition: all 0.3s ease;
    }
    .troc-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        outline: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Offres de Troc</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible text-white mx-4" role="alert">
                            <span class="text-sm">{{ session('success') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if ($offres->isNotEmpty())
                        <div class="px-4 pb-2">
                            <a href="{{ route('postdechets.troc') }}" class="btn bg-gradient-dark mb-0">Choisir un Post pour Ajouter une Offre</a>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Catégorie</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">État</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Localisation</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Statut</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($offres as $offre)
                                    @php
                                        $photoPaths = $offre->photos ? json_decode($offre->photos, true) : [];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $offre->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if (!empty($photoPaths) && isset($photoPaths[0]))
                                                <img src="{{ asset('storage/' . $photoPaths[0]) }}" alt="{{ $offre->description }}" class="troc-image">
                                            @else
                                                <div class="troc-image-placeholder">Pas d'image</div>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ \Illuminate\Support\Str::limit($offre->description, 50) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $offre->categorie }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ ucfirst($offre->etat) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $offre->localisation }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($offre->status === 'accepted')
                                                <span class="badge badge-sm bg-gradient-success">Accepté</span>
                                            @elseif ($offre->status === 'rejected')
                                                <span class="badge badge-sm bg-gradient-danger">Rejeté</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">En attente</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('offres-troc.show', $offre->post_dechet_id) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="Voir Offres">
                                                <i class="material-symbols-rounded">visibility</i>
                                            </a>
                                            <a href="{{ route('offres-troc.edit', $offre->id) }}" class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Modifier Offre">
                                                <i class="material-symbols-rounded">edit</i>
                                            </a>
                                            <form action="{{ route('offres-troc.destroy', $offre->id) }}" method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn">
                                                    <i class="material-symbols-rounded">delete</i>
                                                </button>
                                            </form>
                                            @if ($offre->status === 'en_attente')
                                                <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST" class="inline-flex items-center">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="troc-select mr-2">
                                                        <option value="accepted">Accepter</option>
                                                        <option value="rejected">Rejeter</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-link text-primary text-sm mb-0" data-toggle="tooltip" data-original-title="Mettre à jour statut">
                                                        <i class="material-symbols-rounded">check_circle</i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-sm text-gray-600 py-4">
                                            Aucune offre de troc pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($offres->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $offres->links('vendor.pagination.material-dashboard') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                let form = this.closest('form');

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: 'Cette offre sera supprimée définitivement !',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    </script>
@endsection
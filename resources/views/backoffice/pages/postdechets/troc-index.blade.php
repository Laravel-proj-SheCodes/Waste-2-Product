@extends('backoffice.layouts.layout')

@section('styles')
<style>
  .troc-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
  }
  tr.favorited-card {
    box-shadow: 0 8px 24px rgba(25, 135, 84, 0.3); /* Green shadow for favorited posts */
    border: 1px solid rgba(25, 135, 84, 0.5); /* Subtle green border */
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
                        <h6 class="text-white text-capitalize ps-3">Posts de Troc</h6>
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
                    <div class="px-4 pb-2">
                        <a href="{{ route('postdechets.create') }}" class="btn bg-gradient-dark mb-0">Ajouter un Post</a>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Image</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Titre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">État</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Statut</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posts as $post)
                                    @php
                                        $photoPaths = $post->photos ?? [];
                                        $isFavorited = auth()->check() && $post->favoritedBy->contains(auth()->id());
                                    @endphp
                                    <tr class="{{ $isFavorited ? 'favorited-card' : '' }}">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $post->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if (!empty($photoPaths))
                                                <img src="{{ asset('storage/' . $photoPaths[0]) }}" alt="{{ $post->titre }}" class="troc-image">
                                            @else
                                                <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
                                            @endif
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $post->titre }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ \Illuminate\Support\Str::limit($post->description, 50) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $post->quantite }} {{ $post->unite_mesure }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ ucfirst($post->etat) }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($post->statut === 'accepted')
                                                <span class="badge badge-sm bg-gradient-success">{{ $post->statut }}</span>
                                            @elseif ($post->statut === 'rejected')
                                                <span class="badge badge-sm bg-gradient-danger">{{ $post->statut }}</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">{{ $post->statut }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('postdechets.show', $post) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="Voir Post">
                                                <i class="material-symbols-rounded">visibility</i>
                                            </a>
                                            <a href="{{ route('postdechets.edit', $post) }}" class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Modifier Post">
                                                <i class="material-symbols-rounded">edit</i>
                                            </a>
                                            <form action="{{ route('postdechets.destroy', $post) }}" method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn">
                                                    <i class="material-symbols-rounded">delete</i>
                                                </button>
                                            </form>
                                            <a href="{{ route('offres-troc.create', $post) }}" class="btn btn-link text-primary text-sm mb-0" data-toggle="tooltip" data-original-title="Proposer une Offre">
                                                <i class="material-symbols-rounded">add_circle</i>
                                            </a>
                                            <a href="{{ route('postdechets.offres', $post) }}" class="btn btn-link text-sm mb-0" style="color: #9b59b6;" data-toggle="tooltip" data-original-title="Voir Offres">
                                                <i class="material-symbols-rounded">list</i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->links('vendor.pagination.material-dashboard') }}
                    </div>
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
                    text: 'Ce post sera supprimé définitivement !',
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
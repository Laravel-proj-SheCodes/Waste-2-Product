@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Liste des utilisateurs</h6>
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
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible text-white mx-4" role="alert">
                            <span class="text-sm">{{ session('error') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Rôle</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $user->name }}</p>
                                                    <p class="text-xs text-muted mb-0">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $user->email }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $user->isAdmin() ? 'Administrateur' : 'Client' }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm {{ $user->is_active ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="Voir les détails">
                                                <i class="material-symbols-rounded">visibility</i>
                                            </a>
                                            <form action="{{ route('users.toggleActive', $user) }}" method="POST" class="toggle-active-form d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-link text-sm mb-0 {{ $user->is_active ? 'text-danger' : 'text-success' }} toggle-active-btn"
                                                        data-toggle="tooltip" data-original-title="{{ $user->is_active ? 'Désactiver' : 'Activer' }} le compte">
                                                    <i class="material-symbols-rounded">{{ $user->is_active ? 'block' : 'check_circle' }}</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links('vendor.pagination.material-dashboard') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .table th:nth-child(1) {
        width: 30%; /* Nom */
    }
    .table th:nth-child(2) {
        width: 30%; /* Email */
    }
    .table th:nth-child(3) {
        width: 15%; /* Rôle */
    }
    .table th:nth-child(4) {
        width: 10%; /* Statut */
    }
    .table th:nth-child(5) {
        width: 15%; /* Actions */
    }
    .table td:nth-child(1) p.text-xs {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-active-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            let form = this.closest('form');
            let isActive = this.classList.contains('text-danger'); // text-danger means currently active (will deactivate)

            Swal.fire({
                title: 'Confirmation',
                text: isActive ? 'Voulez-vous désactiver ce compte ?' : 'Voulez-vous activer ce compte ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#d33' : '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: isActive ? 'Désactiver' : 'Activer',
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
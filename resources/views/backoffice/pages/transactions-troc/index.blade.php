@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Transactions Troc</h6>
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
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Offre Troc</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Post Déchet</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Proposant</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Acceptant</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date d'accord</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut Livraison</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $transaction->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->offreTroc->description }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->offreTroc->postDechet->titre }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->offreTroc->user->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->utilisateurAcceptant->name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $transaction->date_accord->translatedFormat('d/m/Y H:i') }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($transaction->statut_livraison === 'livre')
                                                <span class="badge badge-sm bg-gradient-success">{{ $transaction->statut_livraison }}</span>
                                            @elseif ($transaction->statut_livraison === 'annule')
                                                <span class="badge badge-sm bg-gradient-danger">{{ $transaction->statut_livraison }}</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">{{ $transaction->statut_livraison }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('transactions-troc.show', $transaction->id) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="Voir Transaction">
                                                <i class="material-symbols-rounded">visibility</i>
                                            </a>
                                          <!--  <a href="{{ route('transactions-troc.edit', $transaction->id) }}" class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Modifier Transaction">
                                                <i class="material-symbols-rounded">edit</i>
                                            </a>
                                            <form action="{{ route('transactions-troc.destroy', $transaction->id) }}" method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn">
                                                    <i class="material-symbols-rounded">delete</i>
                                                </button>-->
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->links('vendor.pagination.material-dashboard') }}
                    </div>
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
                text: "Cette transaction sera supprimée définitivement !",
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
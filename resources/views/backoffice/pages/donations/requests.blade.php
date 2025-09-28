@extends('backoffice.layouts.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Requests for Donation: {{ $donation->product_name }}</h6>
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
                        @if ($requests->isEmpty())
                            <p class="text-center text-muted mx-4">No requests for this donation yet.</p>
                        @else
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Requester</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requests as $request)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $request->id }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-sm font-weight-bold mb-0">{{ $request->user->name ?? 'Anonymous' }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-sm text-muted mb-0">{{ $request->user->email ?? 'N/A' }}</p>
                                                </td>
                                                <td>
                                                    @if ($request->status === 'accepted')
                                                        <span class="badge badge-sm bg-gradient-success">{{ $request->status }}</span>
                                                    @elseif ($request->status === 'rejected')
                                                        <span class="badge badge-sm bg-gradient-danger">{{ $request->status }}</span>
                                                    @else
                                                        <span class="badge badge-sm bg-gradient-warning">{{ $request->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($request->status === 'pending')
                                                        <form action="{{ route('donation-requests.accept', $request) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-link text-success text-sm mb-0">
                                                                <i class="material-symbols-rounded">check_circle</i> Accept
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('donation-requests.reject', $request) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-link text-danger text-sm mb-0">
                                                                <i class="material-symbols-rounded">cancel</i> Reject
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        <div class="px-4 pb-2 mt-4">
                            <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">Back to Donations</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Ensure table columns are balanced */
        .table th, .table td {
            vertical-align: middle;
        }
        .table th {
            width: 10%; /* ID */
        }
        .table th:nth-child(2) {
            width: 25%; /* Requester */
        }
        .table th:nth-child(3) {
            width: 30%; /* Email */
        }
        .table th:nth-child(4) {
            width: 15%; /* Status */
        }
        .table th:nth-child(5) {
            width: 20%; /* Actions */
        }
    </style>
@endsection
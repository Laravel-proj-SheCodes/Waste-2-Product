@extends('backoffice.layouts.layout')

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4 mt-4 d-flex flex-row flex-nowrap overflow-auto">
        <div class="col-auto me-2" style="min-width: 250px;">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Total Donations</p>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">inventory_2</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">All time</span> donations</p>
                </div>
            </div>
        </div>
        <div class="col-auto me-2" style="min-width: 250px;">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Pending</p>
                            <h4 class="mb-0">{{ $stats['pending'] ?? 0 }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-warning shadow-warning text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">schedule</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-warning font-weight-bolder">Awaiting</span> validation</p>
                </div>
            </div>
        </div>
        <div class="col-auto me-2" style="min-width: 250px;">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Accepted</p>
                            <h4 class="mb-0">{{ $stats['accepted'] ?? 0 }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-success shadow-success text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">check_circle</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">Validated</span> donations</p>
                </div>
            </div>
        </div>
        <div class="col-auto me-2" style="min-width: 250px;">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Rejected</p>
                            <h4 class="mb-0">{{ $stats['rejected'] ?? 0 }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-danger shadow-danger text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">cancel</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-danger font-weight-bolder">Declined</span> donations</p>
                </div>
            </div>
        </div>
        <div class="col-auto" style="min-width: 250px;">
            <div class="card">
                <div class="card-header p-2 ps-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Taken</p>
                            <h4 class="mb-0">{{ $stats['taken'] ?? 0 }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-info shadow-info text-center border-radius-md">
                            <i class="material-symbols-rounded opacity-10">shopping_bag</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-2 ps-3">
                    <p class="mb-0 text-sm"><span class="text-info font-weight-bolder">Collected</span> donations</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Donations Table</h6>
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
                        <div class="px-4 pb-2">
                            <form action="{{ route('donations.index') }}" method="GET" class="row g-3 align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="material-symbols-rounded">search</i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Search by product name" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
    <select name="type" class="form-select">
        <option value="all" {{ request('type') === 'all' || !request('type') ? 'selected' : '' }}>All Types</option>
        <option value="recyclable" {{ request('type') === 'recyclable' ? 'selected' : '' }}>♻️ Recyclable donations</option>
        <option value="renewable" {{ request('type') === 'renewable' ? 'selected' : '' }}>🌿 Renewable donations</option>
    </select>
</div>

                                <div class="col-md-5">
                                    <button type="submit" class="btn bg-gradient-dark mb-0 me-2">Filter</button>
                                    <a href="{{ route('donations.index') }}" class="btn bg-gradient-secondary mb-0">Clear Filters</a>
                                </div>
                            </form>
                            <a href="{{ route('donations.create') }}" class="btn bg-gradient-dark mb-0 mt-3">Add Donation</a>
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Donor</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Location</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($donations as $donation)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <p class="text-sm font-weight-bold mb-0">{{ $donation->user->name ?? 'Anonymous' }}</p>
                                                        <p class="text-xs text-muted mb-0">{{ $donation->user->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $donation->product_name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $donation->quantity }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ ucfirst($donation->type) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $donation->location }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $donation->donation_date }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($donation->status === 'accepted')
                                                    <span class="badge badge-sm bg-gradient-success">{{ ucfirst($donation->status) }}</span>
                                                @elseif ($donation->status === 'rejected')
                                                    <span class="badge badge-sm bg-gradient-danger">{{ ucfirst($donation->status) }}</span>
                                                @elseif ($donation->status === 'taken')
                                                    <span class="badge badge-sm bg-gradient-info">{{ ucfirst($donation->status) }}</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-warning">{{ ucfirst($donation->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('donations.show', $donation) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="View Donation">
                                                    <i class="material-symbols-rounded">visibility</i>
                                                </a>
                                                <a href="{{ route('donations.edit', $donation) }}" class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Edit Donation">
                                                    <i class="material-symbols-rounded">edit</i>
                                                </a>
                                                <a href="{{ route('donations.showRequests', $donation) }}" class="btn btn-link text-primary text-sm mb-0" data-toggle="tooltip" data-original-title="View Requests">
                                                    <i class="material-symbols-rounded">group</i>
                                                </a>
                                                <form action="{{ route('donations.destroy', $donation) }}" method="POST" class="delete-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn">
                                                        <i class="material-symbols-rounded">delete</i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-sm py-3">No donations found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $donations->appends(['search' => request('search'), 'type' => request('type')])->links('vendor.pagination.material-dashboard') }}
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
            width: 25%; /* Donor */
        }
        .table th:nth-child(2) {
            width: 20%; /* Product */
        }
        .table th:nth-child(3) {
            width: 10%; /* Quantity */
        }
        .table th:nth-child(4) {
            width: 10%; /* Type */
        }
        .table th:nth-child(5) {
            width: 15%; /* Location */
        }
        .table th:nth-child(6) {
            width: 10%; /* Date */
        }
        .table th:nth-child(7) {
            width: 10%; /* Status */
        }
        .table th:nth-child(8) {
            width: 20%; /* Actions */
        }
        .table td:nth-child(1) p.text-xs {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .input-group-text {
            background-color: #f8f9fa;
        }
        .form-select, .form-control {
            border-radius: 0.375rem;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                let form = this.closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This donation will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Auto-submit form on type change
        document.querySelector('select[name="type"]').addEventListener('change', function () {
            this.closest('form').submit();
        });
    });
    </script>
@endsection
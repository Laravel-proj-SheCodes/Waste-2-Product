@extends('backoffice.layouts.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-lg border-0">
                    <!-- Enhanced header with solid color -->
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-dark shadow-dark border-radius-lg pt-5 pb-4">
                            <div class="text-center">
                                <h4 class="text-white font-weight-bold mb-1">Donation Details</h4>
                                <p class="text-white-50 mb-0 text-sm">View the details of the donation below</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-4 py-5">
                        <!-- Donation details with improved styling -->
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-id-badge text-primary me-2"></i>
                                    <span class="text-dark font-weight-bold">ID:</span>
                                    <span class="text-dark ms-2">{{ $donation->id }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-box text-success me-2"></i>
                                    <span class="text-dark font-weight-bold">Product:</span>
                                    <span class="text-dark ms-2">{{ $donation->product_name }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-sort-numeric-up text-info me-2"></i>
                                    <span class="text-dark font-weight-bold">Quantity:</span>
                                    <span class="text-dark ms-2">{{ $donation->quantity }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-tags text-warning me-2"></i>
                                    <span class="text-dark font-weight-bold">Type:</span>
                                    <span class="text-dark ms-2">{{ ucfirst($donation->type) }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    <span class="text-dark font-weight-bold">Location:</span>
                                    <span class="text-dark ms-2">{{ $donation->location }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-calendar-alt text-danger me-2"></i>
                                    <span class="text-dark font-weight-bold">Date:</span>
                                    <span class="text-dark ms-2">{{ $donation->donation_date }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-start mb-3">
                                    <i class="fas fa-align-left text-secondary me-2"></i>
                                    <span class="text-dark font-weight-bold">Description:</span>
                                    <span class="text-dark ms-2">{{ $donation->description ?? 'Aucune' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check-circle text-info me-2"></i>
                                    <span class="text-dark font-weight-bold">Status:</span>
                                    <span class="ms-2">
                                        @if ($donation->status === 'accepted')
                                            <span class="badge badge-sm bg-success">{{ $donation->status }}</span>
                                        @elseif ($donation->status === 'rejected')
                                            <span class="badge badge-sm bg-danger">{{ $donation->status }}</span>
                                        @else
                                            <span class="badge badge-sm bg-warning">{{ $donation->status }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <span class="text-dark font-weight-bold">User:</span>
                                    <span class="text-dark ms-2">{{ $donation->user->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons with consistent styling -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{ route('donations.backedit', $donation) }}" 
                                       class="btn btn-outline-warning btn-lg px-4 me-2" 
                                       data-toggle="tooltip" 
                                       data-original-title="Edit Donation">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                    <form action="{{ route('donations.destroy', $donation) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger btn-lg px-4" 
                                                data-toggle="tooltip" 
                                                data-original-title="Delete Donation" 
                                                onclick="return confirm('Voulez-vous vraiment supprimer cette donation ?')">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom styles matching create/edit pages without linear-gradient -->
    <style>
        .card {
            border-radius: 15px;
            overflow: hidden;
        }

        .badge {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5em 1em;
            transition: all 0.3s ease;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.025em;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .text-dark {
            color: #343a40 !important;
        }

        .font-weight-bold {
            font-weight: 600 !important;
        }

        .bg-dark {
            background-color: #343a40 !important;
        }

        .bg-success {
            background-color: #2dce89 !important;
        }

        .bg-danger {
            background-color: #f5365c !important;
        }

        .bg-warning {
            background-color: #fb6340 !important;
        }
    </style>
@endsection
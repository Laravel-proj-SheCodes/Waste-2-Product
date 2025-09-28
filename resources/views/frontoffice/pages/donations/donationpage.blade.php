@extends('frontoffice.layouts.layoutfront')

@section('content')
    <!-- Hero section (big donation box) -->
    <section class="py-5 border-bottom bg-light">
        <div class="container px-5 my-5 text-center">
            <img src="{{ Vite::asset('resources/assets-frontoffice/img/donimg.jpg') }}" alt="Donation Image" class="img-fluid mb-3" style="max-width: 500px; height: auto;">
            <h2 class="fw-bolder">Make a Difference with Your Donation</h2>
            <p class="lead mb-4">In today's world, recycling and donating waste products is more important than ever. By donating recyclable or renewable items, you help reduce landfill waste, conserve natural resources, lower pollution, and support sustainable communities. Every donation counts towards a greener planet!</p>
            <a href="{{ route('donate.create') }}" class="btn btn-success btn-lg">Donate Now</a>
        </div>
    </section>

    <!-- Simplified Accepted Donations Section with Search -->
    <section class="py-5 bg-light">
        <div class="container px-5 my-5">
            <div class="text-center mb-5">
                <h2 class="fw-bolder mb-3" style="color: #126e27ff;">Available Donations</h2>
                <p class="text-muted">Discover meaningful donations waiting for a new home</p>
            </div>

            <!-- Search Form -->
            <div class="mb-4">
                <form action="{{ route('donate.donationpage') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="search" class="form-label text-muted small">Search by Product Name</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Enter product name...">
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label text-muted small">Filter by Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="" {{ request('type') == '' ? 'selected' : '' }}>All Types</option>
                            <option value="recyclable" {{ request('type') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                            <option value="renewable" {{ request('type') == 'renewable' ? 'selected' : '' }}>Renewable</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100 py-2">Search</button>
                    </div>
                </form>
            </div>

            @if ($acceptedDonations->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted fs-4">No accepted donations available at the moment.</p>
                    <p class="text-muted">Check back soon for new opportunities to make a difference!</p>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    @foreach ($acceptedDonations as $donation)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 rounded-3" style="transition: all 0.3s ease;">
                                <!-- Simple card header with green background -->
                                <div class="card-header text-white p-3" style="background-color: #126e27ff;">
                                    <h5 class="card-title mb-1 fw-bold">{{ $donation->product_name }}</h5>
                                    <span class="badge bg-white text-success px-2 py-1 rounded-pill">{{ ucfirst($donation->type) }}</span>
                                </div>

                                <!-- Compact card body with reduced padding -->
                                <div class="card-body p-3">
                                    <div class="row g-2 text-sm">
                                        <div class="col-6">
                                            <small class="text-muted">Quantity:</small>
                                            <div class="fw-semibold">{{ $donation->quantity }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Location:</small>
                                            <div class="fw-semibold">{{ $donation->location }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Date:</small>
                                            <div class="fw-semibold">{{ $donation->donation_date }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Donor:</small>
                                            <div class="fw-semibold">{{ $donation->user->name ?? 'Anonymous' }}</div>
                                        </div>
                                        @if ($donation->description)
                                            <div class="col-12 mt-2">
                                                <small class="text-muted">Description:</small>
                                                <div class="text-dark">{{ $donation->description }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Simple card footer with green button -->
                                <div class="card-footer bg-transparent border-0 p-3">
                                    <form action="{{ route('donations.request', $donation) }}" method="POST" id="requestDonationForm_{{ $donation->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-success w-100 py-2 request-donation-btn" data-bs-toggle="modal" data-bs-target="#confirmRequestModal_{{ $donation->id }}">
                                            Request Donation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="confirmRequestModal_{{ $donation->id }}" tabindex="-1" aria-labelledby="confirmRequestModalLabel_{{ $donation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 shadow-lg border-0">
                                    <div class="modal-header border-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <h4 class="modal-title text-success fw-bold mb-3" id="confirmRequestModalLabel_{{ $donation->id }}">Confirm Your Request</h4>
                                        <p class="text-muted mb-4">Are you sure you want to request <strong class="text-success">{{ $donation->product_name }}</strong>? Your request will be sent to the donor for approval.</p>
                                        <div class="d-flex justify-content-center gap-3">
                                            <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success px-4 py-2 confirm-request-btn" form="requestDonationForm_{{ $donation->id }}">Yes, Request It!</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Descriptive sections (bla bla bla) -->
    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="row gx-5">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                        <i class="bi bi-tree"></i>
                    </div>
                    <h2 class="h4 fw-bolder">Environmental Impact</h2>
                    <p>Recycling one ton of plastic saves enough energy to run a refrigerator for a month. Your donations help combat climate change.</p>
                </div>
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                        <i class="bi bi-globe"></i>
                    </div>
                    <h2 class="h4 fw-bolder">Community Benefits</h2>
                    <p>Donated materials can be transformed into new products, creating jobs and supporting local economies in places like Tunisia.</p>
                </div>
                <div class="col-lg-4">
                    <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                        <i class="bi bi-recycle"></i>
                    </div>
                    <h2 class="h4 fw-bolder">Why Now?</h2>
                    <p>With waste levels rising globally, immediate action through donations ensures a sustainable future for generations to come.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

<style>
    /* Simplified styling with no gradients, just green and black colors */
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 1rem;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    /* Search Form Styling */
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }

    .form-control:focus, .form-select:focus {
        border-color: #126e27ff;
        box-shadow: 0 0 0 0.2rem rgba(18, 110, 39, 0.25);
    }

    @media (max-width: 768px) {
        .row-cols-md-2 {
            column-count: 1;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.request-donation-btn').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                const formId = form.id;
                const donationId = formId.replace('requestDonationForm_', '');
                const confirmButton = document.querySelector(`#confirmRequestModal_${donationId} .confirm-request-btn`);
                if (confirmButton) {
                    // Remove any existing event listeners to prevent duplicates
                    confirmButton.removeEventListener('click', submitForm);
                    // Add new event listener to submit the form
                    confirmButton.addEventListener('click', submitForm);
                    function submitForm() {
                        document.getElementById(formId).submit();
                    }
                } else {
                    console.error('Confirm button not found for form:', formId);
                }
            });
        });
    });
</script>
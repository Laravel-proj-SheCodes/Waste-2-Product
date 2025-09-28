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

    <!-- Accepted Donations Section -->
    <section class="py-5 bg-light">
        <div class="container px-5 my-5">
            <h2 class="fw-bolder text-center mb-5" style="color: #28a745;">Available Donations</h2>
            @if ($acceptedDonations->isEmpty())
                <p class="text-center text-muted">No accepted donations available at the moment.</p>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($acceptedDonations as $donation)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden" style="transition: all 0.3s ease;">
                                <div class="card-body p-4 bg-white">
                                    <h5 class="card-title text-success fw-bold">{{ $donation->product_name }}</h5>
                                    <ul class="list-unstyled text-muted mb-0">
                                        <li><strong>Quantity:</strong> {{ $donation->quantity }}</li>
                                        <li><strong>Type:</strong> {{ ucfirst($donation->type) }}</li>
                                        <li><strong>Location:</strong> {{ $donation->location }}</li>
                                        <li><strong>Date:</strong> {{ $donation->donation_date }}</li>
                                        <li><strong>Donated by:</strong> {{ $donation->user->name ?? 'Anonymous' }}</li>
                                        @if ($donation->description)
                                            <li><strong>Description:</strong> {{ $donation->description }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-center p-3">
                                    <form action="{{ route('donations.request', $donation) }}" method="POST" id="requestDonationForm_{{ $donation->id }}">
                                        @csrf
                                        <button type="button" class="btn btn-success w-100 rounded-3 request-donation-btn" data-bs-toggle="modal" data-bs-target="#confirmRequestModal_{{ $donation->id }}">Request Donation</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="confirmRequestModal_{{ $donation->id }}" tabindex="-1" aria-labelledby="confirmRequestModalLabel_{{ $donation->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-light rounded-4 shadow-lg">
                                    <div class="modal-header border-0">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center p-5">
                                        <div class="heart-animation mb-4">
                                            <div class="heart"></div>
                                        </div>
                                        <h4 class="modal-title text-success fw-bold" id="confirmRequestModalLabel_{{ $donation->id }}">Confirm Your Request</h4>
                                        <p class="text-muted mb-4">Are you sure you want to request <strong>{{ $donation->product_name }}</strong>? Your request will be sent to the donor for approval.</p>
                                        <div class="d-flex justify-content-center gap-3">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success confirm-request-btn" form="requestDonationForm_{{ $donation->id }}">Yes, Request It!</button>
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
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .card-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .list-unstyled li {
        padding: 0.25rem 0;
        border-bottom: 1px solid #eee;
    }

    .list-unstyled li:last-child {
        border-bottom: none;
    }

    /* Heart Animation for Modal */
    .heart-animation {
        position: relative;
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }

    .heart {
        position: absolute;
        width: 40px;
        height: 40px;
        background-color: #28a745;
        transform: rotate(-45deg);
        animation: heartbeat 1.5s infinite;
        top: 20px;
        left: 20px;
    }

    .heart:before,
    .heart:after {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        background-color: #28a745;
        border-radius: 50%;
    }

    .heart:before {
        top: -20px;
        left: 0;
    }

    .heart:after {
        left: 20px;
        top: 0;
    }

    @keyframes heartbeat {
        0% { transform: scale(1) rotate(-45deg); }
        50% { transform: scale(1.2) rotate(-45deg); }
        100% { transform: scale(1) rotate(-45deg); }
    }

    /* Modal Styling */
    .modal-content {
        border: none;
        border-radius: 1rem;
    }

    .modal-title {
        font-size: 1.5rem;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
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
@extends('frontoffice.layouts.layoutfront')

@section('content')
    <section class="py-5">
        <div class="container px-5 my-5">
            <!-- Enhanced title styling with better spacing -->
            <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">My Donation Requests</h2>
            @if ($requests->isEmpty())
                <!-- Improved empty state styling -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <p class="text-muted fs-5">You haven't made any donation requests yet.</p>
                    <a href="{{ route('donate.donationpage') }}" class="btn btn-success mt-3">Browse Donations</a>
                </div>
            @else
                <!-- Reduced card height and improved compact layout -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($requests as $request)
                        <div class="col">
                            <div class="card border-0 shadow-sm" style="transition: transform 0.2s ease; border-radius: 12px;">
                                <!-- Added green header bar for visual appeal -->
                                <div class="card-header bg-success text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                                    <h6 class="mb-0 fw-bold">{{ $request->donation->product_name }}</h6>
                                </div>
                                <div class="card-body p-3">
                                    <!-- Compact grid layout for better space usage -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Quantity</small>
                                            <div class="fw-semibold">{{ $request->donation->quantity }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Type</small>
                                            <div class="fw-semibold">{{ ucfirst($request->donation->type) }}</div>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted">Location</small>
                                            <div class="fw-semibold">{{ $request->donation->location }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Date</small>
                                            <div class="fw-semibold">{{ $request->donation->donation_date }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Donor</small>
                                            <div class="fw-semibold">{{ $request->donation->user->name ?? 'Anonymous' }}</div>
                                        </div>
                                    </div>
                                    
                                    @if ($request->donation->description)
                                        <div class="mb-3">
                                            <small class="text-muted">Description</small>
                                            <p class="mb-0 small">{{ Str::limit($request->donation->description, 80) }}</p>
                                        </div>
                                    @endif
                                    
                                    <!-- Improved status badge positioning and styling -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Status</small>
                                        @if ($request->status === 'accepted')
                                            <span class="badge bg-success px-3 py-2">Accepted</span>
                                        @elseif ($request->status === 'rejected')
                                            <span class="badge bg-danger px-3 py-2">Rejected</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Enhanced back button styling -->
            <div class="text-center mt-5">
                <a href="{{ route('donate.donationpage') }}" class="btn btn-outline-success btn-lg px-4">
                    <i class="fas fa-arrow-left me-2"></i>Back to Donations
                </a>
            </div>
        </div>
    </section>

    <!-- Added hover effects with CSS -->
    <style>
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }
        
        .badge {
            font-size: 0.75rem;
            border-radius: 20px;
        }
    </style>
@endsection

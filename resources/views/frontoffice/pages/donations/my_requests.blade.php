@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">My Donation Requests</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Search Form -->
        <div class="mb-4">
            <form action="{{ route('donate.myRequests') }}" method="GET" class="row g-3 align-items-end">
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

        @if ($requests->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                </div>
                <p class="text-muted fs-5">You haven't made any donation requests yet.</p>
                <a href="{{ route('donate.donationpage') }}" class="btn btn-success mt-3">Browse Donations</a>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($requests as $request)
                    <div class="col">
                        <div class="card border-0 shadow-sm" style="transition: transform 0.2s ease; border-radius: 12px;">
                            <div class="card-header bg-success text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0 fw-bold">{{ $request->donation->product_name }}</h6>
                            </div>
                            <div class="card-body p-3">
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

        <div class="text-center mt-5">
            <a href="{{ route('donate.donationpage') }}" class="btn btn-outline-success btn-lg px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Donations
            </a>
        </div>
    </div>
</section>

<style>
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}
.badge {
    font-size: 0.75rem;
    border-radius: 20px;
}
.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
}
.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
@media (max-width: 768px) {
    .row-cols-md-2 {
        column-count: 1;
    }
}
</style>
@endsection
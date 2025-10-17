@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">My Donations</h2>

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
            <form action="{{ route('mes-donations') }}" method="GET" class="row g-3 align-items-end">
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

        @if ($donations->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-gift" style="font-size: 3rem; color: #6c757d;"></i>
                </div>
                <p class="text-muted fs-5">You haven't made any donations yet.</p>
                <a href="{{ route('donate.donationpage') }}" class="btn btn-success mt-3">Make a Donation</a>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($donations as $donation)
                    <div class="col">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; transition: transform 0.2s;">
                            <div class="card-header bg-success text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0 fw-bold">{{ $donation->product_name }}</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Quantity</small>
                                        <div class="fw-semibold">{{ $donation->quantity }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Type</small>
                                        <div class="fw-semibold">{{ ucfirst($donation->type) }}</div>
                                    </div>
                                    <div class="col-12">
                                        <small class="text-muted">Location</small>
                                        <div class="fw-semibold">{{ $donation->location }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Date</small>
                                        <div class="fw-semibold">{{ $donation->donation_date }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Status</small>
                                        <div class="fw-semibold">
                                            @if ($donation->status === 'accepted')
                                                <span class="badge bg-success">Accepted</span>
                                            @elseif ($donation->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif ($donation->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif ($donation->status === 'taken')
                                                <span class="badge bg-primary">Taken</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($donation->description)
                                    <div class="mb-3">
                                        <small class="text-muted">Description</small>
                                        <p class="mb-0 small">{{ Str::limit($donation->description, 80) }}</p>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('donate.show', $donation->id) }}" class="btn btn-sm btn-outline-success">View</a>
                                    @if ($donation->status !== 'taken')
                                        <a href="{{ route('donate.edit', $donation->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form action="{{ route('donations.destroy', $donation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this donation?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                        @if ($donation->status === 'accepted')
                                            <a href="{{ route('donate.showRequests', $donation->id) }}" class="btn btn-sm btn-outline-info">View Requests</a>
                                        @endif
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
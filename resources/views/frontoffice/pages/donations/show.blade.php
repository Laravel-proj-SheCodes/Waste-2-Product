@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">Donation Details</h2>

        <div class="card shadow-sm p-4" style="max-width: 600px; margin: auto; border-radius: 12px;">
            <div class="card-header bg-success text-white py-2 px-3" style="border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-bold">{{ $donation->product_name }}</h6>
            </div>
            <div class="card-body">
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
                    @if ($donation->description)
                        <div class="col-12 mt-3">
                            <small class="text-muted">Description</small>
                            <p class="mb-0">{{ $donation->description }}</p>
                        </div>
                    @endif
                    @if ($donation->taken_by_user_id)
                        <div class="col-12 mt-3">
                            <small class="text-muted">Taken By</small>
                            <div class="fw-semibold">{{ $donation->takenBy->name ?? 'Unknown' }}</div>
                        </div>
                    @endif
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('mes-donations') }}" class="btn btn-outline-secondary">Back to My Donations</a>
                    @if ($donation->status !== 'taken')
                        <a href="{{ route('donate.edit', $donation->id) }}" class="btn btn-outline-primary">Edit</a>
                        <form action="{{ route('donations.destroy', $donation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this donation?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                        </form>
                        @if ($donation->status === 'accepted')
                            <a href="{{ route('donate.showRequests', $donation->id) }}" class="btn btn-outline-info">View Requests</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
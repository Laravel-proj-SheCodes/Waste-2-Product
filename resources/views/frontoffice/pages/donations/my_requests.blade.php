@extends('frontoffice.layouts.layoutfront')

@section('content')
    <section class="py-5">
        <div class="container px-5 my-5">
            <h2 class="fw-bolder text-center mb-5" style="color: #28a745;">My Donation Requests</h2>
            @if ($requests->isEmpty())
                <p class="text-center text-muted">You haven't made any donation requests yet.</p>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach ($requests as $request)
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden" style="transition: all 0.3s ease;">
                                <div class="card-body p-4 bg-white">
                                    <h5 class="card-title text-success fw-bold">{{ $request->donation->product_name }}</h5>
                                    <ul class="list-unstyled text-muted mb-0">
                                        <li><strong>Quantity:</strong> {{ $request->donation->quantity }}</li>
                                        <li><strong>Type:</strong> {{ ucfirst($request->donation->type) }}</li>
                                        <li><strong>Location:</strong> {{ $request->donation->location }}</li>
                                        <li><strong>Date:</strong> {{ $request->donation->donation_date }}</li>
                                        <li><strong>Donated by:</strong> {{ $request->donation->user->name ?? 'Anonymous' }}</li>
                                        @if ($request->donation->description)
                                            <li><strong>Description:</strong> {{ $request->donation->description }}</li>
                                        @endif
                                        <li><strong>Status:</strong>
                                            @if ($request->status === 'accepted')
                                                <span class="badge bg-success">{{ $request->status }}</span>
                                            @elseif ($request->status === 'rejected')
                                                <span class="badge bg-danger">{{ $request->status }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ $request->status }}</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="text-center mt-5">
                <a href="{{ route('donate.donationpage') }}" class="btn btn-outline-success">Back to Donations</a>
            </div>
        </div>
    </section>
@endsection
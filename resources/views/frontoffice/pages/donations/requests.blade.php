@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">Requests for {{ $donation->product_name }}</h2>

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

        @if ($requests->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-list" style="font-size: 3rem; color: #6c757d;"></i>
                </div>
                <p class="text-muted fs-5">No requests for this donation yet.</p>
                <a href="{{ route('mes-donations') }}" class="btn btn-outline-success mt-3">Back to My Donations</a>
            </div>
        @else
            <div class="card shadow-sm p-4" style="border-radius: 12px;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <td>{{ $request->user->name ?? 'Unknown' }}</td>
                                    <td>
                                        @if ($request->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($request->status === 'accepted')
                                            <span class="badge bg-success">Accepted</span>
                                        @elseif ($request->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if ($request->status === 'pending')
                                            <form action="{{ route('donation-requests.accept', $request->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Accept</button>
                                            </form>
                                            <form action="{{ route('donation-requests.reject', $request->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <a href="{{ route('mes-donations') }}" class="btn btn-outline-secondary">Back to My Donations</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
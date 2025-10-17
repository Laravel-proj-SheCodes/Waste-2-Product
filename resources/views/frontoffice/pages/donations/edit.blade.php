@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <h2 class="fw-bolder text-center mb-5" style="color: #28a745; font-size: 2.2rem;">Edit Donation</h2>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm p-4" style="max-width: 600px; margin: auto; border-radius: 12px;">
            <form action="{{ route('donations.update', $donation->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name', $donation->product_name) }}" required>
                    @error('product_name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $donation->quantity) }}" required min="1">
                    @error('quantity')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="type" class="form-label">Type</label>
                    <select class="form-control" id="type" name="type" required>
                        <option value="recyclable" {{ $donation->type === 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                        <option value="renewable" {{ $donation->type === 'renewable' ? 'selected' : '' }}>Renewable</option>
                    </select>
                    @error('type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $donation->location) }}" required>
                    @error('location')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="donation_date" class="form-label">Donation Date</label>
                    <input type="date" class="form-control" id="donation_date" name="donation_date" value="{{ old('donation_date', $donation->donation_date) }}" required>
                    @error('donation_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $donation->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('mes-donations') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update Donation</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
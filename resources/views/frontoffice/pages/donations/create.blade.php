@extends('frontoffice.layouts.layoutfront')

@section('content')
    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-success text-white text-center py-4">
                            <h4 class="mb-0">Submit Your Donation</h4>
                            <p class="mb-0 text-white-50">Fill in the details to donate your items</p>
                        </div>
                        <div class="card-body p-5">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('donations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="from_front" value="1"> <!-- Detect frontoffice submission -->

                                <div class="row gx-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter location" value="{{ old('location') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="product_name" class="form-label">Product Name</label>
                                        <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter product name" value="{{ old('product_name') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Enter quantity" value="{{ old('quantity') }}" min="1" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select id="type" name="type" class="form-select" required>
                                            <option value="" disabled selected>Choose type...</option>
                                            <option value="recyclable" {{ old('type') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                                            <option value="renewable" {{ old('type') == 'renewable' ? 'selected' : '' }}>Renewable</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description (Optional)</label>
                                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Additional details...">{{ old('description') }}</textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="donation_date" class="form-label">Donation Date</label>
                                        <input type="date" id="donation_date" name="donation_date" class="form-control" value="{{ old('donation_date', date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('donate.donationpage') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-success">Submit Donation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
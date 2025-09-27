@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 my-4">
                <!-- Enhanced header with better styling and icon -->
                <div class="card-header p-0 position-relative mx-3 z-index-2 border-0" style="min-height: 70px;">
                    <div class="bg-gradient-dark shadow-lg border-radius-lg pt-4 pb-3">
                        <div class="d-flex align-items-center ps-4">
                            <div class="icon icon-lg icon-shape bg-white shadow text-center border-radius-md me-3">
                                <i class="fas fa-edit text-dark opacity-10"></i>
                            </div>
                            <div>
                                <h5 class="text-white mb-0 font-weight-bolder">Edit Donation</h5>
                                <p class="text-white text-sm mb-0 opacity-8">Update donation information</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-5 pt-4 pb-4">
                    <!-- Enhanced error display with better styling -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please correct the following errors:</strong>
                            </div>
                            <ul class="list-unstyled mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm"><i class="fas fa-circle text-xs me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('donations.update', $donation) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Two-column responsive grid layout -->
                        <div class="row g-4">
                            <!-- Location -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Location
                                    </label>
                                    <input type="text" 
                                           id="location" 
                                           name="location" 
                                           class="form-control form-control-lg border-2 @error('location') is-invalid @enderror"
                                           value="{{ old('location', $donation->location) }}" 
                                           placeholder="Enter donation location"
                                           required>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product_name" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-box text-info me-2"></i>Product Name
                                    </label>
                                    <input type="text" 
                                           id="product_name" 
                                           name="product_name" 
                                           class="form-control form-control-lg border-2 @error('product_name') is-invalid @enderror"
                                           value="{{ old('product_name', $donation->product_name) }}" 
                                           placeholder="Enter product name"
                                           required>
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-sort-numeric-up text-success me-2"></i>Quantity
                                    </label>
                                    <input type="number" 
                                           id="quantity" 
                                           name="quantity" 
                                           class="form-control form-control-lg border-2 @error('quantity') is-invalid @enderror"
                                           value="{{ old('quantity', $donation->quantity) }}" 
                                           placeholder="Enter quantity"
                                           min="1" 
                                           required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-tags text-warning me-2"></i>Type
                                    </label>
                                    <select id="type" 
                                            name="type" 
                                            class="form-select form-control-lg border-2 @error('type') is-invalid @enderror" 
                                            required>
                                        <option value="">Choose type...</option>
                                        <option value="recyclable" {{ old('type', $donation->type) == 'recyclable' ? 'selected' : '' }}>
                                            Recyclable
                                        </option>
                                        <option value="renewable" {{ old('type', $donation->type) == 'renewable' ? 'selected' : '' }}>
                                            Renouvelable
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Donation Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="donation_date" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>Donation Date
                                    </label>
                                    <input type="date" 
                                           id="donation_date" 
                                           name="donation_date" 
                                           class="form-control form-control-lg border-2 @error('donation_date') is-invalid @enderror"
                                           value="{{ old('donation_date', $donation->donation_date) }}" 
                                           required>
                                    @error('donation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-flag text-danger me-2"></i>Status
                                    </label>
                                    <select id="status" 
                                            name="status" 
                                            class="form-select form-control-lg border-2 @error('status') is-invalid @enderror">
                                        <option value="pending" {{ old('status', $donation->status) == 'pending' ? 'selected' : '' }}>
                                            <i class="fas fa-clock"></i> En attente
                                        </option>
                                        <option value="accepted" {{ old('status', $donation->status) == 'accepted' ? 'selected' : '' }}>
                                            <i class="fas fa-check"></i> Acceptée
                                        </option>
                                        <option value="rejected" {{ old('status', $donation->status) == 'rejected' ? 'selected' : '' }}>
                                            <i class="fas fa-times"></i> Rejetée
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-align-left text-secondary me-2"></i>Description
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              class="form-control border-2 @error('description') is-invalid @enderror"
                                              rows="4" 
                                              placeholder="Optional: Add any additional details about the donation...">{{ old('description', $donation->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced action buttons with better styling and spacing -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('donations.index') }}" 
                                       class="btn btn-outline-secondary btn-lg px-4 py-2">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                    <button type="submit" 
                                            class="btn bg-gradient-dark btn-lg px-5 py-2 shadow-lg">
                                        <i class="fas fa-save me-2"></i>Update Donation
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
    border-color: #5e72e4;
    box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
}

.form-control-lg {
    padding: 0.875rem 1rem;
    font-size: 1rem;
    border-radius: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.15s ease;
}

.card {
    transition: all 0.3s ease;
}

.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

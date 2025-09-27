@extends('backoffice.layouts.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-lg border-0">
                    <!-- Enhanced header with better typography and spacing -->
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-5 pb-4">
                            <div class="text-center">
                                <h4 class="text-white font-weight-bold mb-1">Add New Donation</h4>
                                <p class="text-white-50 mb-0 text-sm">Fill in the details below to register a new donation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body px-4 py-5">
                        @if ($errors->any())
                            <!-- Improved error styling -->
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Please correct the following errors:</strong>
                                </div>
                                <ul class="list-unstyled mt-2 mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">• {{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('donations.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf

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
                                               class="form-control form-control-lg border-2" 
                                               placeholder="Enter donation location"
                                               value="{{ old('location') }}"
                                               required>
                                        <div class="invalid-feedback">Please provide a valid location.</div>
                                    </div>
                                </div>

                                <!-- Product Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name" class="form-label text-dark font-weight-bold mb-2">
                                            <i class="fas fa-box text-success me-2"></i>Product Name
                                        </label>
                                        <input type="text" 
                                               id="product_name" 
                                               name="product_name" 
                                               class="form-control form-control-lg border-2" 
                                               placeholder="Enter product name"
                                               value="{{ old('product_name') }}"
                                               required>
                                        <div class="invalid-feedback">Please provide a product name.</div>
                                    </div>
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity" class="form-label text-dark font-weight-bold mb-2">
                                            <i class="fas fa-sort-numeric-up text-info me-2"></i>Quantity
                                        </label>
                                        <input type="number" 
                                               id="quantity" 
                                               name="quantity" 
                                               class="form-control form-control-lg border-2" 
                                               placeholder="Enter quantity"
                                               value="{{ old('quantity') }}"
                                               min="1" 
                                               required>
                                        <div class="invalid-feedback">Please provide a valid quantity (minimum 1).</div>
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
                                                class="form-select form-select-lg border-2" 
                                                required>
                                            <option value="" disabled selected>Choose type...</option>
                                            <option value="recyclable" {{ old('type') == 'recyclable' ? 'selected' : '' }}>
                                                Recyclable
                                            </option>
                                            <option value="renewable" {{ old('type') == 'renewable' ? 'selected' : '' }}>
                                                Renewable
                                            </option>
                                        </select>
                                        <div class="invalid-feedback">Please select a donation type.</div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label text-dark font-weight-bold mb-2">
                                            <i class="fas fa-align-left text-secondary me-2"></i>Description
                                            <span class="text-muted font-weight-normal">(Optional)</span>
                                        </label>
                                        <textarea id="description" 
                                                  name="description" 
                                                  class="form-control border-2" 
                                                  rows="4"
                                                  placeholder="Add any additional details about the donation...">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <!-- Donation Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="donation_date" class="form-label text-dark font-weight-bold mb-2">
                                            <i class="fas fa-calendar-alt text-danger me-2"></i>Donation Date
                                        </label>
                                        <input type="date" 
                                               id="donation_date" 
                                               name="donation_date" 
                                               class="form-control form-control-lg border-2" 
                                               value="{{ old('donation_date', date('Y-m-d')) }}"
                                               required>
                                        <div class="invalid-feedback">Please select a donation date.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced button section with better spacing and styling -->
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('donations.index') }}" 
                                           class="btn btn-outline-secondary btn-lg px-4">
                                            <i class="fas fa-arrow-left me-2"></i>Cancel
                                        </a>
                                        <button type="submit" 
                                                class="btn bg-gradient-dark btn-lg px-5 shadow">
                                            <i class="fas fa-save me-2"></i>Submit Donation
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

    <!-- Added custom styles for enhanced appearance -->
    <style>
        .form-control, .form-select {
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.25);
            transform: translateY(-1px);
        }
        
        .form-label {
            font-size: 0.875rem;
            letter-spacing: 0.025em;
        }
        
        .card {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.025em;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .alert {
            border-radius: 10px;
        }
        
        .form-control::placeholder {
            color: #adb5bd;
            font-style: italic;
        }
    </style>
@endsection

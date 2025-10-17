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
                            
                            {{-- Added warning and error message displays --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            @if (session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('donations.store') }}" method="POST" id="donationForm">
                                @csrf
                                <input type="hidden" name="from_front" value="1">

                                <div class="row gx-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" id="location" name="location" class="form-control" placeholder="Enter location" value="{{ old('location') }}" required>
                                        @error('location')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="product_name" class="form-label">Product Name</label>
                                        <input type="text" id="product_name" name="product_name" class="form-control" placeholder="Enter product name" value="{{ old('product_name') }}" required>
                                        @error('product_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Enter quantity" value="{{ old('quantity') }}" min="1" required>
                                        @error('quantity')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select id="type" name="type" class="form-select" required>
                                            <option value="" disabled {{ !old('type') ? 'selected' : '' }}>Choose type...</option>
                                            <option value="recyclable" {{ old('type') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
                                            <option value="renewable" {{ old('type') == 'renewable' ? 'selected' : '' }}>Renewable</option>
                                        </select>
                                        @error('type')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description (Optional - AI can enhance it!)</label>
                                        {{-- Updated to use $description variable properly --}}
                                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Describe your donation, e.g., 'Used plastic bottles for recycling.'">{{ $description ?? old('description') }}</textarea>
                                        @error('description')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                        <button type="button" id="enhanceBtn" class="btn btn-outline-success btn-sm mt-2">
                                            <i class="bi bi-stars me-1"></i>Enhance with AI
                                        </button>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="donation_date" class="form-label">Donation Date</label>
                                        <input type="date" id="donation_date" name="donation_date" class="form-control" value="{{ old('donation_date', date('Y-m-d')) }}" required>
                                        @error('donation_date')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
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

    {{-- Removed duplicate script, kept only one clean version --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const enhanceBtn = document.getElementById('enhanceBtn');
        const descriptionField = document.getElementById('description');
        
        enhanceBtn.addEventListener('click', function () {
            const description = descriptionField.value;
            
            if (!description.trim()) {
                alert('Please enter a description to enhance!');
                return;
            }

            // Disable button and show loading state
            enhanceBtn.disabled = true;
            enhanceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enhancing...';

            // Create and submit form
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("donate.create") }}';
            
            const descInput = document.createElement('input');
            descInput.type = 'hidden';
            descInput.name = 'description';
            descInput.value = description;
            
            const previewInput = document.createElement('input');
            previewInput.type = 'hidden';
            previewInput.name = 'preview';
            previewInput.value = '1';
            
            form.appendChild(descInput);
            form.appendChild(previewInput);
            document.body.appendChild(form);
            form.submit();
        });
    });
    </script>

    <style>
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
    </style>
@endsection

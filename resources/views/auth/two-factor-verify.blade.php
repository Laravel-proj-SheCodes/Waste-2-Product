@extends('frontoffice.layouts.layoutfront')


@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 col-sm-8">
            <!-- Updated card styling to match template design with green primary color -->
            <div class="card shadow border-0">
                <!-- Updated header to use green gradient instead of dark gradient -->
                <div class="card-header bg-success text-white py-4">
                    <div class="text-center">
                        
                        <h5 class="text-white text-capitalize mb-0 fw-bold">
                            Verify Your Identity
                        </h5>
                    </div>
                </div>

                <!-- Updated card body styling to match template spacing -->
                <div class="card-body p-4">
                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="material-symbols-rounded me-3 mt-1">error</i>
                                <div>
                                    <strong>Error!</strong>
                                    @foreach ($errors->all() as $error)
                                        <div class="small">{{ $error }}</div>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="material-symbols-rounded me-3 mt-1">error</i>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Updated description text styling -->
                    <div class="mb-4">
                        <p class="text-secondary mb-2">
                          
                            <strong>Check your email</strong>
                        </p>
                        <p class="text-muted small mb-0">
                            A verification code has been sent to your registered email address. Please enter it below to complete your login.
                        </p>
                    </div>

                    <!-- Updated form styling to match template -->
                    <form action="{{ route('two-factor.verify-login') }}" method="POST" id="verificationForm">
                        @csrf
                        
                        <!-- Updated code input styling with template colors -->
                        <div class="mb-4">
                           
                            <div class="input-group">
                               
                                <input type="text" 
                                    class="form-control text-center fw-bold" 
                                    name="code" 
                                    id="codeInput"
                                    placeholder="000000" 
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    required
                                    autofocus
                                    inputmode="numeric"
                                    style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                            </div>
                            <small class="text-muted d-block mt-2">
                                
                                Enter the 6-digit code from your email
                            </small>
                        </div>

                        <!-- Updated button to use template's green primary color -->
                        <button type="submit" class="btn btn-success w-100 fw-bold mb-3" 
                                style="transition: all 0.3s ease;">
                           
                            Verify Code
                        </button>
                    </form>

                  

                    <!-- Updated expiration notice styling to match template -->
                    <div class="alert alert-info mt-4 mb-0">
                        <div class="d-flex align-items-center">
                          
                            <small>
                                <strong>Code expires in 10 minutes</strong> - Make sure to verify before it expires
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Updated footer text styling -->
            <div class="text-center mt-4">
                <p class="text-muted small">
                    <i class="material-symbols-rounded align-middle" style="font-size: 16px;">security</i>
                    Your account is protected with two-factor authentication
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('codeInput');
        
        // Auto-submit when 6 digits are entered
        codeInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                // Optional: auto-submit after a short delay
                setTimeout(() => {
                    document.getElementById('verificationForm').submit();
                }, 300);
            }
        });

        // Auto-dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endsection

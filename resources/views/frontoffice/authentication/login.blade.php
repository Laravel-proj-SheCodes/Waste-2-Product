@extends('frontoffice.layouts.layoutfront')

@section('content')
    {{-- Bandeau discret --}}
    <header class="py-5 bg-dark text-white text-center border-bottom">
        <h1 class="fw-bold mb-0">Sign in</h1>
        <p class="text-white-50 mb-0">Access your Waste2Product account</p>
    </header>

    <section class="py-5">
        <div class="container px-4">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:56px" class="mb-2">
                                <div class="fs-5 fw-semibold">Waste2Product</div>
                            </div>

                            {{-- Messages d’erreurs globaux --}}
                            @if ($errors->any())
                                <div class="alert alert-danger rounded-3">
                                    <ul class="m-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" novalidate>
                                @csrf

                               <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input id="email" 
               type="email" 
               name="email" 
               value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               autocomplete="email" 
               autofocus 
               required>
        @error('email') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input id="password" 
               type="password" 
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               autocomplete="current-password" 
               required>
        @error('password') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>
</div>


                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    {{-- Lien placeholder si tu ajoutes la réinitialisation plus tard --}}
                                    <a class="small text-decoration-none" href="#">Forgot password?</a>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
                                </button>
                            </form>

                            <hr class="my-4">
                            <p class="mb-0 text-center">
                                New here?
                                <a href="{{ route('register') }}" class="text-success fw-semibold">Create an account</a>
                            </p>
                        </div>
                    </div>

                    {{-- mini-note for UX --}}
                    <p class="text-center text-muted small mt-3">
                        By continuing you agree to our terms and privacy policy.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

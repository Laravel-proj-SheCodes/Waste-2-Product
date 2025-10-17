@extends('frontoffice.layouts.layoutfront')

@section('content')
    <header class="py-5 bg-dark text-white text-center border-bottom">
        <h1 class="fw-bold mb-0">Create an account</h1>
        <p class="text-white-50 mb-0">Join the Waste2Product community</p>
    </header>

    <section class="py-5">
        <div class="container px-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/logo-w2p.png') }}" alt="Logo" style="height:56px" class="mb-2">
                                <div class="fs-5 fw-semibold">Waste2Product</div>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success rounded-3">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger rounded-3">
                                    <ul class="m-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}" novalidate>
                                @csrf

                                {{-- Par défaut on inscrit un client --}}
                                <input type="hidden" name="role" value="client">

                                {{-- Full name --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input id="name"
                                               type="text"
                                               name="name"
                                               value="{{ old('name') }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               required
                                               autocomplete="name"
                                               autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input id="email"
                                               type="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               required
                                               autocomplete="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input id="password"
                                               type="password"
                                               name="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               required
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Confirm password --}}
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirm password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                        <input id="password_confirmation"
                                               type="password"
                                               name="password_confirmation"
                                               class="form-control"
                                               required
                                               autocomplete="new-password">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="bi bi-person-plus me-1"></i> Create account
                                </button>
                            </form>

                            <hr class="my-4">
                            <p class="mb-0 text-center">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-success fw-semibold">Sign in</a>
                            </p>
                        </div>
                    </div>

                    <p class="text-center text-muted small mt-3">
                        Your role is set to <span class="fw-semibold text-success">client</span> by default.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
  <div class="container px-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Forgot Password</h3>

            @if (session('status'))
              <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" class="form-control" required autofocus>
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
              </div>

              <button type="submit" class="btn btn-success w-100">
                Send Password Reset Link
              </button>
            </form>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}" class="text-decoration-none text-success">
                Back to login
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
<section class="py-5">
  <div class="container px-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Reset Password</h3>

            <form method="POST" action="{{ route('password.update') }}">
              @csrf
              <input type="hidden" name="token" value="{{ $token }}">

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" class="form-control" required autofocus>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input id="password" type="password" name="password" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-success w-100">
                Reset Password
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

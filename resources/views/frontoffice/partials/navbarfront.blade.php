<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Waste2Product Logo" 
                 style="width:40px; height:auto; margin-right:10px;">
            <span class="fw-bold">Waste2Product</span>
        </a>

        <!-- Menu -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Home</a>
                </li>
            </ul>

            <div class="ms-3">
                @guest
                    <!-- Si utilisateur NON connecté -->
                    <a class="btn btn-outline-light me-2" href="{{ route('login') }}">Sign In</a>
                    <a class="btn btn-success" href="{{ route('register') }}">Sign Up</a>
                @endguest

                @auth
                    <!-- Si utilisateur connecté -->
                    @if(Auth::user()->isAdmin())
                        <a class="btn btn-primary me-2" href="{{ route('dashboard') }}">Dashboard</a>
                    @endif

                    <span class="text-white me-2">
                        {{ Auth::user()->name }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

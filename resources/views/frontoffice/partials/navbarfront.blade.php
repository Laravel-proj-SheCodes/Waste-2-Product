<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Waste2Product Logo"
                 style="width:40px;height:auto;margin-right:10px;">
            <span class="fw-bold">Waste2Product</span>
        </a>

        {{-- Toggler (mobile) --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Contenu --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Liens de navigation – poussés à droite --}}
            <ul class="navbar-nav ms-auto align-items-lg-center">
                {{-- Home toujours visible --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        Home
                    </a>
                </li>

                {{-- Les gestions : visibles uniquement si connecté --}}
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('troc.*') ? 'active' : '' }}"
                           href="{{ route('troc.index') }}">Troc</a>
                    </li>
                  <li class="nav-item">
  <a class="nav-link {{ request()->routeIs('front.waste-posts.*') ? 'active' : '' }}"
 href="{{ route('front.waste-posts.index') }}">
     Post-Déchet
 </a>
</li>




                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transformations.*') ? 'active' : '' }}"
                           href="{{ route('transformations.index') }}">Transformation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('donations.*') ? 'active' : '' }}"
                           href="{{ route('donations.index') }}">Donation management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('marketplace.*') ? 'active' : '' }}"
                           href="{{ route('marketplace.index') }}">Marketplace management</a>
                    </li>

                    {{-- Espace avant l’avatar --}}
                    <li class="nav-item d-none d-lg-block" style="width:8px;"></li>

                    {{-- Avatar (initiales) + menu --}}
                    @php
                        $fullName = trim(Auth::user()->name ?? '');
                        $parts = preg_split('/\s+/', $fullName);
                        $initials = '';
                        foreach ($parts as $p) {
                            if ($p !== '') { $initials .= mb_strtoupper(mb_substr($p,0,1)); }
                            if (mb_strlen($initials) >= 2) break;
                        }
                        if ($initials === '') $initials = 'U';
                    @endphp

                    <li class="nav-item dropdown ms-2">
                        <button class="btn btn-success rounded-circle fw-bold text-uppercase dropdown-toggle"
                                type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false"
                                style="width:40px;height:40px;line-height:40px;padding:0;">
                            {{ $initials }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li class="px-3 py-2">
                                <div class="fw-semibold">{{ $fullName }}</div>
                                <div class="small text-muted">{{ Auth::user()->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">Manage profile</a></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                {{-- Boutons invités --}}
                @guest
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-light me-2" href="{{ route('login') }}">Sign In</a>
                        <a class="btn btn-success" href="{{ route('register') }}">Sign Up</a>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

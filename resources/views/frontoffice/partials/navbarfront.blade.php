<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid px-2 px-lg-3">
    {{-- Logo + marque --}}
    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="Waste2Product">
      <img
        src="{{ asset('images/logo-w2p.png') }}"
        alt="Waste2Product logo"
        class="brand-logo me-2"
        width="40" height="40"
        loading="eager" decoding="async"
        onerror="this.style.display='none';"
      >
      <span class="fw-bold text-white">Waste2Product</span>
    </a>

    {{-- Toggler (mobile) --}}
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    {{-- Contenu --}}
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">

        {{-- Home --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
            Home
          </a>
        </li>

        @auth
          {{-- Post-Déchet --}}
          @if (Route::has('front.waste-posts.index'))
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('front.waste-posts.*') ? 'active' : '' }}"
                 href="{{ route('front.waste-posts.index') }}">
                Post-Déchet
              </a>
            </li>
          @endif

          {{-- Transformation (affiché seulement si la route existe) --}}
          @if (Route::has('front.transformations.index'))
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('front.transformations.*') ? 'active' : '' }}"
                 href="{{ route('front.transformations.index') }}">
                Transformation
              </a>
            </li>
          @endif

          {{-- Marketplace --}}
          @if (Route::has('marketplace'))
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('marketplace') ? 'active' : '' }}"
                 href="{{ route('marketplace') }}">
                Marketplace
              </a>
            </li>
          @endif

          {{-- Troc (actif sur /home/troc et /home/offres-troc/*) --}}
          @php
            $isTrocActive = request()->routeIs('postdechets.troc.front') || request()->routeIs('offres-troc.*.front');
          @endphp
          @if (Route::has('postdechets.troc.front'))
            <li class="nav-item">
              <a class="nav-link {{ $isTrocActive ? 'active' : '' }}"
                 href="{{ route('postdechets.troc.front') }}">
                Troc
              </a>
            </li>
          @endif

          {{-- Donation Dropdown --}}
          @if (Route::has('donate.donationpage') || Route::has('donate.myRequests'))
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ request()->routeIs('donate.*') ? 'active' : '' }}"
                 href="#" id="donationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Donation
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="donationDropdown">
                @if (Route::has('donate.donationpage'))
                  <li>
                    <a class="dropdown-item {{ request()->routeIs('donate.donationpage') || request()->routeIs('donate.create') || request()->routeIs('donate.thankyou') ? 'active' : '' }}"
                       href="{{ route('donate.donationpage') }}">
                      Donate
                    </a>
                  </li>
                @endif
                @if (Route::has('donate.myRequests'))
                  <li>
                    <a class="dropdown-item {{ request()->routeIs('donate.myRequests') ? 'active' : '' }}"
                       href="{{ route('donate.myRequests') }}">
                      My Requests
                    </a>
                  </li>
                @endif
              </ul>
            </li>
          @endif

          {{-- espace --}}
          <li class="nav-item d-none d-lg-block" style="width:8px;"></li>

          {{-- Avatar (initiales) --}}
          @php
            $fullName = trim(Auth::user()->name ?? '');
            $parts = preg_split('/\s+/', $fullName);
            $initials = '';
            foreach ($parts as $p) { if ($p !== '') { $initials .= mb_strtoupper(mb_substr($p,0,1)); } if (mb_strlen($initials) >= 2) break; }
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
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="/home/transactions-troc">Mes Transactions</a></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="dropdown-item text-danger" type="submit">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @endauth

        {{-- Invités --}}
        @guest
          <li class="nav-item ms-3">
            @if (Route::has('login'))
              <a class="btn btn-outline-light me-2" href="{{ route('login') }}">Sign In</a>
            @endif
            @if (Route::has('register'))
              <a class="btn btn-success" href="{{ route('register') }}">Sign Up</a>
            @endif
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>

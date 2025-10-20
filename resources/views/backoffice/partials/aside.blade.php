<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <!-- Logo -->
    <div class="sidenav-header text-center py-1">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand d-block mx-auto" href="{{ route('dashboard') }}">
            <img src="{{ Vite::asset('resources/assets-backoffice/img/logoWaste2Product.png') }}"
                 class="navbar-brand-img"
                 style="height: 100%; width: 100%; max-height: 180px; object-fit: contain;"
                 alt="main_logo">
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto d-flex flex-column h-100" id="sidenav-collapse-main">
        <ul class="navbar-nav flex-column flex-grow-1">

            <li class="nav-item">
                <a class="nav-link text-dark {{ Route::is('users.*') ? 'active bg-gradient-dark text-white' : '' }}" href="{{ route('users.index') }}">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">Utilisateurs</span>
                </a>
            </li>

            <li class="nav-item">
  <a class="nav-link text-dark {{ Route::is('admin.profile.show') ? 'active bg-gradient-dark text-white' : '' }}"
     href="{{ route('admin.profile.show') }}">
      <i class="material-symbols-rounded opacity-5">account_circle</i>
      <span class="nav-link-text ms-1">Profil</span>
  </a>
</li>




            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/troc') }}">
                    <i class="material-symbols-rounded opacity-5">swap_horiz</i>
                    <span class="nav-link-text ms-2">Posts de Troc</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/transactions-troc') }}">
                    <i class="material-symbols-rounded opacity-5">local_shipping</i>
                    <span class="nav-link-text ms-2">Transactions de Troc</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('postdechets.index') }}">
                    <i class="material-symbols-rounded opacity-5">delete</i>
                    <span class="nav-link-text ms-1">Waste Posts</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('propositions.index') }}">
                    <i class="material-symbols-rounded opacity-5">edit_note</i>
                    <span class="nav-link-text ms-1">Proposals</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('annonces.index') }}">
                    <i class="material-symbols-rounded opacity-5">local_offer</i>
                    <span class="nav-link-text ms-1">Annonces</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/transactions') }}">
                    <i class="material-symbols-rounded opacity-5">swap_horiz</i>
                    <span class="nav-link-text ms-1">Transactions</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('donations.index') }}">
                    <i class="material-symbols-rounded opacity-5">volunteer_activism</i>
                    <span class="nav-link-text ms-1">Donations</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('commandes.index')}}">
                    <i class="material-symbols-rounded opacity-5">list_alt</i>
                    <span class="nav-link-text ms-1">Commands</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/payments') }}">
                    <i class="material-symbols-rounded opacity-5">payment</i>
                    <span class="nav-link-text ms-1">Payments</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('proposition-transformations.index') }}">
                    <i class="material-symbols-rounded opacity-5">build</i>
                    <span class="nav-link-text ms-1">Transformations</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('processus-transformations.index')}}">
                    <i class="material-symbols-rounded opacity-5">inventory</i>
                    <span class="nav-link-text ms-1">Process</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('produit-transformes.index')}}">
                    <i class="material-symbols-rounded opacity-5">inventory</i>
                    <span class="nav-link-text ms-1">Produit</span>
                </a>
            </li>

        </ul>

        {{-- === Bouton Déconnexion collé en bas === --}}
        <div class="mt-auto pt-2 border-top px-3 pb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                    <i class="material-symbols-rounded me-2">logout</i>
                    LogOut
                </button>
            </form>
        </div>

    </div>
</aside>

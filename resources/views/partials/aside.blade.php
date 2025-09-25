<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="https://demos.creative-tim.com/material-dashboard/pages/dashboard" target="_blank">
            <img src="{{ Vite::asset('resources/assets/img/logo-ct-dark.png') }}" class="navbar-brand-img" width="26" height="26" alt="main_logo">
            <span class="ms-1 text-sm text-dark">Creative Tim</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/users') }}">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/profiles') }}">
                    <i class="material-symbols-rounded opacity-5">account_circle</i>
                    <span class="nav-link-text ms-1">Profiles</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/waste-posts') }}">
                    <i class="material-symbols-rounded opacity-5">delete</i>
                    <span class="nav-link-text ms-1">Waste Posts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/proposals') }}">
                    <i class="material-symbols-rounded opacity-5">edit_note</i>
                    <span class="nav-link-text ms-1">Proposals</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/offers') }}">
                    <i class="material-symbols-rounded opacity-5">local_offer</i>
                    <span class="nav-link-text ms-1">Offers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/transactions') }}">
                    <i class="material-symbols-rounded opacity-5">swap_horiz</i>
                    <span class="nav-link-text ms-1">Transactions</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/commands') }}">
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
                <a class="nav-link text-dark" href="{{ url('/transformations') }}">
                    <i class="material-symbols-rounded opacity-5">build</i>
                    <span class="nav-link-text ms-1">Transformations</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ url('/products') }}">
                    <i class="material-symbols-rounded opacity-5">inventory</i>
                    <span class="nav-link-text ms-1">Products</span>
                </a>
            </li>
            

</aside>
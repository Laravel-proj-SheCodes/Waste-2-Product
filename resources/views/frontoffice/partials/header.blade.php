@php
    // Chemin de l’image si elle existe, sinon un fallback (tu peux changer le fallback)
    $heroPath = public_path('images/header-hero.jpg');
    $heroUrl  = file_exists($heroPath)
        ? asset('images/header-hero.jpg') . '?v=' . filemtime($heroPath)   // bust cache
        : 'https://images.unsplash.com/photo-1557800636-894a64c1696f?q=80&w=1600&auto=format&fit=crop';
@endphp

<header class="hero-header py-5">
  <style>
    .hero-header{
      min-height: 420px;
      /* image de fond + voile sombre */
      background-image:
        linear-gradient(0deg, rgba(0,0,0,.55), rgba(0,0,0,.55)),
        url('{{ $heroUrl }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
  </style>

  <div class="container px-5">
    <div class="row gx-5 justify-content-center">
      <div class="col-lg-8">
        <div class="text-center my-5 text-white">
          <h1 class="display-5 fw-bolder text-success mb-2">Waste2Product</h1>
          <p class="lead text-white-50 mb-4">
            Give waste a second life: reuse, exchange, repair and transform.<br>
            Reduce landfilling and create local value together.
          </p>
          <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
            <a class="btn btn-success btn-lg px-4 me-sm-3" href="{{ route('front.waste-posts.index') }}">
              Explore Waste Posts
            </a>
            <a class="btn btn-outline-light btn-lg px-4" href="{{ route('register') }}">
              Create an Account
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

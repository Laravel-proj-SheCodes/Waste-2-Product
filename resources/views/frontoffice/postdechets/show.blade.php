@extends('frontoffice.layouts.layoutfront')

@php
    use Illuminate\Support\Facades\Storage;

    $photos = is_array($postDechet->photos) ? $postDechet->photos : [];
    $main   = asset('images/placeholder.jpg');

    if (count($photos)) {
        $first = ltrim(str_replace('\\','/',$photos[0]), '/');
        if (Storage::disk('public')->exists($first)) {
            $main = Storage::disk('public')->url($first);
        } elseif (file_exists(public_path('storage/'.$first))) {
            $main = asset('storage/'.$first);
        }
    }

    // Sécuriser la variable $received si le contrôleur ne l'a pas fournie
    $received = isset($received) ? $received : collect();
@endphp

@section('content')
<style>
  /* HERO VERT + titre blanc */
  .hero{
    background:#198754;color:#ffffff;border-radius:16px;overflow:hidden;
    padding:24px 20px;margin-bottom:24px
  }
  .hero h1{color:#ffffff;font-weight:800;letter-spacing:.3px;margin:0}
  .hero .sub{opacity:.95}

  .thumb {width:84px;height:84px;object-fit:cover;border-radius:10px;cursor:pointer}
  .thumb.active{outline:3px solid #198754}
  .photo-main{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:16px;background:#f3f5f7}
  .card-soft{border:0;border-radius:16px;background:#fff;box-shadow:0 10px 28px rgba(0,0,0,.08)}
  .specs li{margin-bottom:.4rem}
</style>

<div class="container py-4">
  <a href="{{ route('front.waste-posts.index') }}" class="text-success">&larr; Retour</a>

  {{-- Bandeau vert --}}
  <div class="hero mt-3">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="h3">{{ $postDechet->titre }}</h1>
        <p class="sub mb-0">
          Catégorie <strong>{{ $postDechet->categorie }}</strong>
          • {{ $postDechet->quantite }} {{ $postDechet->unite_mesure }}
          • <span class="text-capitalize">{{ $postDechet->etat }}</span>
        </p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        @if($postDechet->localisation)
          <span class="badge bg-white text-success">{{ $postDechet->localisation }}</span>
        @endif
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Galerie gauche --}}
    <div class="col-lg-7">
      <img id="mainPhoto" src="{{ $main }}" class="photo-main" alt="{{ $postDechet->titre }}"
           onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';">

      @if(count($photos) > 1)
        <div class="d-flex flex-wrap gap-2 mt-3">
          @foreach($photos as $idx => $p)
            @php
              $p = ltrim(str_replace('\\','/',$p), '/');
              $u = Storage::disk('public')->exists($p)
                   ? Storage::disk('public')->url($p)
                   : (file_exists(public_path('storage/'.$p)) ? asset('storage/'.$p) : asset('images/placeholder.jpg'));
            @endphp
            <img src="{{ $u }}" data-src="{{ $u }}" class="thumb {{ $idx===0 ? 'active' : '' }}"
                 onclick="document.getElementById('mainPhoto').src=this.dataset.src;
                          document.querySelectorAll('.thumb').forEach(t=>t.classList.remove('active'));
                          this.classList.add('active');"
                 onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';" alt="miniature">
          @endforeach
        </div>
      @endif
    </div>

    {{-- Fiche droite --}}
    <div class="col-lg-5">
      <div class="card-soft p-4">
        <p class="text-muted">{{ $postDechet->description }}</p>

        <ul class="list-unstyled specs small">
          <li><strong>Catégorie :</strong> {{ $postDechet->categorie }}</li>
          <li><strong>Quantité :</strong> {{ $postDechet->quantite }} {{ $postDechet->unite_mesure }}</li>
          <li class="text-capitalize"><strong>État :</strong> {{ $postDechet->etat }}</li>
          <li><strong>Localisation :</strong> {{ $postDechet->localisation }}</li>
          @if($postDechet->created_at)
            <li><strong>Publ. :</strong> {{ $postDechet->created_at->translatedFormat('d/m/Y • H:i') }}</li>
          @endif
        </ul>

        @auth
          @if(auth()->id() === $postDechet->user_id)
            {{-- Propriétaire : actions d’édition --}}
            <div class="d-flex gap-2 mt-2">
              <a href="{{ route('front.waste-posts.edit', $postDechet) }}" class="btn btn-outline-success">
                Modifier
              </a>
              <form method="POST" action="{{ route('front.waste-posts.destroy', $postDechet) }}"
                    onsubmit="return confirm('Supprimer ce post ?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger" type="submit">Supprimer</button>
              </form>
            </div>
          @else
            {{-- Utilisateur connecté mais NON propriétaire : bouton Proposer --}}
            @if (Route::has('front.propositions.create'))
              <a href="{{ route('front.propositions.create', $postDechet) }}"
                 class="btn btn-success w-100 mt-2">
                Faire une proposition
              </a>
            @endif
          @endif
        @else
          {{-- Invité : incitation à se connecter pour proposer --}}
          <a href="{{ route('login') }}" class="btn btn-outline-success w-100 mt-2">
            Connectez-vous pour proposer
          </a>
        @endauth
      </div>
    </div>
  </div>

  {{-- =========================
       Propositions reçues (propriétaire uniquement)
  ========================= --}}
  @auth
    @if(auth()->id() === $postDechet->user_id)
      <div class="mt-4">
        <h3 class="h5 mb-3">Propositions reçues</h3>

        @if($received->isEmpty())
          <div class="alert alert-light border">Aucune proposition reçue pour le moment.</div>
        @else
          <div class="list-group">
            @foreach($received as $prop)
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <div class="fw-semibold">
                      {{ $prop->user->name ?? 'Utilisateur' }}
                      <span class="badge ms-2
                        @switch($prop->statut)
                          @case('acceptee') bg-success @break
                          @case('refusee')  bg-danger  @break
                          @default         bg-secondary
                        @endswitch">
                        {{ str_replace('_',' ', $prop->statut ?? 'en_attente') }}
                      </span>
                    </div>
                    <div class="text-muted small">
                      {{ $prop->created_at ? $prop->created_at->diffForHumans() : ($prop->date_proposition ? \Carbon\Carbon::parse($prop->date_proposition)->diffForHumans() : '') }}
                    </div>
                    <div class="mt-2">
                      {!! nl2br(e($prop->description)) !!}
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <a href="{{ route('front.propositions.index') }}" class="btn btn-outline-success mt-3">
            Voir toutes mes propositions
          </a>
        @endif
      </div>
    @endif
  @endauth
</div>
@endsection

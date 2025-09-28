@extends('frontoffice.layouts.layoutfront')

@php
use Illuminate\Support\Facades\Storage;

// S'assurer que $post->photos est bien un tableau
$photos = $post->photos;
if (is_string($photos)) {
    $photos = json_decode($photos, true) ?? [];
}
if (!is_array($photos)) {
    $photos = [];
}

// Définir la photo principale
$mainPhoto = asset('images/placeholder.jpg');
if (count($photos)) {
    $first = ltrim(str_replace('\\','/',$photos[0]), '/');
    if (Storage::disk('public')->exists($first)) {
        $mainPhoto = Storage::disk('public')->url($first);
    } elseif (file_exists(public_path('storage/'.$first))) {
        $mainPhoto = asset('storage/'.$first);
    }
}

// Vérifier si une offre a été acceptée
$hasAcceptedOffer = $offres->contains(function($offre) {
    return strtolower($offre->status) === 'accepted';
});

// Sécuriser $received
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
  .action-btns { display:flex; gap:.5rem; margin-top:.5rem; }
  .accept-btn { flex:1; background:#198754; color:#fff; border:none; border-radius:8px; padding:.5rem; cursor:pointer; }
  .accept-btn:hover { background:#157347; }
  .reject-btn { flex:1; background:#dc3545; color:#fff; border:none; border-radius:8px; padding:.5rem; cursor:pointer; }
  .reject-btn:hover { background:#b02a37; }
  .offre-accepted-message { background:#d0f0d0; color:#198754; padding:.5rem 1rem; border-radius:8px; margin-bottom:1rem; }
  .troc-card { border:0; border-radius:16px; background:#fff; box-shadow:0 10px 28px rgba(0,0,0,.08); padding:1rem; position:relative; }
  .troc-image { width:100%; aspect-ratio:4/3; object-fit:cover; border-radius:16px; background:#f3f5f7; margin-bottom:.5rem; }
  .offre-accepted { border:2px solid #198754; }
  .dropdown { position:absolute; top:1rem; right:1rem; }
  .dropdown-btn { background:none; border:none; font-size:1.5rem; cursor:pointer; }
  .dropdown-content { display:none; position:absolute; right:0; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,.08); border-radius:8px; z-index:10; }
  .dropdown:hover .dropdown-content { display:block; }
  .dropdown-content a, .dropdown-content button { display:block; padding:.5rem 1rem; text-decoration:none; width:100%; text-align:left; border:none; background:none; cursor:pointer; }
  .dropdown-content a:hover, .dropdown-content button:hover { background:#f3f3f3; }
</style>

<div class="container py-4">
  <a href="{{ route('postdechets.troc.front') }}" class="text-success mb-3 d-inline-block">&larr; Retour</a>

  {{-- Bandeau vert --}}
  <div class="hero mb-4">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="h3">{{ $post->titre }}</h1>
        <p class="sub mb-0">
          Catégorie <strong>{{ $post->categorie }}</strong>
          • {{ $post->quantite }} {{ $post->unite_mesure }}
          • <span class="text-capitalize">{{ $post->etat }}</span>
        </p>
      </div>
      <div class="col-md-4 text-md-end mt-3 mt-md-0">
        @if($post->localisation)
          <span class="badge bg-white text-success">{{ $post->localisation }}</span>
        @endif
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- Galerie gauche --}}
    <div class="col-lg-7">
      <img id="mainPhoto" src="{{ $mainPhoto }}" class="photo-main" alt="{{ $post->titre }}"
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
        <p class="text-muted">{{ $post->description }}</p>
        <ul class="list-unstyled specs small">
          <li><strong>Catégorie :</strong> {{ $post->categorie }}</li>
          <li><strong>Quantité :</strong> {{ $post->quantite }} {{ $post->unite_mesure }}</li>
          <li class="text-capitalize"><strong>État :</strong> {{ $post->etat }}</li>
          <li><strong>Localisation :</strong> {{ $post->localisation }}</li>
          @if($post->created_at)
            <li><strong>Publ. :</strong> {{ $post->created_at->translatedFormat('d/m/Y • H:i') }}</li>
          @endif
        </ul>

        @auth
          @if(auth()->id() === $post->user_id)
            <div class="d-flex gap-2 mt-2">
              <a href="{{ url('waste-posts/edit', $post) }}" class="btn btn-outline-success">
                Modifier
              </a>
             <form method="POST" action="{{ route('front.waste-posts.destroy', $post->id) }}"
      onsubmit="return confirm('Supprimer ce post ?');">
    @csrf
    @method('DELETE')
    <button class="btn btn-outline-danger" type="submit">Supprimer</button>
</form>

            </div>
          @endif
        @endauth
      </div>
    </div>
  </div>

  {{-- Offres associées --}}
  <div class="mt-4">
    <h3 class="h5 mb-3">Offres associées</h3>

    @if($hasAcceptedOffer)
      <div class="offre-accepted-message">
        ✓ Une offre a été acceptée pour ce post. Les boutons d'action sont désactivés.
      </div>
    @endif

    @if($offres->isEmpty())
      <p class="text-muted">Aucune offre associée pour ce post.</p>
    @else
      <div class="row g-3">
        @foreach($offres as $offre)
          @php
            $offrePhotos = $offre->photos;
            if (is_string($offrePhotos)) { $offrePhotos = json_decode($offrePhotos, true) ?? []; }
            if (!is_array($offrePhotos)) { $offrePhotos = []; }
            $offreStatus = strtolower($offre->status);
            $showButtons = !$hasAcceptedOffer && $offreStatus !== 'rejected';
            $mainOffrePhoto = asset('images/placeholder.jpg');
            if(count($offrePhotos)){
                $firstOffre = ltrim(str_replace('\\','/',$offrePhotos[0]), '/');
                if (Storage::disk('public')->exists($firstOffre)) {
                    $mainOffrePhoto = Storage::disk('public')->url($firstOffre);
                } elseif(file_exists(public_path('storage/'.$firstOffre))) {
                    $mainOffrePhoto = asset('storage/'.$firstOffre);
                }
            }
          @endphp

          <div class="col-md-6 col-lg-4">
            <div class="troc-card @if($offreStatus==='accepted') offre-accepted @endif">
              <img src="{{ $mainOffrePhoto }}" alt="{{ $offre->description }}" class="troc-image">
              <h5>{{ $offre->description }}</h5>
              <p>Quantité : {{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
              <p>État : {{ ucfirst($offre->etat) }}</p>
              <p>Statut : {{ ucfirst($offre->status) }}</p>

              @if($showButtons)
                <div class="action-btns">
                  <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST" style="width:100%">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="accept-btn">Accepter</button>
                  </form>
                  <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST" style="width:100%">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="reject-btn">Refuser</button>
                  </form>
                </div>
              @elseif($offreStatus==='accepted')
                <div class="text-center mt-3 p-2 bg-green-100 text-green-700 rounded-lg">
                  <strong>✓ Cette offre a été acceptée</strong>
                </div>
              @elseif($offreStatus==='rejected')
                <div class="text-center mt-3 p-2 bg-red-100 text-red-700 rounded-lg">
                  <strong>✗ Cette offre a été refusée</strong>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>
@endsection

@extends('frontoffice.layouts.layoutfront')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
  .troc-card { border:0; border-radius:16px; overflow:hidden; background:#fff; box-shadow:0 8px 24px rgba(0,0,0,0.08); transition: transform 0.18s ease, box-shadow 0.18s ease; }
  .troc-card:hover { transform:translateY(-4px); box-shadow:0 14px 34px rgba(0,0,0,0.14); }
  .troc-card.favorited-card { box-shadow:0 8px 24px rgba(137,235,91,0.91); border:1px solid rgba(25,135,84,0.5); }
  .troc-card.favorited-card:hover { box-shadow:0 14px 34px rgba(25,135,84,0.5); }
  .troc-thumb { width:100%; height:220px; object-fit:cover; background:#f3f5f7; display:block; }
  .troc-chip { font-weight:600; }
  .troc-meta { font-size:0.9rem; color:#6c757d; }
  .btn-outline-purple { border-color:#9b59b6; color:#9b59b6; }
  .btn-outline-purple:hover { background:#9b59b6; color:#fff; }
  .favorite-icon { cursor:pointer; font-size:1.2rem; transition:color 0.2s ease; }
  .favorite-icon.favorited { color:#198754; }
  .estimate-result { font-weight:600; color:#d97706; margin-top:5px; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.estimate-form').submit(function(e) {
        e.preventDefault(); // Empêche le rechargement de page

        var form = $(this);
        var postId = form.data('post-id');
        var token = form.find('input[name="_token"]').val();

        $.ajax({
            url: '/waste-posts/' + postId + '/estimate-ai',
            type: 'POST',
            data: { _token: token },
            success: function(response) {
                if(response.success) {
                    $('#estimate-result-' + postId).html('Valeur estimée: ' + response.estimated_price + ' TND');
                } else {
                    $('#estimate-result-' + postId).html('Impossible d’estimer la valeur pour cet objet.');
                }
            },
            error: function(xhr) {
                $('#estimate-result-' + postId).html('Erreur IA : ' + (xhr.responseJSON?.error || 'Erreur inconnue'));
            }
        });
    });
});
</script>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title" style="color:#2a5d3a;">Posts de Troc</h1>
    <div class="d-flex gap-2">
        {{-- Formulaire recherche visuelle --}}
        <form id="visualSearchForm" action="{{ route('front.waste-posts.visual-search') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="photo" accept="image/*" class="form-control form-control-sm" style="display:inline-block; width:auto;">
            <button type="submit" class="btn btn-outline-success btn-sm">Recherche visuelle</button>
        </form>

        {{-- Bouton pour revenir à la liste complète --}}
        @if(isset($searching) && $searching)
            <a href="{{ url('/home/troc') }}" class="btn btn-outline-primary btn-sm">Voir tous les posts</a>
        @endif

        @auth
            <a href="{{ route('front.waste-posts.create') }}" class="btn btn-success btn-sm">Nouveau post</a>
        @endauth
    </div>
  </div>

  @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      {{ session('error') }}
    </div>
  @endif

  {{-- Message recherche --}}
  @if(isset($searching) && $searching)
    <div class="alert alert-info mb-4">Résultats pour : <strong>{{ $searchLabel }}</strong></div>
  @endif

  <div class="row g-4">
    @forelse ($posts as $post)
      @php
        $paths = is_array($post->photos) ? $post->photos : [];
        $firstPath = count($paths) ? str_replace('\\', '/', $paths[0]) : null;
        $imgUrl = $firstPath && Storage::disk('public')->exists(ltrim($firstPath, '/')) 
                    ? Storage::disk('public')->url(ltrim($firstPath, '/')) 
                    : asset('images/placeholder.jpg');
        $inlineFallback = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
        $isFavorited = auth()->check() && $post->favoritedBy->contains(auth()->id());
        $hasAcceptedOffer = $post->offreTrocs()->where('status', 'accepted')->exists();
      @endphp

      <div class="col-12 col-sm-6 col-lg-4">
        <div class="troc-card h-100 {{ $isFavorited ? 'favorited-card' : '' }}">
          <div class="position-relative">
            <img src="{{ $imgUrl }}" alt="" class="troc-thumb" loading="lazy" onerror="this.onerror=null;this.src='{{ $inlineFallback }}';">
            <div class="position-absolute top-0 start-0 p-2">
              <span class="badge bg-light text-dark troc-chip">{{ $post->categorie ?? '—' }}</span>
            </div>
            <div class="position-absolute top-0 end-0 p-2">
              <span class="badge bg-success troc-chip">{{ $post->quantite }} {{ $post->unite_mesure }}</span>
            </div>
            @auth
              <div class="position-absolute top-0 end-0 p-2 mt-5">
                <form action="{{ route('favorites.toggle', $post->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-link p-0 favorite-icon {{ $isFavorited ? 'favorited' : '' }}">
                    <i class="bi {{ $isFavorited ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                  </button>
                </form>
              </div>
            @endauth
          </div>

          <div class="p-3">
            <h5 class="fw-semibold mb-2">{{ $post->titre }}</h5>
            <p class="text-muted mb-3" style="min-height:48px;">{{ \Illuminate\Support\Str::limit($post->description, 120) }}</p>

            <div class="d-flex justify-content-between troc-meta mb-3">
              <span><i class="bi bi-geo-alt"></i> {{ $post->localisation ?? '—' }}</span>
              <span class="text-capitalize">{{ $post->etat ?? '—' }}</span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-outline-success btn-sm" href="{{ route('front.waste-posts.show', $post) }}">Voir</a>
            <form class="estimate-form" data-post-id="{{ $post->id }}">
                @csrf
                <button type="submit" class="btn btn-outline-warning btn-sm">Estimation IA</button>
            </form>

            <div class="estimate-result" id="estimate-result-{{ $post->id }}">
                @if(isset($post->estimated_price))
                    Valeur estimée: {{ $post->estimated_price }} TND
                @endif
            </div>


              @auth
                @if(auth()->id() === $post->user_id)
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('postdechets.edit', $post->id) }}">Modifier</a>
                  <a class="btn btn-outline-purple btn-sm" href="{{ route('offres-troc.show.front', $post->id) }}">Voir Offres</a>
                @endif
                @if(auth()->id() !== $post->user_id && !$hasAcceptedOffer)
                  <a class="btn btn-outline-primary btn-sm" href="{{ route('offres-troc.create.front', $post->id) }}">Proposer une Offre</a>
                @endif
              @endauth
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p class="text-muted">Aucun post de troc disponible pour le moment.</p></div>
    @endforelse
  </div>

  <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection

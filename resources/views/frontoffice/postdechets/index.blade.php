@extends('frontoffice.layouts.layoutfront')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<style>
  .w2p-card{
    border:0;border-radius:16px;overflow:hidden;background:#fff;
    box-shadow:0 8px 24px rgba(0,0,0,.08);
    transition:transform .18s ease, box-shadow .18s ease;
  }
  .w2p-card:hover{transform:translateY(-4px);box-shadow:0 14px 34px rgba(0,0,0,.14)}
  .w2p-thumb{width:100%;height:220px;object-fit:cover;background:#f3f5f7;display:block}
  .w2p-chip{font-weight:600}
  .w2p-meta{font-size:.9rem;color:#6c757d}
</style>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Posts déchets</h1>

    @auth
      <a href="{{ route('front.waste-posts.create') }}" class="btn btn-success">Nouveau post</a>
    @endauth
  </div>

  <div class="row g-4">
    @forelse ($posts as $post)
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="w2p-card h-100">
          <div class="position-relative">
            {{-- Image principale (via accessor $post->main_photo_url) --}}
            <img
              src="{{ $post->main_photo_url }}"
              alt="{{ $post->titre }}"
              class="w2p-thumb"
              loading="lazy"
              onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
            >

            {{-- Catégorie en haut à gauche --}}
            <div class="position-absolute top-0 start-0 p-2">
              <span class="badge bg-light text-dark w2p-chip">{{ $post->categorie ?? '—' }}</span>
            </div>

            {{-- Quantité en haut à droite --}}
            <div class="position-absolute top-0 end-0 p-2">
              <span class="badge bg-success w2p-chip">{{ $post->quantite }} {{ $post->unite_mesure }}</span>
            </div>
          </div>

          <div class="p-3">
            <h5 class="fw-semibold mb-2">{{ $post->titre }}</h5>

            <p class="text-muted mb-3" style="min-height:48px;">
              {{ \Illuminate\Support\Str::limit($post->description, 120) }}
            </p>

            <div class="d-flex justify-content-between w2p-meta mb-3">
              <span><i class="bi bi-geo-alt"></i> {{ $post->localisation ?? '—' }}</span>
              <span class="text-capitalize">{{ $post->etat ?? '—' }}</span>
            </div>

            <div class="d-flex gap-2">
              <a class="btn btn-outline-success btn-sm" href="{{ route('front.waste-posts.show', $post) }}">Voir</a>
              @auth
                @if(auth()->id() === $post->user_id)
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('front.waste-posts.edit', $post) }}">Modifier</a>
                @endif
              @endauth
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p class="text-muted">Aucun post trouvé.</p></div>
    @endforelse
  </div>

  <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
</div>
@endsection

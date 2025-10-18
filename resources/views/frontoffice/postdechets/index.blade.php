@extends('frontoffice.layouts.layoutfront')

@php
    use Illuminate\Support\Facades\Storage;

    /**
     * URL d'image sûre pour un PostDechet.
     * - Priorité à $post->main_photo_url
     * - Sinon, première photo de $post->photos (array / json / string)
     * - Normalisation des chemins (public/, backslashes)
     * - Fallback placeholder
     */
    function photoUrlSafe($post) {
        if (!empty($post->main_photo_url)) {
            return $post->main_photo_url;
        }

        $first = null;
        if (!empty($post->photos)) {
            if (is_array($post->photos)) {
                $first = $post->photos[0] ?? null;
            } else {
                $decoded = json_decode($post->photos, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $first = $decoded[0] ?? null;
                } else {
                    $first = $post->photos; // simple string
                }
            }
        }

        if ($first) {
            $p = str_replace('\\', '/', $first);
            $p = preg_replace('#^public/#', '', $p);
            $p = ltrim($p, '/');

            if (Storage::disk('public')->exists($p)) {
                return Storage::disk('public')->url($p);
            }
            if (file_exists(public_path('storage/'.$p))) {
                return asset('storage/'.$p);
            }
        }

        return asset('images/placeholder.jpg');
    }
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

  .w2p-search{
    background:#fff;border-radius:14px;padding:14px 14px 8px;border:1px solid #eaecef;
    box-shadow:0 4px 14px rgba(0,0,0,.05);
  }
  .w2p-search .form-control, .w2p-search .form-select{
    border-radius:10px;
  }
</style>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title m-0">Posts déchets</h1>

    @auth
      <a href="{{ route('front.waste-posts.create') }}" class="btn btn-success">Nouveau post</a>
    @endauth
  </div>

  {{-- === Barre de RECHERCHE réduite : q, categorie, ville, etat === --}}
  <form method="GET" action="{{ route('front.waste-posts.index') }}" class="w2p-search mb-4">
    <div class="row g-2 align-items-end">
      <div class="col-12 col-md-4">
        <label class="form-label mb-1">Recherche</label>
        <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Titre / description…">
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label mb-1">Catégorie</label>
        @php $hasCategories = isset($categories) && count($categories); @endphp
        @if($hasCategories)
          <select name="categorie" class="form-select">
            <option value="">Toutes</option>
            @foreach($categories as $c)
              @php $label = is_string($c) ? $c : ($c->name ?? $c->titre ?? $c); @endphp
              <option value="{{ $label }}" @selected(request('categorie')==$label)>{{ $label }}</option>
            @endforeach
          </select>
        @else
          <input name="categorie" value="{{ request('categorie') }}" class="form-control" placeholder="ex: meubles">
        @endif
      </div>

      <div class="col-6 col-md-3">
        <label class="form-label mb-1">Ville</label>
        <input name="localisation" value="{{ request('localisation') }}" class="form-control" placeholder="ex: Tunis">
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label mb-1">État</label>
        <select name="etat" class="form-select">
          <option value="">Tous</option>
          @foreach(['neuf','tres_bon','bon','moyen','a_reparer'] as $e)
            <option value="{{ $e }}" @selected(request('etat')==$e)>{{ ucfirst(str_replace('_',' ', $e)) }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-2 d-grid">
        <button class="btn btn-success">Filtrer</button>
      </div>
      <div class="col-6 col-md-2 d-grid">
        <a class="btn btn-outline-secondary" href="{{ route('front.waste-posts.index') }}">Réinitialiser</a>
      </div>
    </div>

    {{-- Badges des filtres actifs (seulement ces 4-là) --}}
    @php
      $actifs = collect([
        'q'            => request('q'),
        'categorie'    => request('categorie'),
        'localisation' => request('localisation'),
        'etat'         => request('etat'),
      ])->filter();
    @endphp
    @if($actifs->isNotEmpty())
      <div class="mt-2">
        @foreach($actifs as $k => $v)
          <span class="badge rounded-pill text-bg-light border me-1">
            {{ $k }}: <strong>{{ $v }}</strong>
          </span>
        @endforeach
      </div>
    @endif
  </form>

  {{-- Compteur --}}
  <div class="mb-2 text-muted">
    {{ $posts->total() }} résultat{{ $posts->total() > 1 ? 's' : '' }}
  </div>

  {{-- === Grille des cartes === --}}
  <div class="row g-4">
    @forelse ($posts as $post)
      <div class="col-12 col-sm-6 col-lg-4">
        <div class="w2p-card h-100">
          <div class="position-relative">
            {{-- Image principale (sécurisée) --}}
            <img
              src="{{ photoUrlSafe($post) }}"
              alt="{{ $post->titre }}"
              class="w2p-thumb"
              loading="lazy"
              onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}';"
            >

            {{-- Catégorie en haut à gauche --}}
            <div class="position-absolute top-0 start-0 p-2">
              <span class="badge bg-light text-dark w2p-chip">
                {{ $post->categorie ?? '—' }}
              </span>
            </div>

            {{-- Quantité en haut à droite --}}
            <div class="position-absolute top-0 end-0 p-2">
              <span class="badge bg-success w2p-chip">
                {{ $post->quantite }} {{ $post->unite_mesure }}
              </span>
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

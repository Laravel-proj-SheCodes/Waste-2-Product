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

  /* Surbrillance 5s après clic de notif */
  .glow-target {
    animation: glowPulse 1s ease-in-out 5 alternate;
    box-shadow: 0 0 0 rgba(255, 193, 7, 0);
    background-color: transparent;
  }
  @keyframes glowPulse {
    0%   { box-shadow: 0 0 0 rgba(255, 193, 7, 0); background-color: transparent; }
    50%  { box-shadow: 0 0 18px rgba(255, 193, 7, .8); background-color: rgba(255, 243, 205, .6); }
    100% { box-shadow: 0 0 0 rgba(255, 193, 7, 0); background-color: transparent; }
  }
</style>

<div class="container py-4">
  <a href="{{ route('front.waste-posts.index') }}" class="text-success">&larr; Retour</a>

  {{-- Alertes (succès / info) --}}
  @if (session('ok'))
    <div class="alert alert-success mt-3">{{ session('ok') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger mt-3">{{ session('error') }}</div>
  @endif

  {{-- Alerte quand l’utilisateur vient d’une notification "acceptée" ou "refusée" --}}
  @if(request()->boolean('accepted'))
    <div class="alert alert-success mt-3">
      ✅ Félicitations ! Votre proposition a été <strong>acceptée</strong>.
    </div>
  @endif
  @if(request()->boolean('rejected'))
    <div class="alert alert-danger mt-3">
      ❌ Votre proposition a été <strong>refusée</strong>.
    </div>
  @endif

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
            {{-- Utilisateur connecté mais NON propriétaire : bouton Proposer (caché si déjà accepté ou déjà proposé) --}}
            @php
              $postDechet->loadMissing('propositions');
              $dejaAcceptee   = $postDechet->propositions->contains(fn($p) => $p->statut === 'accepte');
              $jAiDejaPropose = auth()->check()
                                   ? $postDechet->propositions->contains(fn($p) => $p->user_id === auth()->id())
                                   : false;
            @endphp

            @if (!$dejaAcceptee && !$jAiDejaPropose)
              @if (Route::has('front.propositions.create') && $postDechet->type !== 'troc')
                <a href="{{ route('front.propositions.create', $postDechet) }}"
                   class="btn btn-success w-100 mt-2">
                  Faire une proposition
                </a>
              @elseif (Route::has('offres-troc.create.front') && $postDechet->type === 'troc')
                <a href="{{ route('offres-troc.create.front', $postDechet) }}"
                   class="btn btn-success w-100 mt-2">
                  Faire une proposition de troc
                </a>
              @endif
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
      <div class="mt-4" id="propositions">
        <h3 class="h5 mb-3">Propositions reçues</h3>

        @if($received->isEmpty())
          <div class="alert alert-light border">Aucune proposition reçue pour le moment.</div>
        @else
          <div class="list-group">
            @foreach($received as $prop)
              <div id="proposition-{{ $prop->id }}" class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="me-3">
                    <div class="fw-semibold">
                      {{ $prop->user->name ?? 'Utilisateur' }}

                      @php
                        $badgeClass = $prop->statut === 'accepte' ? 'bg-success'
                                     : ($prop->statut === 'refusee' ? 'bg-danger' : 'bg-secondary');

                        $labelMap = [
                          'en_attente' => 'en attente',
                          'accepte'    => 'acceptée',
                          'refusee'    => 'refusée',
                        ];
                        $label = $labelMap[$prop->statut ?? 'en_attente'];
                      @endphp

                      <span class="badge ms-2 {{ $badgeClass }}">{{ $label }}</span>
                    </div>
                    <div class="text-muted small">
                      {{ $prop->created_at ? $prop->created_at->diffForHumans() : ($prop->date_proposition ? \Carbon\Carbon::parse($prop->date_proposition)->diffForHumans() : '') }}
                    </div>
                    <div class="mt-2">
                      {!! nl2br(e($prop->description)) !!}
                    </div>
                  </div>

                  {{-- Actions : accepter / refuser (si en attente) --}}
                  @if(($prop->statut ?? 'en_attente') === 'en_attente')
                    <div class="text-end">
                      <form action="{{ route('front.propositions.accept', $prop) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                      </form>
                      <form action="{{ route('front.propositions.reject', $prop) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Refuser</button>
                      </form>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endif
  @endauth
</div>

{{-- Surbrillance 5s + scroll automatique vers la proposition ciblée --}}
<script>
  (function () {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('highlight');
    if (!id) return;

    const el = document.getElementById('proposition-' + id);
    if (!el) return;

    // ajouter l'effet
    el.classList.add('glow-target');

    // scroll dans la page jusqu'à l’élément
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // retirer la classe après ~5 secondes
    setTimeout(() => el.classList.remove('glow-target'), 5200);
  })();
</script>
@endsection

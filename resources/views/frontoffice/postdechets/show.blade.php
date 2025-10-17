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

  /* Chat bulles */
  #chatBox .bubble { display:inline-block; padding:.4rem .6rem; border-radius:.5rem; max-width:80%; word-break:break-word; }
  #chatBox .bubble.me { background:#198754; color:#fff; }
  #chatBox .bubble.other { background:#f5f5f5; color:#222; }
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

  @php
    // ===== Détermination du droit d’afficher le bouton "💬 Chat" =====
    $postDechet->loadMissing('propositions');
    $acceptedStatuses = ['accepte','accepted','acceptee','acceptée'];
    $userId   = auth()->id();
    $isOwner  = $userId && ($postDechet->user_id === $userId);

    // Le propriétaire voit le chat si au moins UNE proposition est acceptée
    $acceptedForOwner = $isOwner
      ? $postDechet->propositions->first(function($p) use($acceptedStatuses){
          return in_array(strtolower((string)($p->statut ?? '')), $acceptedStatuses, true);
        })
      : null;

    // Le client voit le chat si SA proposition est acceptée
    $acceptedForMe = (!$isOwner && $userId)
      ? $postDechet->propositions->first(function($p) use($userId, $acceptedStatuses){
          return (int)$p->user_id === (int)$userId
              && in_array(strtolower((string)($p->statut ?? '')), $acceptedStatuses, true);
        })
      : null;

    $acceptedProp = $acceptedForOwner ?: $acceptedForMe;
  @endphp

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
              $dejaAcceptee   = $postDechet->propositions->contains(fn($p) => strtolower((string)$p->statut) === 'accepte');
              $jAiDejaPropose = auth()->check()
                                   ? $postDechet->propositions->contains(fn($p) => (int)$p->user_id === (int)auth()->id())
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

          {{-- ✅ Bouton Chat pour propriétaire OU client si proposition acceptée --}}
          @if($acceptedProp)
            <button type="button"
                    class="btn btn-success w-100 mt-3 open-chat"
                    data-proposition-id="{{ $acceptedProp->id }}">
              💬 Chat
            </button>
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
                        $badgeClass = ($prop->statut === 'accepte' || strtolower((string)$prop->statut) === 'accepted') ? 'bg-success'
                                     : (( $prop->statut === 'refuse') ? 'bg-danger' : 'bg-secondary');

                        $labelMap = [
                          'en_attente' => 'en attente',
                          'accepte'    => 'acceptée',
                          'accepted'   => 'acceptée',
                          'acceptee'   => 'acceptée',
                          'acceptée'   => 'acceptée',
                          'refuse'     => 'refusée',
                        ];
                        $label = $labelMap[strtolower((string)($prop->statut ?? 'en_attente'))] ?? ($prop->statut ?? '—');
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

                  <div class="text-end">
                    {{-- Actions : accepter / refuser (si en attente) --}}
                    @if(($prop->statut ?? 'en_attente') === 'en_attente')
                      <form action="{{ route('front.propositions.accept', $prop) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Accepter</button>
                      </form>
                      <form action="{{ route('front.propositions.reject', $prop) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Refuser</button>
                      </form>
                    @endif

                    {{-- ✅ Bouton Chat (tu l’as déjà) --}}
@if($acceptedProp)
  <button type="button"
          class="btn btn-success w-100 mt-3 open-chat"
          data-proposition-id="{{ $acceptedProp->id }}">
    💬 Chat
  </button>
@endif
                  </div>
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

    el.classList.add('glow-target');
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(() => el.classList.remove('glow-target'), 5200);
  })();
</script>

{{-- ========= Modal de chat réutilisable ========= --}}
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="chatBox" class="border rounded p-2" style="height:360px; overflow:auto;"></div>
      </div>
      <div class="modal-footer">
        <input id="chatInput" class="form-control" placeholder="Écrire un message…">
        <button id="chatSend" class="btn btn-success">Envoyer</button>
      </div>
    </div>
  </div>
</div>

{{-- ========= Logique du chat : open + polling + send ========= --}}
<script>
(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let chat = { convId:null, lastId:0, poll:null };

  async function openChat(propositionId){
    try{
      const r = await fetch(`/chat/open/${propositionId}`, {
        method:'POST',
        headers:{ 'X-CSRF-TOKEN': csrf }
      });
      const data = await r.json();
      if(!data.ok){ alert('Impossible d’ouvrir le chat.'); return; }

      chat.convId = data.conversation.id;
      chat.lastId = 0;
      document.querySelector('#chatModal .modal-title').textContent =
        `Chat – ${data.conversation.title} (avec ${data.conversation.with})`;
      document.getElementById('chatBox').innerHTML = '';

      await loadMessages();
      new bootstrap.Modal(document.getElementById('chatModal')).show();
      startPolling();
    }catch(e){
      console.error(e); alert('Erreur réseau à l’ouverture du chat.');
    }
  }

  async function loadMessages(){
    if(!chat.convId) return;
    try{
      const r = await fetch(`/chat/${chat.convId}/messages?after=${chat.lastId}`);
      const data = await r.json(); if(!data.ok) return;

      const box = document.getElementById('chatBox');
      data.messages.forEach(m=>{
        chat.lastId = Math.max(chat.lastId, m.id);
        const row = document.createElement('div');
        row.className = (m.me ? 'text-end' : 'text-start') + ' my-1';
        row.innerHTML = `<span class="bubble ${m.me?'me':'other'}">${m.body}</span>`;
        box.appendChild(row);
      });
      box.scrollTop = box.scrollHeight;
    }catch(e){ console.error(e); }
  }

  function startPolling(){ stopPolling(); chat.poll = setInterval(loadMessages, 3000); }
  function stopPolling(){ if(chat.poll){ clearInterval(chat.poll); chat.poll=null; } }

  async function sendMessage(){
    if(!chat.convId) return;
    const input = document.getElementById('chatInput');
    const body  = (input.value || '').trim(); if(!body) return;

    try{
      const r = await fetch(`/chat/${chat.convId}/messages`, {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ body })
      });
      const data = await r.json();
      if(data.ok){
        input.value='';
        chat.lastId = Math.max(chat.lastId, data.message.id);
        const box = document.getElementById('chatBox');
        const row = document.createElement('div');
        row.className = 'text-end my-1';
        row.innerHTML = `<span class="bubble me">${data.message.body}</span>`;
        box.appendChild(row);
        box.scrollTop = box.scrollHeight;
      }
    }catch(e){ console.error(e); alert('Envoi impossible.'); }
  }

  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.open-chat');
    if(btn){ openChat(btn.dataset.propositionId); }
  });
  document.getElementById('chatSend')?.addEventListener('click', sendMessage);
  document.getElementById('chatInput')?.addEventListener('keydown', (e)=>{
    if(e.key==='Enter'){ e.preventDefault(); sendMessage(); }
  });
  document.getElementById('chatModal')?.addEventListener('hidden.bs.modal', stopPolling);
})();
</script>
@endsection

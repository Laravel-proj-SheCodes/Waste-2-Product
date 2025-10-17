{{-- resources/views/frontoffice/postdechets/partials/chat.blade.php --}}
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">

      {{-- HEADER --}}
      <div class="modal-header bg-success text-white">
        <div class="d-flex align-items-center gap-3">
          <img id="chatOtherAvatar" src="{{ asset('images/avatar.png') }}" alt="" width="40" height="40"
               class="rounded-circle border border-white border-2 object-fit-cover">
          <div class="d-flex flex-column">
            <h5 class="modal-title m-0" id="chatModalLabel">Chat</h5>
            <small class="opacity-75" id="chatSubTitle">…</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      {{-- BODY --}}
      <div class="modal-body p-0 d-flex flex-column" style="height: 540px;">
        <div id="chatTopBar" class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom small">
          <button id="chatLoadOlder" class="btn btn-sm btn-outline-secondary">
            Charger les messages plus anciens
          </button>
          <span id="chatStatus" class="text-muted">En ligne</span>
        </div>

        <div id="chatBox" class="flex-grow-1 overflow-auto p-3 bg-light" style="scroll-behavior:smooth">
          {{-- Messages injectés ici --}}
        </div>

        <div class="border-top px-3 py-2 d-flex align-items-end gap-2">
          <textarea id="chatInput" class="form-control" rows="1" placeholder="Écrire un message…"
                    style="resize:none; border-radius:14px; padding:10px 12px;"></textarea>
          <button id="chatSend" class="btn btn-success px-3" disabled>
            <i class="bi bi-send"></i> Envoyer
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- STYLES --}}
<style>
  #chatBox .msg-wrap{margin:.25rem 0; display:flex; flex-direction:column; max-width:80%}
  #chatBox .msg.me{align-self:flex-end; text-align:right}
  #chatBox .bubble{display:inline-block; padding:.55rem .75rem; border-radius:14px; word-break:break-word}
  #chatBox .bubble.me{background:#198754; color:#fff; border-bottom-right-radius:4px}
  #chatBox .bubble.other{background:#eef1f3; color:#1f2937; border-bottom-left-radius:4px}
  #chatBox .meta{font-size:.75rem; color:#64748b; margin-top:.15rem}
  #chatScrollDown{position:absolute; right:18px; bottom:86px; z-index:3}
</style>

{{-- JS (autonome) --}}
<script>
(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  let chat = {
    convId: null,
    lastId: 0,
    oldestId: null,
    poll: null,
    modal: null,
  };

  // helpers
  const el = s => document.querySelector(s);
  const box = () => el('#chatBox');

  const esc = (t) => {
    const d = document.createElement('div'); d.textContent = t; return d.innerHTML;
  };

  function render(messages, mode='append'){
    if(!messages?.length) return;
    const frag = document.createDocumentFragment();
    messages.forEach(m => {
      chat.lastId   = Math.max(chat.lastId || 0, m.id || 0);
      chat.oldestId = chat.oldestId ? Math.min(chat.oldestId, m.id) : m.id;

      const wrap = document.createElement('div');
      wrap.className = `msg-wrap ${m.me ? 'msg me' : 'msg other'}`;
      wrap.innerHTML = `
        <span class="bubble ${m.me ? 'me' : 'other'}">${esc(m.body)}</span>
        <span class="meta">${m.user} • ${m.at}</span>
      `;
      frag.appendChild(wrap);
    });

    if(mode === 'prepend'){
      const prevH = box().scrollHeight;
      box().insertBefore(frag, box().firstChild);
      // conserver la position (on reste là où on était)
      box().scrollTop = box().scrollHeight - prevH;
    } else {
      box().appendChild(frag);
      box().scrollTop = box().scrollHeight;
    }
  }

  async function api(url, options = {}){
    const r = await fetch(url, options);
    return r.json();
  }

  async function openChat(propositionId){
    try {
      const data = await api(`/chat/open/${propositionId}`, { method:'POST', headers:{'X-CSRF-TOKEN':csrf}});
      if(!data.ok) { alert('Impossible d’ouvrir le chat.'); return; }

      chat.convId = data.conversation.id;
      chat.lastId = 0; chat.oldestId = null;

      // header
      el('#chatModalLabel').textContent = `Chat – ${data.conversation.title}`;
      el('#chatSubTitle').textContent   = `avec ${data.conversation.with}`;
      // si tu as un champ avatar côté back, tu peux mettre: el('#chatOtherAvatar').src = data.conversation.avatar;

      box().innerHTML = '';
      await loadAll();                                 // charge tout au 1er affichage
      chat.modal = new bootstrap.Modal(el('#chatModal'));
      chat.modal.show();
      startPolling();
    } catch (e) { console.error(e); alert('Erreur réseau.'); }
  }

  async function loadNew(){
    if(!chat.convId) return;
    const data = await api(`/chat/${chat.convId}/messages?after=${chat.lastId}`);
    if(data.ok) render(data.messages, 'append');
  }

  async function loadOlder(){
    if(!chat.convId || !chat.oldestId) return;
    const data = await api(`/chat/${chat.convId}/messages?before=${chat.oldestId}`);
    if(data.ok) render(data.messages, 'prepend');
  }

  async function loadAll(){
    const data = await api(`/chat/${chat.convId}/messages?all=1`);
    if(data.ok) render(data.messages, 'append');
  }

  function startPolling(){ stopPolling(); chat.poll = setInterval(loadNew, 3000); }
  function stopPolling(){ if(chat.poll) { clearInterval(chat.poll); chat.poll=null; } }

  async function sendMessage(){
    const t = el('#chatInput');
    const body = (t.value || '').trim();
    if(!body || !chat.convId) return;

    el('#chatSend').disabled = true;
    try {
      const data = await api(`/chat/${chat.convId}/messages`, {
        method:'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
        body: JSON.stringify({ body })
      });
      if(data.ok){
        t.value = ''; autosize(t);                      // reset textarea
        render([data.message], 'append');
      }
    } finally {
      el('#chatSend').disabled = false;
      t.focus();
    }
  }

  // autosize textarea
  function autosize(ta){
    ta.style.height = 'auto';
    ta.style.height = Math.min(120, ta.scrollHeight) + 'px';
    el('#chatSend').disabled = !ta.value.trim();
  }

  // ============ bindings ============
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.open-chat');
    if(btn){ openChat(btn.dataset.propositionId); }
  });

  el('#chatLoadOlder')?.addEventListener('click', loadOlder);
  el('#chatSend')?.addEventListener('click', sendMessage);

  el('#chatInput')?.addEventListener('input', (e)=> autosize(e.target));
  el('#chatInput')?.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
  });

  el('#chatModal')?.addEventListener('hidden.bs.modal', stopPolling);
})();
</script>

@include('frontoffice.postdechets.partials.chat')
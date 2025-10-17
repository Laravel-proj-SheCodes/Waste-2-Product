<!-- resources/views/frontoffice/postdechets/partials/chat.blade.php -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="chatModalLabel">
          💬 Chat – {{ $conversation->post?->titre ?? 'Conversation' }} (avec {{ $otherUser->name ?? 'Utilisateur' }})
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body d-flex flex-column" style="height: 500px;">
        <div id="chatBox" class="flex-grow-1 overflow-auto p-3 bg-light border rounded" style="scroll-behavior: smooth;">
          <!-- Messages chargés dynamiquement -->
        </div>

        <div class="mt-3 d-flex">
          <textarea id="chatInput" class="form-control me-2" rows="1" placeholder="Écrire un message..."
            style="resize:none; border-radius: 20px; padding: 10px 15px;"></textarea>
          <button id="chatSend" class="btn btn-success rounded-circle px-3 py-2">
            <i class="bi bi-send"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- STYLES --}}
<style>
#chatBox {
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  box-shadow: inset 0 0 4px rgba(0,0,0,0.05);
}

#chatBox .bubble {
  display: inline-block;
  padding: 10px 14px;
  border-radius: 20px;
  margin: 4px 0;
  max-width: 75%;
  word-wrap: break-word;
  font-size: 15px;
  line-height: 1.3;
}

#chatBox .me {
  background-color: #198754;
  color: white;
  border-bottom-right-radius: 4px;
  margin-left: auto;
}

#chatBox .other {
  background-color: #e9ecef;
  color: #212529;
  border-bottom-left-radius: 4px;
  margin-right: auto;
}

.message-wrapper {
  display: flex;
  flex-direction: column;
}

.message-time {
  font-size: 0.75rem;
  color: #6c757d;
  margin-top: 2px;
  text-align: right;
}

#chatInput {
  height: 44px;
  overflow-y: auto;
}

#chatSend {
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

{{-- SCRIPT --}}
<script>
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function renderMessages(msgs, mode){
  const box = document.getElementById('chatBox');
  const frag = document.createDocumentFragment();
  msgs.forEach(m=>{
    const wrap = document.createElement('div');
    wrap.className = 'message-wrapper ' + (m.me ? 'text-end' : 'text-start');
    wrap.innerHTML = `
      <div class="bubble ${m.me ? 'me' : 'other'}">${escapeHtml(m.body)}</div>
      <div class="message-time">${m.at}</div>
    `;
    frag.appendChild(wrap);
  });
  if(mode === 'prepend'){
    box.insertBefore(frag, box.firstChild);
  } else {
    box.appendChild(frag);
  }
  box.scrollTop = box.scrollHeight;
}

document.addEventListener('DOMContentLoaded', () => {
  const input = document.getElementById('chatInput');
  const sendBtn = document.getElementById('chatSend');
  const box = document.getElementById('chatBox');

  // Envoyer avec Enter
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendBtn.click();
    }
  });

  sendBtn.addEventListener('click', () => {
    const msg = input.value.trim();
    if (!msg) return;
    input.value = '';

    fetch(`/chat/{{ $conversation->id }}/send`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ body: msg })
    })
    .then(r => r.json())
    .then(data => {
      if (data.ok) renderMessages([data.message], 'append');
    });
  });

  // Charger tous les anciens messages
  fetch(`/chat/{{ $conversation->id }}/messages?all=1`)
    .then(r => r.json())
    .then(data => {
      if (data.ok) renderMessages(data.messages, 'append');
    });
});
</script>

<!-- Lanceur quand le chat est fermé -->
<button
  x-data
  x-show="$store?.ecoBot?.open === false"
  @click="$store.ecoBot.open = true"
  class="position-fixed bottom-0 end-0 m-3 btn btn-success rounded-circle shadow"
  style="width:48px;height:48px;z-index:1050"
  title="Ouvrir ÉcoBot"
>🌿</button>

<div
  x-data="ecoBot()"
  x-init="$store.ecoBot = $data"
  x-show="open"
  x-transition
  class="position-fixed bottom-0 end-0 m-3 bg-white border border-success rounded shadow"
  style="width:340px; z-index:1050;"
>
  <!-- Header -->
  <div class="bg-success text-white p-2 fw-semibold d-flex align-items-center justify-content-between">
    <span>🌿 ÉcoBot</span>
    <div class="d-flex gap-2">
      <!-- Réduire -->
      <button class="btn btn-sm btn-light px-2 py-0" @click.stop="collapsed = !collapsed" title="Réduire">–</button>
      <!-- Fermer -->
      <button class="btn btn-sm btn-light px-2 py-0" @click.stop="open=false" title="Fermer">×</button>
    </div>
  </div>

  <!-- Corps -->
  <div x-show="!collapsed" x-transition>
    <div class="position-relative">
      <!-- zone messages -->
      <div class="p-2 overflow-auto" x-ref="box" style="max-height:60vh;">
        <template x-for="m in msgs" :key="m.id">
          <div :class="m.role === 'user' ? 'text-end' : 'text-start'">
            <span class="d-inline-block rounded px-2 py-1 my-1"
                  :class="m.role === 'user' ? 'bg-success text-white' : 'bg-light'">
              <span x-text="m.content"></span>
            </span>
          </div>
        </template>
        <div x-show="typing" class="text-muted small fst-italic">L’assistant écrit…</div>
      </div>

      <!-- Boutons scroll haut/bas -->
      <button
        class="btn btn-light btn-sm shadow position-absolute end-0 me-2 mt-2"
        style="top:0; opacity:.9"
        @click="scrollTop()"
        title="Haut"
      >↑</button>
      <button
        class="btn btn-light btn-sm shadow position-absolute end-0 me-2 mb-2"
        style="bottom:0; opacity:.9"
        @click="scrollBottom()"
        title="Bas"
      >↓</button>
    </div>

    <div class="p-2 border-top">
      <input class="form-control form-control-sm" type="text" placeholder="Posez votre question…"
             x-model="input" @keydown.enter.prevent="send()">
    </div>
  </div>
</div>

<script>
function ecoBot() {
  return {
    // affichage
    open: true,
    collapsed: false,

    // data
    input: '',
    typing: false,
    msgs: [
      {id: 1, role: 'assistant', content: "Bonjour 🌱 ! Je suis ÉcoBot, votre conseiller pour le tri et le recyclage. Posez-moi vos questions !"}
    ],

    // scroll helpers
    scrollTop() { this.$refs.box.scrollTo({ top: 0, behavior: 'smooth' }); },
    scrollBottom() { this.$refs.box.scrollTo({ top: this.$refs.box.scrollHeight, behavior: 'smooth' }); },

    async send() {
      const text = this.input.trim();
      if (!text) return;
      this.msgs.push({id: Date.now(), role: 'user', content: text});
      this.input = '';
      this.typing = true;

      try {
        const res = await fetch('{{ url('/eco-bot') }}', {
          method: 'POST',
          headers: {
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          },
          body: JSON.stringify({ message: text })
        });
        const data = await res.json();
        this.msgs.push({
          id: Date.now() + 1,
          role: 'assistant',
          content: data.reply || "Désolé, je n’ai pas pu répondre."
        });
      } catch (e) {
        this.msgs.push({id: Date.now() + 2, role: 'assistant', content: "Erreur réseau. Réessaie plus tard."});
      } finally {
        this.typing = false;
        this.$nextTick(() => this.scrollBottom());
      }
    }
  }
}
</script>


{{-- AlpineJS --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

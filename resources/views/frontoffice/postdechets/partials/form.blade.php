@php
    use Illuminate\Support\Facades\Storage;
    $p = $post ?? null;

    // Photos existantes (pour l'édition)
    $existing = [];
    if ($p && is_array($p->photos)) {
        foreach ($p->photos as $ph) {
            $fp = ltrim(str_replace('\\', '/', $ph), '/'); // normalise Windows
            if (Storage::disk('public')->exists($fp)) {
                $existing[] = Storage::disk('public')->url($fp);     // /storage/...
            } elseif (file_exists(public_path('storage/'.$fp))) {
                $existing[] = asset('storage/'.$fp);                 // fallback direct
            }
        }
    }
@endphp

{{-- Titre --}}
<div class="col-md-6">
    <label class="form-label">Titre <span class="text-danger">*</span></label>
    <input name="titre" value="{{ old('titre', $p->titre ?? '') }}" class="form-control" required>
    @error('titre') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Type de post --}}
  <div class="col-lg-6 mb-3">
    <label for="type_post" class="form-label">Type <span class="text-danger">*</span></label>
    <select id="type_post" name="type_post"
            class="form-select @error('type_post') is-invalid @enderror" required>
      @foreach(['don','troc','vente','transformation'] as $opt)
        <option value="{{ $opt }}" @selected(old('type_post',$p->type_post ?? '')==$opt)>
          {{ ucfirst($opt) }}
        </option>
      @endforeach
    </select>
    @error('type_post') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

{{-- Catégorie --}}
<div class="col-md-6">
    <label class="form-label">Catégorie <span class="text-danger">*</span></label>
    <input name="categorie" value="{{ old('categorie', $p->categorie ?? '') }}" class="form-control" required>
    @error('categorie') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Quantité --}}
<div class="col-md-3">
    <label class="form-label">Quantité <span class="text-danger">*</span></label>
    <input type="number" step="0.01" min="0" name="quantite"
           value="{{ old('quantite', $p->quantite ?? '') }}" class="form-control" required>
    @error('quantite') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Unité --}}
<div class="col-md-3">
    <label class="form-label">Unité <span class="text-danger">*</span></label>
    <input name="unite_mesure" value="{{ old('unite_mesure', $p->unite_mesure ?? '') }}" class="form-control"
           placeholder="kg, L, pièce..." required>
    @error('unite_mesure') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- État --}}
<div class="col-md-6">
    <label class="form-label">État <span class="text-danger">*</span></label>
    @php $etat = old('etat', $p->etat ?? ''); @endphp
    <select name="etat" class="form-select" required>
        <option value="">-- choisir --</option>
        <option value="neuf"      {{ $etat==='neuf' ? 'selected' : '' }}>Neuf</option>
        <option value="bon_etat"  {{ $etat==='bon_etat' ? 'selected' : '' }}>Bon état</option>
        <option value="usage"     {{ $etat==='usage' ? 'selected' : '' }}>Usagé</option>
        <option value="a_reparer" {{ $etat==='a_reparer' ? 'selected' : '' }}>À réparer</option>
    </select>
    @error('etat') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Localisation --}}
<div class="col-md-6">
    <label class="form-label">Localisation <span class="text-danger">*</span></label>
    <input name="localisation" value="{{ old('localisation', $p->localisation ?? '') }}" class="form-control" required>
    @error('localisation') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Description --}}
<div class="col-12">
    <label class="form-label">Description <span class="text-danger">*</span></label>
    <textarea name="description" rows="4" class="form-control" required>{{ old('description', $p->description ?? '') }}</textarea>
    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Photos --}}
<div class="col-md-6">
    <label class="form-label">Photos (multiples)</label>
    <input type="file" name="photos[]" class="form-control" multiple accept="image/*">
    @error('photos') <small class="text-danger d-block">{{ $message }}</small> @enderror
    @error('photos.*') <small class="text-danger d-block">{{ $message }}</small> @enderror

    @if(count($existing))
        <div class="form-text mt-2">Photos actuelles (elles seront <strong>remplacées</strong> si vous en importez de nouvelles) :</div>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @foreach($existing as $url)
                <img src="{{ $url }}" alt="" style="width:88px;height:88px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
            @endforeach
        </div>
    @endif
</div>
<!-- mouna's job for troc part-->
 {{-- Analyse IA pour les posts de type troc --}}
<div class="col-12" id="analyze-section" style="display:none;">
  <label class="form-label fw-bold text-success">Analyse de l’image (optionnelle)</label>
  <input type="file" id="analyze-photo" class="form-control mb-2" accept="image/*">
  <button type="button" id="analyze-btn" class="btn btn-outline-success">Analyser l’image</button>
  <div id="analyze-result" class="mt-3 text-info"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const typeSelect = document.querySelector('#type_post');
  const section = document.querySelector('#analyze-section');
  const btn = document.querySelector('#analyze-btn');
  const input = document.querySelector('#analyze-photo');
  const result = document.querySelector('#analyze-result');

  // 🟢 Affiche la section seulement si type = troc
  typeSelect.addEventListener('change', () => {
    section.style.display = (typeSelect.value === 'troc') ? 'block' : 'none';
  });

  // 🧠 Analyse l’image via API Laravel
  btn.addEventListener('click', async () => {
    if (!input.files[0]) {
      result.innerHTML = '<span class="text-danger">Choisissez une image d’abord.</span>';
      return;
    }

    const formData = new FormData();
    formData.append('photo', input.files[0]);

    result.innerHTML = 'Analyse en cours...';

    const res = await fetch("{{ route('front.waste-posts.analyze') }}", {
      method: "POST",
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
      body: formData
    });

    const data = await res.json();
    result.innerHTML = data.label 
      ? `<strong>Résultat :</strong> ${data.message}`
      : `<span class="text-danger">${data.error ?? 'Erreur lors de l’analyse'}</span>`;
  });
});
</script>
<!--end mouna's job -->

{{-- Statut --}}
<div class="col-md-6">
    <label class="form-label">Statut <span class="text-danger">*</span></label>
    @php $statut = old('statut', $p->statut ?? 'en_attente'); @endphp
    <select name="statut" class="form-select" required>
        <option value="en_attente" {{ $statut==='en_attente' ? 'selected' : '' }}>En attente</option>
        <option value="publie"     {{ $statut==='publie' ? 'selected' : '' }}>Publié</option>
        <option value="archive"    {{ $statut==='archive' ? 'selected' : '' }}>Archivé</option>
    </select>
    @error('statut') <small class="text-danger">{{ $message }}</small> @enderror
</div>
<!--mouna's job-->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const typeSelect = document.querySelector('#type_post');
  const section = document.querySelector('#analyze-section');
  const btn = document.querySelector('#analyze-btn');
  const input = document.querySelector('#analyze-photo');
  const result = document.querySelector('#analyze-result');

  // Affiche la section seulement si type = troc
  typeSelect.addEventListener('change', () => {
    section.style.display = (typeSelect.value === 'troc') ? 'block' : 'none';
  });

  // Analyse l’image via API Laravel
btn.addEventListener('click', async () => {
    if (!input.files[0]) {
        result.innerHTML = '<span class="text-danger">Choisissez une image d’abord.</span>';
        return;
    }

    const formData = new FormData();
    formData.append('photo', input.files[0]);
    result.innerHTML = 'Analyse en cours...';

    try {
        const res = await fetch("{{ route('front.waste-posts.analyze') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            result.innerHTML = `<strong>Analyse terminée.</strong>`;

            // Remplissage automatique
            document.querySelector('input[name="titre"]').value = data.titre;
            document.querySelector('input[name="categorie"]').value = data.categorie;
            document.querySelector('textarea[name="description"]').value = data.description;
            document.querySelector('select[name="etat"]').value = data.etat;
            document.querySelector('input[name="quantite"]').value = data.quantite;
            document.querySelector('input[name="unite_mesure"]').value = data.unite_mesure;

        } else {
            result.innerHTML = `<span class="text-danger">${data.error ?? 'Erreur lors de l’analyse'}</span>`;
        }

    } catch (e) {
        result.innerHTML = `<span class="text-danger">Erreur réseau ou serveur : ${e.message}</span>`;
    }
});

});
</script>

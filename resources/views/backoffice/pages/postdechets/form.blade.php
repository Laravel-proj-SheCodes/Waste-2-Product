@php($p = $postdechet ?? null)

{{-- Alerte erreurs globales --}}
@if($errors->any())
  <div class="alert alert-danger">
    Veuillez corriger les champs en rouge.
  </div>
@endif

<style>
  /* Labels homogènes et lisibles */
  .form-label {
    display: block;
    font-weight: 600;
    color: #212529 !important; /* noir Bootstrap */
    margin-bottom: 0.4rem;
  }

  /* Inputs et selects uniformes */
  .form-control,
  .form-select {
    height: 44px;
    border: 1px solid #ced4da;
    border-radius: 0.5rem;
    background: #fff;
  }

  .form-control:focus,
  .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
  }

  /* Textarea un peu plus grand */
  textarea.form-control {
    min-height: 110px;
    height: auto;
    padding-top: .6rem;
  }

  /* Espacements homogènes */
  .mb-3 { margin-bottom: 1rem !important; }

  /* Input file custom */
  .file-input-hidden { display: none; }
  .btn-file {
    display: inline-block;
    border: 1px solid #ced4da;
    background: #fff;
    border-radius: 0.5rem;
    padding: .6rem 1rem;
    cursor: pointer;
  }
  .btn-file:hover {
    background: #f8f9fa;
  }
</style>

<div class="row g-4">

  <div class="col-lg-6 mb-3">
    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
    <input id="titre" type="text" name="titre"
           class="form-control @error('titre') is-invalid @enderror"
           value="{{ old('titre', $p->titre ?? '') }}" required>
    @error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

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

  <div class="col-lg-6 mb-3">
    <label for="categorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
    <input id="categorie" type="text" name="categorie"
           class="form-control @error('categorie') is-invalid @enderror"
           value="{{ old('categorie', $p->categorie ?? '') }}" required>
    @error('categorie') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-lg-3 col-md-6 mb-3">
    <label for="quantite" class="form-label">Quantité <span class="text-danger">*</span></label>
    <input id="quantite" type="number" step="0.01" name="quantite"
           class="form-control @error('quantite') is-invalid @enderror"
           value="{{ old('quantite', $p->quantite ?? '') }}" required>
    @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-lg-3 col-md-6 mb-3">
    <label for="unite_mesure" class="form-label">Unité <span class="text-danger">*</span></label>
    <input id="unite_mesure" type="text" name="unite_mesure"
           class="form-control @error('unite_mesure') is-invalid @enderror"
           placeholder="kg, L, pièce…"
           value="{{ old('unite_mesure', $p->unite_mesure ?? '') }}" required>
    @error('unite_mesure') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-lg-4 mb-3">
    <label for="etat" class="form-label">État <span class="text-danger">*</span></label>
    <select id="etat" name="etat"
            class="form-select @error('etat') is-invalid @enderror" required>
      @foreach(['neuf','usagé','dégradé'] as $opt)
        <option value="{{ $opt }}" @selected(old('etat',$p->etat ?? '')==$opt)>
          {{ ucfirst($opt) }}
        </option>
      @endforeach
    </select>
    @error('etat') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-lg-8 mb-3">
    <label for="localisation" class="form-label">Localisation <span class="text-danger">*</span></label>
    <input id="localisation" type="text" name="localisation"
           class="form-control @error('localisation') is-invalid @enderror"
           value="{{ old('localisation', $p->localisation ?? '') }}" required>
    @error('localisation') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 mb-3">
    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
    <textarea id="description" name="description"
              class="form-control @error('description') is-invalid @enderror"
              rows="4" required>{{ old('description', $p->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Upload photos --}}
  <div class="col-lg-6 mb-3">
    <label class="form-label d-block">Photos (multiples)</label>
    <input id="photos" name="photos[]" type="file" multiple class="file-input-hidden">
    <label for="photos" class="btn-file">Choisir un fichier</label>
    @error('photos') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
  </div>

  <div class="col-lg-6 mb-3">
    <label for="statut" class="form-label">Statut</label>
    <select id="statut" name="statut" class="form-select">
      @foreach(['en_attente','en_cours','terminé'] as $opt)
        <option value="{{ $opt }}" @selected(old('statut',$p->statut ?? 'en_attente')==$opt)>
          {{ str_replace('_',' ', ucfirst($opt)) }}
        </option>
      @endforeach
    </select>
  </div>
</div>

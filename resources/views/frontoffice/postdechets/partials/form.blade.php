@php $p = $post ?? null; @endphp

{{-- Titre --}}
<div class="col-md-6">
    <label class="form-label">Titre *</label>
    <input name="titre" value="{{ old('titre', $p->titre ?? '') }}" class="form-control" required>
    @error('titre') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Type de post --}}
<div class="col-md-6">
    <label class="form-label">Type *</label>
    <select name="type_post" class="form-select" required>
        @php
            $type = old('type_post', $p->type_post ?? '');
        @endphp
        <option value="">-- choisir --</option>
        <option value="don"     {{ $type==='don' ? 'selected' : '' }}>Don</option>
        <option value="echange" {{ $type==='echange' ? 'selected' : '' }}>Échange</option>
        <option value="vente"   {{ $type==='vente' ? 'selected' : '' }}>Vente</option>
    </select>
    @error('type_post') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Catégorie --}}
<div class="col-md-6">
    <label class="form-label">Catégorie *</label>
    <input name="categorie" value="{{ old('categorie', $p->categorie ?? '') }}" class="form-control" required>
    @error('categorie') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Quantité --}}
<div class="col-md-3">
    <label class="form-label">Quantité *</label>
    <input type="number" step="0.01" name="quantite" value="{{ old('quantite', $p->quantite ?? '') }}" class="form-control" required>
    @error('quantite') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Unité --}}
<div class="col-md-3">
    <label class="form-label">Unité *</label>
    <input name="unite_mesure" value="{{ old('unite_mesure', $p->unite_mesure ?? '') }}" class="form-control" placeholder="kg, L, pièce..." required>
    @error('unite_mesure') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- État --}}
<div class="col-md-6">
    <label class="form-label">État *</label>
    @php $etat = old('etat', $p->etat ?? ''); @endphp
    <select name="etat" class="form-select" required>
        <option value="">-- choisir --</option>
        <option value="neuf"         {{ $etat==='neuf' ? 'selected' : '' }}>Neuf</option>
        <option value="bon_etat"     {{ $etat==='bon_etat' ? 'selected' : '' }}>Bon état</option>
        <option value="usage"        {{ $etat==='usage' ? 'selected' : '' }}>Usagé</option>
        <option value="a_reparer"    {{ $etat==='a_reparer' ? 'selected' : '' }}>À réparer</option>
    </select>
    @error('etat') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Localisation --}}
<div class="col-md-6">
    <label class="form-label">Localisation *</label>
    <input name="localisation" value="{{ old('localisation', $p->localisation ?? '') }}" class="form-control" required>
    @error('localisation') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Description --}}
<div class="col-12">
    <label class="form-label">Description *</label>
    <textarea name="description" rows="4" class="form-control" required>{{ old('description', $p->description ?? '') }}</textarea>
    @error('description') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Photos --}}
<div class="col-md-6">
    <label class="form-label">Photos (multiples)</label>
    <input type="file" name="photos[]" class="form-control" multiple>
    @if($p && is_array($p->photos) && count($p->photos))
        <small class="text-muted">Des photos existent — importer pour remplacer.</small>
    @endif
    @error('photos') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Statut --}}
<div class="col-md-6">
    <label class="form-label">Statut *</label>
    @php $statut = old('statut', $p->statut ?? 'en_attente'); @endphp
    <select name="statut" class="form-select" required>
        <option value="en_attente" {{ $statut==='en_attente' ? 'selected' : '' }}>En attente</option>
        <option value="publie"     {{ $statut==='publie' ? 'selected' : '' }}>Publié</option>
        <option value="archive"    {{ $statut==='archive' ? 'selected' : '' }}>Archivé</option>
    </select>
    @error('statut') <small class="text-danger">{{ $message }}</small> @enderror
</div>

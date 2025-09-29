@php($p = $processus ?? null)

@if($errors->any())
<div class="alert alert-danger">Veuillez corriger les champs en rouge.</div>
@endif

<div class="row g-4">
    <!-- Proposition Transformation -->
    <div class="col-lg-6 mb-3">
        <label for="proposition_transformation_id" class="form-label">Proposition Transformation <span class="text-danger">*</span></label>
        <select name="proposition_transformation_id" id="proposition_transformation_id"
                class="form-select @error('proposition_transformation_id') is-invalid @enderror" required>
            @foreach($propositions as $prop)
                <option value="{{ $prop->id }}" 
                    @selected(old('proposition_transformation_id', $p->proposition_transformation_id ?? '') == $prop->id)>
                    {{ $prop->description ?? 'Proposition #' . $prop->id }}
                </option>
            @endforeach
        </select>
        @error('proposition_transformation_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Déchet entrant -->
    <div class="col-lg-6 mb-3">
        <label for="dechet_entrant_id" class="form-label">Déchet Entrant <span class="text-danger">*</span></label>
        <select name="dechet_entrant_id" id="dechet_entrant_id"
                class="form-select @error('dechet_entrant_id') is-invalid @enderror" required>
            @foreach($dechets as $dechet)
                <option value="{{ $dechet->id }}" 
                    @selected(old('dechet_entrant_id', $p->dechet_entrant_id ?? '') == $dechet->id)>
                    {{ $dechet->titre }}
                </option>
            @endforeach
        </select>
        @error('dechet_entrant_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Durée estimée -->
    <div class="col-lg-6 mb-3">
        <label for="duree_estimee" class="form-label">Durée estimée (jours)</label>
       <input type="number" name="duree_estimee" id="duree_estimee" min="1"
       class="form-control @error('duree_estimee') is-invalid @enderror"
       value="{{ old('duree_estimee', $p->duree_estimee ?? '') }}">
        @error('duree_estimee')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Coût -->
    <div class="col-lg-6 mb-3">
        <label for="cout" class="form-label">Coût (DT)</label>
        <input type="number" step="0.01" name="cout" id="cout"
       class="form-control @error('cout') is-invalid @enderror"
       value="{{ old('cout', $p->cout ?? '') }}">
        @error('cout')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Équipements -->
    <div class="col-12 mb-3">
        <label for="equipements" class="form-label">Équipements</label>
      <textarea name="equipements" id="equipements" rows="2"
          class="form-control @error('equipements') is-invalid @enderror">{{ old('equipements', $p->equipements ?? '') }}</textarea>
        @error('equipements')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Statut -->
    <div class="col-lg-6 mb-3">
        <label for="statut" class="form-label">Statut</label>
        <select name="statut" id="statut" class="form-select">
            @foreach(['en_cours','termine'] as $opt)
                <option value="{{ $opt }}" @selected(old('statut', $p->statut ?? 'en_cours') == $opt)>
                    {{ ucfirst(str_replace('_',' ',$opt)) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

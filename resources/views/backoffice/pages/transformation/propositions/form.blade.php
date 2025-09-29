@php($p = $proposition ?? null)
@if($errors->any())
<div class="alert alert-danger">Veuillez corriger les champs en rouge.</div>
@endif

<div class="row g-4">
    <!-- Post Déchet -->
    <div class="col-lg-6 mb-3">
        <label for="proposition_id" class="form-label">Post Déchet <span class="text-danger">*</span></label>
        <select name="proposition_id" id="proposition_id" class="form-select @error('proposition_id') is-invalid @enderror" required>
            @foreach($posts as $post)
                <option value="{{ $post->id }}" 
                    @selected(old('proposition_id', $p->proposition_id ?? '') == $post->id)>
                    {{ $post->postDechet->titre ?? 'Titre non défini' }}
                </option>
            @endforeach
        </select>
        @error('proposition_id') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <!-- Transformateur -->
    <div class="col-lg-6 mb-3">
        <label for="transformateur_id" class="form-label">Transformateur <span class="text-danger">*</span></label>
        <select name="transformateur_id" id="transformateur_id" class="form-select @error('transformateur_id') is-invalid @enderror" required>
            @foreach($transformateurs as $id => $name)
                <option value="{{ $id }}" 
                    @selected(old('transformateur_id', $p->transformateur_id ?? '') == $id)>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('transformateur_id') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <!-- Description -->
    <div class="col-12 mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $p->description ?? '') }}</textarea>
        @error('description') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <!-- Statut -->
    <div class="col-lg-6 mb-3">
        <label for="statut" class="form-label">Statut</label>
        <select name="statut" id="statut" class="form-select">
            @foreach(['en_attente','accepté','refusé'] as $opt)
                <option value="{{ $opt }}" @selected(old('statut', $p->statut ?? 'en_attente') == $opt)>{{ ucfirst($opt) }}</option>
            @endforeach
        </select>
    </div>
</div>

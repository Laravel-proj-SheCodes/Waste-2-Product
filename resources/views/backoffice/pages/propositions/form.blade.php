@php($pr = $proposition ?? null)

@if($errors->any())
  <div class="alert alert-danger">Veuillez corriger les champs en rouge.</div>
@endif

<style>
  .form-label{ display:block; font-weight:600; color:#212529!important; margin-bottom:.4rem; }
  .form-control,.form-select{ height:44px; border:1px solid #ced4da; border-radius:.5rem; background:#fff; }
  .form-control:focus,.form-select:focus{ border-color:#0d6efd; box-shadow:0 0 0 .2rem rgba(13,110,253,.15); }
  textarea.form-control{ min-height:110px; height:auto; padding-top:.6rem; }
</style>

<div class="row g-4">

  {{-- Post lié --}}
  <div class="col-lg-6">
    <label class="form-label">Post *</label>
    <select name="post_dechet_id" class="form-select @error('post_dechet_id') is-invalid @enderror" required>
      <option value="" disabled {{ old('post_dechet_id', $pr->post_dechet_id ?? '')==''?'selected':'' }}>
        — Choisir un post —
      </option>
      @foreach($posts as $id => $titre)
        <option value="{{ $id }}" @selected(old('post_dechet_id', $pr->post_dechet_id ?? '')==$id)>{{ $titre }}</option>
      @endforeach
    </select>
    @error('post_dechet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Utilisateur (si tu as l’auth, prends l’ID courant en hidden) --}}
  <div class="col-lg-6">
    <label class="form-label">Utilisateur *</label>
    @if(auth()->check())
      <input type="hidden" name="user_id" value="{{ auth()->id() }}">
      <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
    @else
      {{-- fallback si pas d’auth activée --}}
      <input type="number" name="user_id" value="{{ old('user_id', $pr->user_id ?? 1) }}"
             class="form-control @error('user_id') is-invalid @enderror" required>
      @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @endif
  </div>

  {{-- Description --}}
  <div class="col-12">
    <label class="form-label">Description *</label>
    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>
      {{ old('description', $pr->description ?? '') }}
    </textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Date de proposition --}}
  <div class="col-lg-4">
    <label class="form-label">Date de proposition *</label>
    <input type="date" name="date_proposition"
           value="{{ old('date_proposition', optional($pr->date_proposition ?? null)->format('Y-m-d') ?? now()->toDateString()) }}"
           class="form-control @error('date_proposition') is-invalid @enderror" required>
    @error('date_proposition') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Statut (valeurs conformes à la BDD) --}}
  <div class="col-lg-4">
    <label class="form-label">Statut *</label>
    <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
      @foreach(['en_attente','accepté','refusé'] as $st)
        <option value="{{ $st }}" @selected(old('statut', $pr->statut ?? 'en_attente')==$st)>
          {{ ucfirst($st) }}
        </option>
      @endforeach
    </select>
    @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

</div>

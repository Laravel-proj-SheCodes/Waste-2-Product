@php $p = $proposition ?? null; @endphp

<div class="mb-3">
  <label class="form-label">Description *</label>
  <textarea name="description" class="form-control" rows="5" required>{{ old('description', $p->description ?? '') }}</textarea>
  @error('description') <small class="text-danger">{{ $message }}</small> @enderror
</div>

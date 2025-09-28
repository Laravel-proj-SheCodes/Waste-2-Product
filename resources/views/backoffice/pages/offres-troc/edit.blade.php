@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <!-- Offer Form (Emphasized) -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-5 pb-4">
                        <div class="text-center">
                            <h4 class="text-white font-weight-bold mb-1">Modifier l'Offre de Troc</h4>
                            <p class="text-white-50 mb-0 text-sm">Mettez à jour les détails de votre offre</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 py-5">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Veuillez corriger les erreurs suivantes :</strong>
                            </div>
                            <ul class="list-unstyled mt-2 mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">• {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('offres-troc.update', $offre->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <!-- Catégorie -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="categorie" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-tags text-warning me-2"></i>Catégorie
                                    </label>
                                    <input type="text" 
                                           id="categorie" 
                                           name="categorie" 
                                           class="form-control form-control-lg border-2" 
                                           placeholder="Entrez la catégorie" 
                                           value="{{ old('categorie', $offre->categorie) }}" 
                                           required>
                                    <div class="invalid-feedback">Veuillez fournir une catégorie valide.</div>
                                    @error('categorie')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Quantité -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantite" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-sort-numeric-up text-info me-2"></i>Quantité
                                    </label>
                                    <input type="number" 
                                           id="quantite" 
                                           name="quantite" 
                                           class="form-control form-control-lg border-2" 
                                           placeholder="Entrez la quantité" 
                                           value="{{ old('quantite', $offre->quantite) }}" 
                                           min="1" 
                                           required>
                                    <div class="invalid-feedback">Veuillez fournir une quantité valide (minimum 1).</div>
                                    @error('quantite')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Unité de Mesure -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unite_mesure" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-balance-scale text-primary me-2"></i>Unité de Mesure
                                    </label>
                                    <select id="unite_mesure" 
                                            name="unite_mesure" 
                                            class="form-select form-control-lg border-2" 
                                            required>
                                        <option value="" disabled {{ old('unite_mesure', $offre->unite_mesure) ? '' : 'selected' }}>Choisissez une unité...</option>
                                        <option value="kg" {{ old('unite_mesure', $offre->unite_mesure) == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="litres" {{ old('unite_mesure', $offre->unite_mesure) == 'litres' ? 'selected' : '' }}>litres</option>
                                        <option value="unités" {{ old('unite_mesure', $offre->unite_mesure) == 'unités' ? 'selected' : '' }}>unités</option>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner une unité de mesure.</div>
                                    @error('unite_mesure')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- État -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="etat" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>État
                                    </label>
                                    <select id="etat" 
                                            name="etat" 
                                            class="form-select form-control-lg border-2" 
                                            required>
                                        <option value="" disabled {{ old('etat', $offre->etat) ? '' : 'selected' }}>Choisissez un état...</option>
                                        <option value="neuf" {{ old('etat', $offre->etat) == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="usagé" {{ old('etat', $offre->etat) == 'usagé' ? 'selected' : '' }}>Usagé</option>
                                        <option value="endommagé" {{ old('etat', $offre->etat) == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
                                    </select>
                                    <div class="invalid-feedback">Veuillez sélectionner un état.</div>
                                    @error('etat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Localisation -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="localisation" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Localisation
                                    </label>
                                    <input type="text" 
                                           id="localisation" 
                                           name="localisation" 
                                           class="form-control form-control-lg border-2" 
                                           placeholder="Entrez la localisation" 
                                           value="{{ old('localisation', $offre->localisation) }}" 
                                           required>
                                    <div class="invalid-feedback">Veuillez fournir une localisation valide.</div>
                                    @error('localisation')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Photos -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="photos" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-image text-secondary me-2"></i>Photos
                                        <span class="text-muted font-weight-normal">(Optionnel)</span>
                                    </label>
                                    <input type="file" 
                                           id="photos" 
                                           name="photos[]" 
                                           class="form-control form-control-lg border-2" 
                                           multiple>
                                    <div class="invalid-feedback">Veuillez fournir des fichiers image valides.</div>
                                    @error('photos.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <!-- Display Existing Photos -->
                                    @php
                                        // Ensure $photoPaths is an array
                                        $photoPaths = $offre->photos ? json_decode($offre->photos, true) : [];
                                        if (!is_array($photoPaths)) {
                                            $photoPaths = [];
                                        }
                                    @endphp
                                    @if (!empty($photoPaths))
                                        <div class="mt-2 d-flex flex-wrap">
                                            @foreach ($photoPaths as $index => $photo)
                                                <div class="position-relative me-2 mb-2">
                                                    <img src="{{ asset('storage/' . $photo) }}" 
                                                         alt="Offre image {{ $index + 1 }}" 
                                                         class="troc-image">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                            onclick="confirmDestroyPhoto('{{ route('offres-troc.photo-destroy', [$offre->post_dechet_id, $offre->id, $index]) }}')"
                                                            {{ strtolower($offre->status) === 'accepted' ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label text-dark font-weight-bold mb-2">
                                        <i class="fas fa-align-left text-secondary me-2"></i>Description
                                        <span class="text-muted font-weight-normal">(Optionnel)</span>
                                    </label>
                                    <textarea id="description" 
                                              name="description" 
                                              class="form-control form-control-lg border-2" 
                                              rows="4" 
                                              placeholder="Ajoutez des détails supplémentaires sur l'offre...">{{ old('description', $offre->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('postdechets.show', $offre->post_dechet_id) }}" 
                                       class="btn btn-outline-secondary btn-lg px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" 
                                            class="btn bg-gradient-dark btn-lg px-5 shadow">
                                        <i class="fas fa-save me-2"></i>Mettre à jour l'Offre
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        transition: all 0.3s ease;
        border-radius: 8px;
        border-color: #e6ffe6;
        background: #f9fffb;
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #2ecc71;
        box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
        transform: translateY(-1px);
    }
    .form-label {
        font-size: 0.875rem;
        letter-spacing: 0.025em;
        color: #3c6e4d;
    }
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .btn {
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.025em;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    .alert {
        border-radius: 10px;
    }
    .form-control::placeholder, .form-select::placeholder {
        color: #adb5bd;
        font-style: italic;
    }
    .troc-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Client-side form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Confirm photo deletion
    window.confirmDestroyPhoto = function(url) {
        if (confirm('Voulez-vous vraiment supprimer cette photo ?')) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression de la photo : ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur s\'est produite lors de la suppression.');
            });
        }
    };
});
</script>
@endsection
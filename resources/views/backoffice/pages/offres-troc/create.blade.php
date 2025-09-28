@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <!-- Post Details (Minimized) -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 {{ auth()->check() && $post->favoritedBy->contains(auth()->id()) ? 'favorited-card' : '' }}">
                <div class="card-header bg-light border-bottom p-3">
                    <h5 class="text-dark font-weight-bold mb-0">Détails du Post</h5>
                </div>
                <div class="card-body px-3 py-3">
                    @php
                        // Ensure $photoPaths is an array
                        $photoPaths = $post->photos ?? [];
                        if (is_string($photoPaths)) {
                            $photoPaths = json_decode($photoPaths, true) ?? [];
                        }
                        if (!is_array($photoPaths)) {
                            $photoPaths = [];
                        }
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-box text-success me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Titre:</span>
                                <span class="text-dark ms-2 text-sm">{{ $post->titre }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-sort-numeric-up text-info me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Quantité:</span>
                                <span class="text-dark ms-2 text-sm">{{ $post->quantite }} {{ $post->unite_mesure }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-tags text-warning me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">État:</span>
                                <span class="text-dark ms-2 text-sm">{{ ucfirst($post->etat) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Localisation:</span>
                                <span class="text-dark ms-2 text-sm">{{ $post->localisation }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Statut:</span>
                                <span class="ms-2">
                                    @if ($post->statut === 'accepted')
                                        <span class="badge badge-sm bg-success">{{ $post->statut }}</span>
                                    @elseif ($post->statut === 'rejected')
                                        <span class="badge badge-sm bg-danger">{{ $post->statut }}</span>
                                    @else
                                        <span class="badge badge-sm bg-warning">{{ $post->statut }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Utilisateur:</span>
                                <span class="text-dark ms-2 text-sm">{{ $post->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-align-left text-secondary me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Description:</span>
                                <span class="text-dark ms-2 text-sm">{{ $post->description ?? 'Aucune' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-image text-secondary me-2"></i>
                                <span class="text-dark font-weight-bold text-sm">Images:</span>
                                <div class="ms-2 d-flex flex-wrap">
                                    @if (!empty($photoPaths))
                                        @foreach ($photoPaths as $index => $photo)
                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                 alt="{{ $post->titre }} image {{ $index + 1 }}" 
                                                 class="troc-image me-2 mb-2" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal" 
                                                 data-images="{{ json_encode($photoPaths) }}" 
                                                 data-index="{{ $index }}">
                                        @endforeach
                                    @else
                                        <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offer Form (Emphasized) -->
    <div class="row justify-content-center mt-4">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-5 pb-4">
                        <div class="text-center">
                            <h4 class="text-white font-weight-bold mb-1">Créer une Offre de Troc</h4>
                            <p class="text-white-50 mb-0 text-sm">Remplissez les détails ci-dessous pour proposer une offre</p>
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

                    <form action="{{ route('offres-troc.store', $post->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
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
                                           value="{{ old('categorie') }}" 
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
                                           value="{{ old('quantite') }}" 
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
                                            class="form-select form-select-lg border-2" 
                                            required>
                                        <option value="" disabled {{ old('unite_mesure') ? '' : 'selected' }}>Choisissez une unité...</option>
                                        <option value="kg" {{ old('unite_mesure') == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="litres" {{ old('unite_mesure') == 'litres' ? 'selected' : '' }}>litres</option>
                                        <option value="unités" {{ old('unite_mesure') == 'unités' ? 'selected' : '' }}>unités</option>
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
                                            class="form-select form-select-lg border-2" 
                                            required>
                                        <option value="" disabled {{ old('etat') ? '' : 'selected' }}>Choisissez un état...</option>
                                        <option value="neuf" {{ old('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="usagé" {{ old('etat') == 'usagé' ? 'selected' : '' }}>Usagé</option>
                                        <option value="endommagé" {{ old('etat') == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
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
                                           value="{{ old('localisation') }}" 
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
                                              placeholder="Ajoutez des détails supplémentaires sur l'offre...">{{ old('description') }}</textarea>
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
                                    <a href="{{ route('postdechets.show', $post->id) }}" 
                                       class="btn btn-outline-secondary btn-lg px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <button type="submit" 
                                            class="btn bg-gradient-dark btn-lg px-5 shadow">
                                        <i class="fas fa-save me-2"></i>Créer l'Offre
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Gallery Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Galerie d'images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" class="gallery-image" src="" alt="Galerie">
                    <button class="gallery-nav prev-btn" onclick="changeImage(-1)">&#10094;</button>
                    <button class="gallery-nav next-btn" onclick="changeImage(1)">&#10095;</button>
                </div>
                <div class="modal-footer">
                    <span id="imageCounter"></span>
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
    .badge {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5em 1em;
    }
    .bg-success {
        background-color: #2dce89 !important;
    }
    .bg-danger {
        background-color: #f5365c !important;
    }
    .bg-warning {
        background-color: #fb6340 !important;
    }
    .card.favorited-card {
        box-shadow: 0 8px 24px rgba(25, 135, 84, 0.3);
        border: 1px solid rgba(25, 135, 84, 0.5);
    }
    .troc-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
    }
    /* Modal styles for image gallery */
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
    }
    .modal-body {
        padding: 0;
        position: relative;
    }
    .gallery-image {
        width: 100%;
        max-height: 80vh;
        object-fit: contain;
    }
    .gallery-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        font-size: 1.5rem;
        border-radius: 50%;
    }
    .gallery-nav:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }
    .prev-btn {
        left: 10px;
    }
    .next-btn {
        right: 10px;
    }
    .modal-header {
        border-bottom: none;
    }
    .modal-footer {
        border-top: none;
        justify-content: center;
    }
    /* Minimize Post Details */
    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }
    .text-sm {
        font-size: 0.85rem;
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

    // Image gallery modal
    let currentImages = [];
    let currentIndex = 0;

    document.querySelectorAll('.troc-image').forEach(image => {
        image.addEventListener('click', function () {
            try {
                currentImages = JSON.parse(this.dataset.images);
                currentIndex = parseInt(this.dataset.index);
                updateModal();
            } catch (e) {
                console.error('Error parsing images:', e);
                currentImages = [];
                currentIndex = 0;
                updateModal();
            }
        });
    });

    function updateModal() {
        const modalImage = document.getElementById('modalImage');
        const imageCounter = document.getElementById('imageCounter');
        if (currentImages.length > 0) {
            modalImage.src = '{{ asset("storage/") }}/' + currentImages[currentIndex];
            imageCounter.textContent = `Image ${currentIndex + 1} sur ${currentImages.length}`;
            document.querySelector('.prev-btn').style.display = currentImages.length > 1 ? 'block' : 'none';
            document.querySelector('.next-btn').style.display = currentImages.length > 1 ? 'block' : 'none';
        } else {
            modalImage.src = '';
            imageCounter.textContent = 'Aucune image disponible';
            document.querySelector('.prev-btn').style.display = 'none';
            document.querySelector('.next-btn').style.display = 'none';
        }
    }

    window.changeImage = function (direction) {
        if (currentImages.length > 0) {
            currentIndex = (currentIndex + direction + currentImages.length) % currentImages.length;
            updateModal();
        }
    };
});
</script>
@endsection
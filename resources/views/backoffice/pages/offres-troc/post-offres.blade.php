@extends('backoffice.layouts.layout')

@section('styles')
<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    .troc-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer; /* Indicate clickable image */
    }
    .badge {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5em 1em;
        transition: all 0.3s ease;
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
    .text-dark {
        color: #343a40 !important;
    }
    .font-weight-bold {
        font-weight: 600 !important;
    }
    .bg-dark {
        background-color: #343a40 !important;
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
        box-shadow: 0 8px 24px rgba(25, 135, 84, 0.3); /* Green shadow for favorited post */
        border: 1px solid rgba(25, 135, 84, 0.5); /* Subtle green border */
    }
    tr.favorited-card {
        box-shadow: 0 8px 24px rgba(25, 135, 84, 0.3); /* Green shadow for favorited offer rows */
        border: 1px solid rgba(25, 135, 84, 0.5);
    }
    .offre-accepted-message {
        background-color: #e6ffe6;
        color: #2a5d3a;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: 600;
    }
    .dropdown {
        position: relative;
        display: inline-block;
    }
    .dropdown-btn {
        cursor: pointer;
        font-size: 1.5rem;
        color: #343a40;
    }
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #fff;
        min-width: 120px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        z-index: 1;
        top: 100%;
        left: 0;
    }
    .dropdown-content a, .dropdown-content button {
        color: #343a40;
        padding: 8px 16px;
        text-decoration: none;
        display: block;
        font-size: 0.9rem;
    }
    .dropdown-content a:hover, .dropdown-content button:hover {
        background-color: #f8f9fa;
    }
    .dropdown:hover .dropdown-content {
        display: block;
    }
    .action-btns .accept-btn, .action-btns .reject-btn {
        width: 100%;
        padding: 8px;
        margin-top: 8px;
        border-radius: 8px;
        font-weight: 600;
    }
    .action-btns .accept-btn {
        background-color: #2dce89;
        color: #fff;
        border: none;
    }
    .action-btns .accept-btn:hover {
        background-color: #28a745;
        transform: translateY(-2px);
    }
    .action-btns .reject-btn {
        background-color: #f5365c;
        color: #fff;
        border: none;
    }
    .action-btns .reject-btn:hover {
        background-color: #dc3545;
        transform: translateY(-2px);
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
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Post Details -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card shadow-lg border-0 {{ auth()->check() && $post->favoritedBy->contains(auth()->id()) ? 'favorited-card' : '' }}">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-dark shadow-dark border-radius-lg pt-5 pb-4">
                        <div class="text-center">
                            <h4 class="text-white font-weight-bold mb-1">Détails du Post de Troc</h4>
                            <p class="text-white-50 mb-0 text-sm">Visualisez les détails du post ci-dessous</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 py-5">
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
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-id-badge text-primary me-2"></i>
                                <span class="text-dark font-weight-bold">ID:</span>
                                <span class="text-dark ms-2">{{ $post->id }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-box text-success me-2"></i>
                                <span class="text-dark font-weight-bold">Titre:</span>
                                <span class="text-dark ms-2">{{ $post->titre }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-sort-numeric-up text-info me-2"></i>
                                <span class="text-dark font-weight-bold">Quantité:</span>
                                <span class="text-dark ms-2">{{ $post->quantite }} {{ $post->unite_mesure }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-tags text-warning me-2"></i>
                                <span class="text-dark font-weight-bold">État:</span>
                                <span class="text-dark ms-2">{{ ucfirst($post->etat) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-dark font-weight-bold">Localisation:</span>
                                <span class="text-dark ms-2">{{ $post->localisation }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-check-circle text-info me-2"></i>
                                <span class="text-dark font-weight-bold">Statut:</span>
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
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-primary me-2"></i>
                                <span class="text-dark font-weight-bold">Utilisateur:</span>
                                <span class="text-dark ms-2">{{ $post->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-align-left text-secondary me-2"></i>
                                <span class="text-dark font-weight-bold">Description:</span>
                                <span class="text-dark ms-2">{{ $post->description ?? 'Aucune' }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-image text-secondary me-2"></i>
                                <span class="text-dark font-weight-bold">Images:</span>
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
                    <!-- Action buttons -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="{{ route('postdechets.edit', $post) }}" 
                                   class="btn btn-outline-warning btn-lg px-4 me-2" 
                                   data-toggle="tooltip" 
                                   data-original-title="Modifier Post">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <form action="{{ route('postdechets.destroy', $post) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-lg px-4 me-2" 
                                            data-toggle="tooltip" 
                                            data-original-title="Supprimer Post" 
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce post ?')">
                                        <i class="fas fa-trash me-2"></i>Supprimer
                                    </button>
                                </form>
                                <a href="{{ route('offres-troc.create', $post) }}" 
                                   class="btn btn-outline-primary btn-lg px-4 me-2" 
                                   data-toggle="tooltip" 
                                   data-original-title="Proposer une Offre">
                                    <i class="fas fa-plus-circle me-2"></i>Proposer une Offre
                                </a>
                                <a href="{{ route('postdechets.troc') }}" 
                                   class="btn btn-outline-secondary btn-lg px-4" 
                                   data-toggle="tooltip" 
                                   data-original-title="Retour à la Liste">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offers Table -->
    <div class="row justify-content-center mt-5">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-dark shadow-dark border-radius-lg pt-5 pb-4">
                        <div class="text-center">
                            <h4 class="text-white font-weight-bold mb-1">Offres Associées</h4>
                            <p class="text-white-50 mb-0 text-sm">Liste des offres pour ce post</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @php
                        $hasAcceptedOffer = $offres->contains(function($offre) {
                            return strtolower($offre->status) === 'accepted';
                        });
                    @endphp
                    @if ($hasAcceptedOffer)
                        <div class="offre-accepted-message mx-4">
                            ✓ Une offre a été acceptée pour ce post. Les boutons d'action sont désactivés.
                        </div>
                    @endif
                    @if ($offres->isEmpty())
                        <p class="text-center text-gray-600 mx-4">Aucune offre associée pour ce post.</p>
                    @else
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Images</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Catégorie</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">État</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Localisation</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($offres as $offre)
                                        @php
                                            // Ensure $photoPaths is an array for offers
                                            $photoPaths = $offre->photos ?? [];
                                            if (is_string($photoPaths)) {
                                                $photoPaths = json_decode($photoPaths, true) ?? [];
                                            }
                                            if (!is_array($photoPaths)) {
                                                $photoPaths = [];
                                            }
                                            $offreStatus = strtolower($offre->status);
                                            $isRejected = $offreStatus === 'rejected';
                                            $showButtons = !$hasAcceptedOffer && !$isRejected;
                                            $isFavorited = auth()->check() && $offre->postDechet && $offre->postDechet->favoritedBy->contains(auth()->id());
                                        @endphp
                                        <tr class="{{ $isFavorited ? 'favorited-card' : '' }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $offre->id }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if (!empty($photoPaths))
                                                    <div class="d-flex flex-wrap">
                                                        @foreach ($photoPaths as $index => $photo)
                                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                                 alt="{{ $offre->description }} image {{ $index + 1 }}" 
                                                                 class="troc-image me-2 mb-2" 
                                                                 data-bs-toggle="modal" 
                                                                 data-bs-target="#imageModal" 
                                                                 data-images="{{ json_encode($photoPaths) }}" 
                                                                 data-index="{{ $index }}">
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="troc-image" style="background: #e6ffe6; display: flex; align-items: center; justify-content: center; color: #2a5d3a;">Pas d'image</div>
                                                @endif
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ \Illuminate\Support\Str::limit($offre->description, 50) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $offre->categorie }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ ucfirst($offre->etat) }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0">{{ $offre->localisation }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($offreStatus === 'accepted')
                                                    <span class="badge badge-sm bg-success">{{ $offre->status }}</span>
                                                @elseif ($offreStatus === 'rejected')
                                                    <span class="badge badge-sm bg-danger">{{ $offre->status }}</span>
                                                @else
                                                    <span class="badge badge-sm bg-warning">{{ $offre->status }}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-btn">⋮</span>
                                                    <div class="dropdown-content">
                                                        <a href="{{ route('offres-troc.edit', $offre->id) }}">Modifier</a>
                                                        <form action="{{ route('offres-troc.destroy', $offre->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700" style="border: none; background: none; padding: 0; width: 100%; text-align: left;">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @if ($showButtons)
                                                    <div class="action-btns mt-2">
                                                        <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="accepted">
                                                            <button type="submit" class="accept-btn">Accepter</button>
                                                        </form>
                                                        <form action="{{ route('offres-troc.update-statut', $offre->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="reject-btn">Refuser</button>
                                                        </form>
                                                    </div>
                                                @elseif ($offreStatus === 'accepted')
                                                    <div class="text-center mt-3 p-2 bg-green-100 text-green-700 rounded-lg">
                                                        <strong>✓ Offre acceptée</strong>
                                                    </div>
                                                @elseif ($offreStatus === 'rejected')
                                                    <div class="text-center mt-3 p-2 bg-red-100 text-red-700 rounded-lg">
                                                        <strong>✗ Offre refusée</strong>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentImages = [];
    let currentIndex = 0;

    // Handle image click to open modal
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

    // Function to update modal content
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

    // Function to change image
    window.changeImage = function (direction) {
        if (currentImages.length > 0) {
            currentIndex = (currentIndex + direction + currentImages.length) % currentImages.length;
            updateModal();
        }
    };
});
</script>
@endsection
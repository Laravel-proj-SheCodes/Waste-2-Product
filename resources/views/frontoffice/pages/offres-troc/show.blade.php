@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
/* Cartes compactes */
.troc-card {
    border: 0;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
    padding: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.offre-accepted {
    border: 2px solid #198754; /* Bordure verte pour les cartes acceptées */
}

.troc-image {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 0.5rem;
}

.thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 0.25rem;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}
.thumb.active { border-color: #198754; }

.action-btns {
    display: flex;
    gap: 0.25rem;
    margin-top: 0.25rem;
}
.accept-btn, .reject-btn, .edit-btn, .delete-btn {
    flex: 1;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-size: 0.8rem;
    transition: all 0.2s ease;
}
.accept-btn { background-color: #198754; color: #fff; }
.accept-btn:hover { background-color: #157347; transform: translateY(-1px); }
.reject-btn { background-color: #dc3545; color: #fff; }
.reject-btn:hover { background-color: #b02a37; transform: translateY(-1px); }
.edit-btn { background-color: #ffc107; color: #000; }
.edit-btn:hover { background-color: #e0a800; transform: translateY(-1px); }
.delete-btn { background-color: #6c757d; color: #fff; }
.delete-btn:hover { background-color: #5a6268; transform: translateY(-1px); }

.status-badge {
    display: inline-block;
    padding: 0.15rem 0.4rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}
.status-accepted { background: #d0f0d0; color: #198754; }
.status-rejected { background: #f8d7da; color: #dc3545; }
.status-pending { background: #fff3cd; color: #856404; }

/* Styles pour le modal */
.modal-dialog {
    max-width: 800px;
}
.modal-content {
    border-radius: 12px;
}
.modal-header {
    background-color: #198754;
    color: #fff;
    border-bottom: none;
}
.modal-body {
    display: flex;
    gap: 1rem;
    padding: 1rem;
}
.modal-image-container {
    flex: 1;
}
.modal-attributes {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.carousel-inner img {
    width: 100%;
    height: 300px;
    object-fit: contain;
    background-color: #f8f9fa;
}
.modal-attributes p {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
</style>

<div class="container py-4">
    <a href="{{ route('postdechets.troc.front') }}" class="text-success mb-2 d-inline-block">&larr; Retour</a>

    <h2 class="h6 text-success mb-2">Offres Associées</h2>

    @if($offres->isEmpty())
        <p class="text-muted">Aucune offre associée pour ce post.</p>
    @else
        <div class="row g-2">
            @foreach($offres as $offre)
                @php
                    $photoPaths = $offre->photos ?? [];
                    if (!is_array($photoPaths)) {
                        $photoPaths = json_decode($photoPaths, true) ?? [];
                    }
                    $offreStatus = strtolower($offre->status);
                    $isRejected = $offreStatus === 'rejected';
                    $hasAcceptedOffer = $offres->contains(fn($o) => strtolower($o->status) === 'accepted');
                    $showButtons = !$hasAcceptedOffer && !$isRejected;
                    $isOwner = auth()->check() && auth()->id() === $offre->user_id; // Vérifie si l'utilisateur est le créateur de l'offre
                    $showEditDelete = $isOwner && $offreStatus !== 'accepted'; // Affiche Modifier/Supprimer si l'utilisateur est le créateur et l'offre n'est pas acceptée
                @endphp

                <div class="col-4 col-md-4 col-lg-4">
                    <div class="troc-card @if($offreStatus==='accepted') offre-accepted @endif" data-bs-toggle="modal" data-bs-target="#offreModal{{ $offre->id }}">
                        @if(!empty($photoPaths))
                            @php $img = $photoPaths[0]; @endphp
                            <img src="{{ asset('storage/' . $img) }}" class="troc-image" alt="{{ $offre->description }}">
                        @else
                            <div class="troc-image d-flex align-items-center justify-content-center text-success text-sm">Pas d'image</div>
                        @endif

                        <h6 class="fw-semibold mb-1" style="font-size:0.8rem;">{{ $offre->description }}</h6>
                        <p class="mb-1" style="font-size:0.75rem;">
                            <strong>Quantité :</strong> {{ $offre->quantite }} {{ $offre->unite_mesure }}
                        </p>
                        <p style="font-size:0.75rem;">Statut :
                            <span class="status-badge
                                @switch($offreStatus)
                                    @case('accepted') status-accepted @break
                                    @case('rejected') status-rejected @break
                                    @default status-pending
                                @endswitch">{{ ucfirst($offre->status) }}</span>
                        </p>

                        <div class="action-btns">
                            @if(auth()->check() && auth()->id() === $post->user_id && $showButtons)
                                <form action="{{ route('offres-troc.update-statut.front', $offre->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="accept-btn">✓</button>
                                </form>
                                <form action="{{ route('offres-troc.update-statut.front', $offre->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="reject-btn">✗</button>
                                </form>
                            @endif
                            @if($showEditDelete)
                                <a href="{{ route('offres-troc.edit.front', $offre->id) }}" class="edit-btn">✎</a>
                                <form action="{{ route('offres-troc.destroy.front', $offre->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="delete-btn">🗑</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal pour cette offre -->
                <div class="modal fade" id="offreModal{{ $offre->id }}" tabindex="-1" aria-labelledby="offreModalLabel{{ $offre->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="offreModalLabel{{ $offre->id }}">{{ $offre->description }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if(!empty($photoPaths))
                                    <div class="modal-image-container">
                                        <div id="carouselOffre{{ $offre->id }}" class="carousel slide" data-bs-ride="carousel">
                                            <div class="carousel-inner">
                                                @foreach($photoPaths as $index => $photo)
                                                    <div class="carousel-item @if($index === 0) active @endif">
                                                        <img src="{{ asset('storage/' . $photo) }}" class="d-block w-100" alt="Image {{ $index + 1 }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if(count($photoPaths) > 1)
                                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselOffre{{ $offre->id }}" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Précédent</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#carouselOffre{{ $offre->id }}" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Suivant</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-attributes">
                                        <p><strong>Description :</strong> {{ $offre->description }}</p>
                                        <p><strong>Quantité :</strong> {{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
                                        <p><strong>Statut :</strong>
                                            <span class="status-badge
                                                @switch($offreStatus)
                                                    @case('accepted') status-accepted @break
                                                    @case('rejected') status-rejected @break
                                                    @default status-pending
                                                @endswitch">{{ ucfirst($offre->status) }}</span>
                                        </p>
                                        @if($showEditDelete)
                                            <div class="action-btns">
                                                <a href="{{ route('offres-troc.edit.front', $offre->id) }}" class="edit-btn">✎ Modifier</a>
                                                <form action="{{ route('offres-troc.destroy.front', $offre->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="delete-btn">🗑 Supprimer</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="modal-attributes text-center">
                                        <p>Aucune image disponible.</p>
                                        <p><strong>Description :</strong> {{ $offre->description }}</p>
                                        <p><strong>Quantité :</strong> {{ $offre->quantite }} {{ $offre->unite_mesure }}</p>
                                        <p><strong>Statut :</strong>
                                            <span class="status-badge
                                                @switch($offreStatus)
                                                    @case('accepted') status-accepted @break
                                                    @case('rejected') status-rejected @break
                                                    @default status-pending
                                                @endswitch">{{ ucfirst($offre->status) }}</span>
                                        </p>
                                        @if($showEditDelete)
                                            <div class="action-btns">
                                                <a href="{{ route('offres-troc.edit.front', $offre->id) }}" class="edit-btn">✎ Modifier</a>
                                                <form action="{{ route('offres-troc.destroy.front', $offre->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette offre ?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="delete-btn">🗑 Supprimer</button>
                                                </form>
                                            </div>
                                        @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Inclure Bootstrap JS pour le fonctionnement du modal et du carrousel -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
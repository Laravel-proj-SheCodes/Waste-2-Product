@extends('frontoffice.layouts.layoutfront')

@section('content')
<!-- Marketplace Header Section -->
<section class="py-5 bg-light border-bottom">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder">Marketplace des Déchets</h1>
            <p class="lead mb-0">Transformez vos déchets en opportunités commerciales</p>
        </div>
    </div>
</section>

<!-- Create Announcement Section -->
<section class="py-5 border-bottom">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Créer une Annonce</h5>
                    </div>
                    <div class="card-body">
                        <form id="createAnnonceForm">
                            @csrf
                            <div class="row gx-3">
                                <div class="col-md-6 mb-3">
                                    <label for="post_dechet_id" class="form-label">Sélectionner votre déchet</label>
                                    <select class="form-select" id="post_dechet_id" name="post_dechet_id" required>
                                        <option value="">Choisir un déchet...</option>
                                        <!-- Will be populated by JavaScript -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="prix" class="form-label">Prix (€)</label>
                                    <input type="number" class="form-control" id="prix" name="prix" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Créer l'Annonce
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- My Announcements Section -->
<section class="py-5 bg-light">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Mes Annonces</h2>
            <p class="lead mb-0">Gérez vos annonces marketplace</p>
        </div>
        
        <!-- Announcements Grid -->
        <div id="annoncesContainer" class="row gx-5">
            <!-- Will be populated by JavaScript -->
        </div>
        
        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5" style="display: none;">
            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Aucune annonce trouvée</h4>
            <p class="text-muted">Créez votre première annonce pour commencer à vendre vos déchets</p>
        </div>
    </div>
</section>

<!-- All Announcements Section -->
<section class="py-5 border-bottom">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Toutes les Annonces</h2>
            <p class="lead mb-0">Découvrez les opportunités disponibles</p>
        </div>
        
        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchFilter" placeholder="Rechercher...">
            </div>
            <div class="col-md-4">
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="vendue">Vendu</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-success" id="applyFilters">
                    <i class="bi bi-funnel me-2"></i>Filtrer
                </button>
            </div>
        </div>
        
        <!-- All Announcements Grid -->
        <div id="allAnnoncesContainer" class="row gx-5">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>
</section>

<!-- Edit Modal -->
<div class="modal fade" id="editAnnonceModal" tabindex="-1" aria-labelledby="editAnnonceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnnonceModalLabel">Modifier l'Annonce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnnonceForm">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_annonce_id" name="annonce_id">
                    <div class="mb-3">
                        <label for="edit_prix" class="form-label">Prix (€)</label>
                        <input type="number" class="form-control" id="edit_prix" name="prix" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_statut" class="form-label">Statut</label>
                        <select class="form-select" id="edit_statut" name="statut_annonce" required>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                            <option value="vendue">Vendu</option>
                            <option value="expiree">Expiré</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
    <!-- Alerts will be inserted here -->
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    // Load user's waste posts for the dropdown
    loadUserPostDechets();
    
    // Load user's announcements
    loadMesAnnonces();
    
    // Load all announcements
    loadAllAnnonces();
    
    // Setup form handlers
    setupFormHandlers();
    
    // Setup filter handlers
    setupFilterHandlers();
});

function loadUserPostDechets() {
    console.log('Loading user post dechets...');
    const select = document.getElementById('post_dechet_id');
    if (!select) {
        console.error('Dropdown element post_dechet_id not found');
        showAlert('Erreur: Élément de sélection introuvable.', 'danger');
        return;
    }

    fetch('/api/mes-post-dechets', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Parsed data:', data);
        select.innerHTML = '<option value="">Choisir un déchet...</option>';

        const posts = data.data || [];
        if (!posts.length) {
            showAlert('Aucun déchet trouvé. <a href="/postdechets/create" class="alert-link">Créez un nouveau post de déchet</a>.', 'warning');
            return;
        }

        posts.forEach(post => {
            const option = document.createElement('option');
            option.value = post.id;
            option.textContent = `${post.titre} - ${post.localisation}`;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error loading post dechets:', error);
        showAlert('Erreur lors du chargement des déchets: ' + error.message, 'danger');
    });
}

function loadMesAnnonces() {
    fetch('/mes-annonces', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            showAlert(data.error, 'danger');
            return;
        }
        displayMesAnnonces(data);
    })
    .catch(error => {
        console.error('Error loading mes annonces:', error);
        document.getElementById('emptyState').style.display = 'block';
        showAlert('Erreur lors du chargement des annonces', 'danger');
    });
}

function loadAllAnnonces() {
    const search = document.getElementById('searchFilter').value;
    const status = document.getElementById('statusFilter').value;
    const query = new URLSearchParams();
    if (search) query.append('search', search);
    if (status) query.append('status', status);

    fetch(`/annonces?${query.toString()}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        displayAllAnnonces(data.data || data);
    })
    .catch(error => {
        console.error('Error loading all annonces:', error);
        showAlert('Erreur lors du chargement des annonces', 'danger');
    });
}

function displayMesAnnonces(annonces) {
    const container = document.getElementById('annoncesContainer');
    const emptyState = document.getElementById('emptyState');
    
    if (!annonces || annonces.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = annonces.map(annonce => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge ${getStatusBadgeClass(annonce.statut_annonce)}">${getStatusText(annonce.statut_annonce)}</span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="editAnnonce(${annonce.id}, ${annonce.prix}, '${annonce.statut_annonce}')">
                                <i class="bi bi-pencil me-2"></i>Modifier
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteAnnonce(${annonce.id})">
                                <i class="bi bi-trash me-2"></i>Supprimer
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${annonce.post_dechet?.titre || 'Titre non disponible'}</h5>
                    <p class="card-text text-muted">${annonce.post_dechet?.description ? annonce.post_dechet.description.substring(0, 100) + '...' : ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 text-success mb-0">${annonce.prix}€</span>
                        <small class="text-muted">
                            <i class="bi bi-geo-alt me-1"></i>${annonce.post_dechet?.localisation || ''}
                        </small>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>Créé le ${new Date(annonce.created_at).toLocaleDateString('fr-FR')}</small>
                </div>
            </div>
        </div>
    `).join('');
}

function displayAllAnnonces(annonces) {
    const container = document.getElementById('allAnnoncesContainer');
    
    if (!annonces || annonces.length === 0) {
        container.innerHTML = '<div class="col-12 text-center"><p class="text-muted">Aucune annonce disponible</p></div>';
        return;
    }
    
    container.innerHTML = annonces.map(annonce => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header">
                    <span class="badge ${getStatusBadgeClass(annonce.statut_annonce)}">${getStatusText(annonce.statut_annonce)}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">${annonce.post_dechet?.titre || 'Titre non disponible'}</h5>
                    <p class="card-text text-muted">${annonce.post_dechet?.description ? annonce.post_dechet.description.substring(0, 100) + '...' : ''}</p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="h5 text-success mb-0">${annonce.prix}€</span>
                        <small class="text-muted">
                            <i class="bi bi-geo-alt me-1"></i>${annonce.post_dechet?.localisation || ''}
                        </small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Par: ${annonce.post_dechet?.user?.nom || 'Utilisateur'}
                        </small>
                        ${annonce.statut_annonce === 'active' ? 
                            '<button class="btn btn-sm btn-success" onclick="contactSeller(' + annonce.id + ')">Contacter</button>' : 
                            '<button class="btn btn-sm btn-secondary" disabled>Non disponible</button>'
                        }
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>Créé le ${new Date(annonce.created_at).toLocaleDateString('fr-FR')}</small>
                </div>
            </div>
        </div>
    `).join('');
}

function setupFormHandlers() {
    // Create announcement form
    document.getElementById('createAnnonceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('/annonces', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
            } else {
                showAlert('Annonce créée avec succès!', 'success');
                this.reset();
                loadMesAnnonces();
                loadAllAnnonces();
            }
        })
        .catch(error => {
            console.error('Error creating annonce:', error);
            showAlert('Erreur lors de la création de l\'annonce', 'danger');
        });
    });
    
    // Edit announcement form
    document.getElementById('editAnnonceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const annonceId = document.getElementById('edit_annonce_id').value;
        const formData = new FormData(this);
        
        fetch(`/annonces/${annonceId}`, {
            method: 'POST', // Laravel interprets _method='PUT'
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
            } else {
                showAlert('Annonce mise à jour avec succès!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('editAnnonceModal')).hide();
                loadMesAnnonces();
                loadAllAnnonces();
            }
        })
        .catch(error => {
            console.error('Error updating annonce:', error);
            showAlert('Erreur lors de la mise à jour', 'danger');
        });
    });
}

function setupFilterHandlers() {
    document.getElementById('applyFilters').addEventListener('click', function() {
        loadAllAnnonces(); // Filters are handled in loadAllAnnonces
    });
}

function editAnnonce(id, prix, statut) {
    document.getElementById('edit_annonce_id').value = id;
    document.getElementById('edit_prix').value = prix;
    document.getElementById('edit_statut').value = statut;
    
    new bootstrap.Modal(document.getElementById('editAnnonceModal')).show();
}

function deleteAnnonce(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')) {
        return;
    }
    
    fetch(`/annonces/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            showAlert(data.error, 'danger');
        } else {
            showAlert('Annonce supprimée avec succès!', 'success');
            loadMesAnnonces();
            loadAllAnnonces();
        }
    })
    .catch(error => {
        console.error('Error deleting annonce:', error);
        showAlert('Erreur lors de la suppression', 'danger');
    });
}

function contactSeller(annonceId) {
    showAlert('Fonctionnalité de contact en cours de développement', 'info');
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'active': return 'bg-success';
        case 'inactive': return 'bg-secondary';
        case 'vendue': return 'bg-info';
        case 'expiree': return 'bg-danger';
        default: return 'bg-warning';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'active': return 'Actif';
        case 'inactive': return 'Inactif';
        case 'vendue': return 'Vendu';
        case 'expiree': return 'Expiré';
        default: return 'Inconnu';
    }
}

function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.remove();
        }
    }, 5000);
}
</script>
@endpush

@extends('frontoffice.layouts.layoutfront')

@section('content')
    <!-- Orders Header Section -->
    <section class="py-5 bg-gradient-to-r from-green-50 to-emerald-50 border-bottom">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-green-800 mb-3">
                    <i class="bi bi-box-seam me-3 text-green-600"></i>
                    Gestion des Commandes
                </h1>
                <p class="lead text-green-700 mb-0">Suivez vos commandes et gérez celles reçues</p>
                <div class="mt-4">
                    <span class="badge bg-green-100 text-green-800 px-3 py-2 rounded-pill me-2">
                        <i class="bi bi-cart me-1"></i>Mes Achats
                    </span>
                    <span class="badge bg-emerald-100 text-emerald-800 px-3 py-2 rounded-pill me-2">
                        <i class="bi bi-shop me-1"></i>Mes Ventes
                    </span>
                    <span class="badge bg-teal-100 text-teal-800 px-3 py-2 rounded-pill">
                        <i class="bi bi-graph-up me-1"></i>Suivi en Temps Réel
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- My Orders Section -->
    <section class="py-5 bg-white">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gray-800 mb-3">
                    <i class="bi bi-person-badge me-2 text-green-600"></i>Mes Commandes
                </h2>
                <p class="lead text-gray-600 mb-0">Consultez les commandes que vous avez passées</p>
            </div>
            
            <!-- My Orders Grid -->
            <div id="mesCommandesContainer" class="row gx-4 gy-4">
                <!-- Will be populated by JavaScript -->
            </div>
            
            <!-- Empty State -->
            <div id="mesCommandesEmptyState" class="text-center py-5" style="display: none;">
                <div class="mb-4">
                    <i class="bi bi-cart-x text-gray-400" style="font-size: 5rem;"></i>
                </div>
                <h4 class="text-gray-600 mb-3">Aucune commande trouvée</h4>
                <p class="text-gray-500 mb-4">Passez une commande depuis le marketplace pour commencer</p>
                <a href="/marketplace" class="btn btn-outline-success rounded-pill px-4">
                    <i class="bi bi-shop me-2"></i>Visiter le Marketplace
                </a>
            </div>
        </div>
    </section>

    <!-- Received Orders Section -->
    <section class="py-5 bg-gray-50 border-bottom">
        <div class="container px-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-gray-800 mb-3">
                    <i class="bi bi-shop me-2 text-emerald-600"></i>Commandes Reçues
                </h2>
                <p class="lead text-gray-600 mb-0">Gérez les commandes pour vos annonces</p>
            </div>
            
            <!-- Received Orders Grid -->
            <div id="commandesRecuesContainer" class="row gx-4 gy-4">
                <!-- Will be populated by JavaScript -->
            </div>
            
            <!-- Empty State -->
            <div id="commandesRecuesEmptyState" class="text-center py-5" style="display: none;">
                <div class="mb-4">
                    <i class="bi bi-inbox text-gray-400" style="font-size: 5rem;"></i>
                </div>
                <h4 class="text-gray-600 mb-3">Aucune commande reçue</h4>
                <p class="text-gray-500 mb-4">Vos annonces n'ont pas encore reçu de commandes</p>
                <a href="/marketplace" class="btn btn-outline-success rounded-pill px-4">
                    <i class="bi bi-plus-circle me-2"></i>Créer une Annonce
                </a>
            </div>
        </div>
    </section>

    <!-- Update Order Status Modal -->
    <div class="modal fade" id="updateOrderModal" tabindex="-1" aria-labelledby="updateOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-top-3">
                    <h5 class="modal-title fw-bold" id="updateOrderModalLabel">
                        <i class="bi bi-arrow-repeat me-2"></i>Modifier le Statut de la Commande
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateOrderForm">
                    <div class="modal-body p-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="update_commande_id" name="commande_id">
                        <input type="hidden" id="update_user_role" name="user_role">
                        
                        <!-- Order Details Card -->
                        <div class="card border-2 border-green-100 bg-green-50 mb-4 rounded-3">
                            <div class="card-body p-4">
                                <h6 class="card-title fw-bold text-green-800 mb-2">
                                    <i class="bi bi-box me-2"></i>
                                    <span id="update_annonce_title">Détails de la commande</span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong class="text-green-700">Quantité:</strong> 
                                            <span class="badge bg-green-200 text-green-800 px-2 py-1 rounded-pill" id="update_quantite">0</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            <strong class="text-green-700">Prix total:</strong> 
                                            <span class="h5 text-success mb-0 fw-bold" id="update_prix_total">0€</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Selection -->
                        <div class="mb-4">
                            <label for="update_statut" class="form-label fw-semibold">
                                <i class="bi bi-flag me-1"></i>Nouveau Statut
                            </label>
                            <select class="form-select form-select-lg border-2 rounded-2" id="update_statut" name="statut_commande" required>
                                <option value="" disabled selected>Choisissez un statut</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Choisissez le nouveau statut pour cette commande
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-light rounded-2 px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success btn-lg rounded-2 px-4 fw-semibold">
                            <i class="bi bi-check-lg me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Order Confirmation Modal -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-gradient-to-r from-red-500 to-red-600 text-white rounded-top-3">
                    <h5 class="modal-title fw-bold" id="deleteOrderModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirmer la Suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-trash text-red-500" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-center mb-3">Êtes-vous sûr de vouloir Annuler cette commande ?</h6>
                    <p class="text-center text-gray-600 mb-0">
                        Cette action est irréversible. La commande sera définitivement Annulée.
                    </p>
                    <input type="hidden" id="delete_commande_id">
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-2 px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger btn-lg rounded-2 px-4 fw-semibold" onclick="confirmDeleteOrder()">
                        <i class="bi bi-trash me-2"></i>Annuler la Commande
                    </button>
                </div>
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
    console.log('[v0] DOM fully loaded, initializing commandes...');
    
    // Load user's orders
    loadMesCommandes();
    
    // Load received orders
    loadCommandesRecues();
    
    // Setup form handlers
    setupFormHandlers();
});

function loadMesCommandes() {
    console.log('[v0] Loading mes commandes...');
    const container = document.getElementById('mesCommandesContainer');
    const emptyState = document.getElementById('mesCommandesEmptyState');
    
    if (!container || !emptyState) {
        showAlert('Erreur: Éléments d\'interface introuvables.', 'danger');
        return;
    }

    fetch('/mes-commandes', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'include'
    })
    .then(response => {
        console.log('[v0] Mes commandes response status:', response.status);
        if (response.status === 401) {
            showAlert('Veuillez vous connecter pour voir vos commandes. <a href="/login" class="alert-link">Se connecter</a>', 'danger');
            return Promise.reject('Unauthorized');
        }
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[v0] Mes commandes data:', data);
        if (data.error) {
            showAlert(data.error, 'danger');
            return;
        }
        displayMesCommandes(data);
    })
    .catch(error => {
        console.error('Error loading mes commandes:', error);
        if (error !== 'Unauthorized') {
            emptyState.style.display = 'block';
            showAlert('Erreur lors du chargement des commandes: ' + error.message, 'danger');
        }
    });
}

function loadCommandesRecues() {
    console.log('[v0] Loading commandes reçues...');
    const container = document.getElementById('commandesRecuesContainer');
    const emptyState = document.getElementById('commandesRecuesEmptyState');
    
    if (!container || !emptyState) {
        showAlert('Erreur: Éléments d\'interface introuvables.', 'danger');
        return;
    }

    fetch('/commandes-recues', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'include'
    })
    .then(response => {
        console.log('[v0] Commandes reçues response status:', response.status);
        if (response.status === 401) {
            showAlert('Veuillez vous connecter pour voir les commandes reçues. <a href="/login" class="alert-link">Se connecter</a>', 'danger');
            return Promise.reject('Unauthorized');
        }
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('[v0] Commandes reçues data:', data);
        if (data.error) {
            showAlert(data.error, 'danger');
            return;
        }
        displayCommandesRecues(data);
    })
    .catch(error => {
        console.error('Error loading commandes reçues:', error);
        if (error !== 'Unauthorized') {
            emptyState.style.display = 'block';
            showAlert('Erreur lors du chargement des commandes reçues: ' + error.message, 'danger');
        }
    });
}

function displayMesCommandes(commandes) {
    const container = document.getElementById('mesCommandesContainer');
    const emptyState = document.getElementById('mesCommandesEmptyState');
    
    if (!commandes || commandes.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = commandes.map(commande => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden hover-lift">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center p-3">
                    <span class="badge ${getStatusBadgeClass(commande.statut_commande)} px-3 py-2 rounded-pill">${getStatusText(commande.statut_commande)}</span>
                    <small class="text-gray-500 fw-semibold">
                        <i class="bi bi-calendar me-1"></i>
                        ${new Date(commande.date_commande).toLocaleDateString('fr-FR')}
                    </small>
                </div>
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-gray-800 mb-2">${commande.annonce_marketplace?.post_dechet?.titre || 'Titre non disponible'}</h5>
                    <p class="card-text text-gray-600 mb-3">${commande.annonce_marketplace?.post_dechet?.description ? commande.annonce_marketplace.post_dechet.description.substring(0, 100) + '...' : 'Description non disponible'}</p>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-green-50 rounded-2">
                                <div class="h4 text-success mb-0 fw-bold">${commande.prix_total}€</div>
                                <small class="text-green-600 fw-semibold">Prix Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-emerald-50 rounded-2">
                                <div class="h4 text-emerald-600 mb-0 fw-bold">${commande.quantite}</div>
                                <small class="text-emerald-600 fw-semibold">Quantité</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-gray-600 fw-semibold">
                            <i class="bi bi-person me-1"></i>
                            Vendeur: ${commande.annonce_marketplace?.post_dechet?.user?.name || 'Utilisateur'}
                        </small>
                        <small class="text-gray-500">
                            <i class="bi bi-geo-alt me-1"></i>
                            ${commande.annonce_marketplace?.post_dechet?.localisation || 'N/A'}
                        </small>
                    </div>
                    
                    ${getOrderActionButton(commande, 'buyer')}
                </div>
            </div>
        </div>
    `).join('');
}

function displayCommandesRecues(commandes) {
    const container = document.getElementById('commandesRecuesContainer');
    const emptyState = document.getElementById('commandesRecuesEmptyState');
    
    if (!commandes || commandes.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    container.innerHTML = commandes.map(commande => `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden hover-lift">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center p-3">
                    <span class="badge ${getStatusBadgeClass(commande.statut_commande)} px-3 py-2 rounded-pill">${getStatusText(commande.statut_commande)}</span>
                    <small class="text-gray-500 fw-semibold">
                        <i class="bi bi-calendar me-1"></i>
                        ${new Date(commande.date_commande).toLocaleDateString('fr-FR')}
                    </small>
                </div>
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-gray-800 mb-2">${commande.annonce_marketplace?.post_dechet?.titre || 'Titre non disponible'}</h5>
                    <p class="card-text text-gray-600 mb-3">${commande.annonce_marketplace?.post_dechet?.description ? commande.annonce_marketplace.post_dechet.description.substring(0, 100) + '...' : 'Description non disponible'}</p>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-green-50 rounded-2">
                                <div class="h4 text-success mb-0 fw-bold">${commande.prix_total}€</div>
                                <small class="text-green-600 fw-semibold">Revenus</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-emerald-50 rounded-2">
                                <div class="h4 text-emerald-600 mb-0 fw-bold">${commande.quantite}</div>
                                <small class="text-emerald-600 fw-semibold">Quantité</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-gray-600 fw-semibold">
                            <i class="bi bi-person me-1"></i>
                            Acheteur: ${commande.acheteur?.name || 'Utilisateur'}
                        </small>
                        <small class="text-gray-500">
                            <i class="bi bi-geo-alt me-1"></i>
                            ${commande.annonce_marketplace?.post_dechet?.localisation || 'N/A'}
                        </small>
                    </div>
                    
                    ${getOrderActionButton(commande, 'seller')}
                </div>
            </div>
        </div>
    `).join('');
}

function getOrderActionButton(commande, userRole) {
    let buttons = '';
    
    if (userRole === 'buyer') {
        const canDelete = ['en_attente', 'annulee'].includes(commande.statut_commande);
        const canMarkDelivered = commande.statut_commande === 'expediee';
        
        buttons = `
            <div class="d-flex gap-2 justify-content-between align-items-center">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                    <i class="bi bi-eye me-1"></i>Statut: ${getStatusText(commande.statut_commande)}
                </span>
                ${canDelete ? `
                    <button class="btn btn-outline-danger btn-sm rounded-2 fw-semibold hover-scale" 
                            onclick="openDeleteOrderModal(${commande.id})">
                        <i class="bi bi-trash me-1"></i>Annuler la Commande
                    </button>
                ` : ''}
                ${canMarkDelivered ? `
                    <button class="btn btn-outline-success btn-sm rounded-2 fw-semibold hover-scale" 
                            onclick="openUpdateOrderModal(${commande.id}, '${commande.annonce_marketplace?.post_dechet?.titre || 'Titre non disponible'}', ${commande.quantite}, ${commande.prix_total}, '${commande.statut_commande}', 'buyer')">
                        <i class="bi bi-check-circle me-1"></i>Marquer comme livrée
                    </button>
                ` : ''}
            </div>
        `;
    } else if (userRole === 'seller') {
        buttons = `
            <div class="d-flex gap-2 justify-content-between align-items-center">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                    <i class="bi bi-eye me-1"></i>Statut: ${getStatusText(commande.statut_commande)}
                </span>
                <button class="btn btn-outline-primary btn-sm rounded-2 fw-semibold hover-scale" 
                        onclick="openUpdateOrderModal(${commande.id}, '${commande.annonce_marketplace?.post_dechet?.titre || 'Titre non disponible'}', ${commande.quantite}, ${commande.prix_total}, '${commande.statut_commande}', 'seller')">
                    <i class="bi bi-arrow-repeat me-1"></i>Modifier Statut
                </button>
            </div>
        `;
    }
    
    return buttons;
}

function setupFormHandlers() {
    const updateOrderForm = document.getElementById('updateOrderForm');
    
    if (!updateOrderForm) {
        showAlert('Erreur: Formulaire de mise à jour introuvable.', 'danger');
        return;
    }

    updateOrderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const commandeId = document.getElementById('update_commande_id').value;
        const newStatus = document.getElementById('update_statut').value;
        
        console.log('[v0] Updating order:', commandeId, 'to status:', newStatus);
        
        // ✅ CORRECTION: Utiliser FormData au lieu de JSON
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('statut_commande', newStatus);
        
        fetch(`/commandes/${commandeId}`, {
            method: 'POST', // ✅ Utiliser POST avec _method
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'include',
            body: formData
        })
        .then(response => {
            console.log('[v0] Update response status:', response.status);
            if (response.status === 401) {
                showAlert('Veuillez vous connecter pour modifier le statut. <a href="/login" class="alert-link">Se connecter</a>', 'danger');
                return Promise.reject('Unauthorized');
            }
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('[v0] Update response data:', data);
            if (data.error) {
                showAlert(data.error, 'danger');
            } else {
                showAlert('Statut de la commande mis à jour avec succès!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('updateOrderModal')).hide();
                loadMesCommandes();
                loadCommandesRecues();
            }
        })
        .catch(error => {
            console.error('Error updating commande:', error);
            if (error !== 'Unauthorized') {
                showAlert('Erreur lors de la mise à jour du statut: ' + error.message, 'danger');
            }
        });
    });
}

function openDeleteOrderModal(commandeId) {
    const modal = document.getElementById('deleteOrderModal');
    if (!modal) {
        showAlert('Erreur: Modal de suppression introuvable.', 'danger');
        return;
    }
    
    console.log('[v0] Opening delete modal for order:', commandeId);
    document.getElementById('delete_commande_id').value = commandeId;
    new bootstrap.Modal(modal).show();
}

function confirmDeleteOrder() {
    const commandeId = document.getElementById('delete_commande_id').value;
    
    console.log('[v0] Deleting order:', commandeId);
    
    fetch(`/commandes/${commandeId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'include'
    })
    .then(response => {
        console.log('[v0] Delete response status:', response.status);
        if (response.status === 401) {
            showAlert('Veuillez vous connecter pour supprimer la commande. <a href="/login" class="alert-link">Se connecter</a>', 'danger');
            return Promise.reject('Unauthorized');
        }
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('[v0] Delete response data:', data);
        if (data.error) {
            showAlert(data.error, 'danger');
        } else {
            showAlert('Commande supprimée avec succès!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('deleteOrderModal')).hide();
            loadMesCommandes();
            loadCommandesRecues();
        }
    })
    .catch(error => {
        console.error('Error deleting commande:', error);
        if (error !== 'Unauthorized') {
            showAlert('Erreur lors de la suppression: ' + error.message, 'danger');
        }
    });
}

function openUpdateOrderModal(commandeId, titre, quantite, prixTotal, statut, userRole) {
    const modal = document.getElementById('updateOrderModal');
    if (!modal) {
        showAlert('Erreur: Modal de mise à jour introuvable.', 'danger');
        return;
    }
    
    console.log('[v0] Opening update modal for order:', commandeId, 'as', userRole);
    
    document.getElementById('update_commande_id').value = commandeId;
    document.getElementById('update_annonce_title').textContent = titre;
    document.getElementById('update_quantite').textContent = quantite;
    document.getElementById('update_prix_total').textContent = prixTotal + '€';
    document.getElementById('update_user_role').value = userRole;
    
    const statusSelect = document.getElementById('update_statut');
    statusSelect.innerHTML = '<option value="" disabled selected>Choisissez un statut</option>';
    
    if (userRole === 'buyer') {
        // Buyers can only mark as delivered when status is expediee
        if (statut === 'expediee') {
            statusSelect.innerHTML += '<option value="livree">Livrée</option>';
        }
    } else if (userRole === 'seller') {
        // Sellers can confirm, prepare, or ship
        const sellerOptions = [
            { value: 'confirmee', text: 'Confirmée' },
            { value: 'en_preparation', text: 'En préparation' },
            { value: 'expediee', text: 'Expédiée' }
        ];
        sellerOptions.forEach(option => {
            statusSelect.innerHTML += `<option value="${option.value}">${option.text}</option>`;
        });
    }
    
    new bootstrap.Modal(modal).show();
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'en_attente': return 'bg-warning text-dark';
        case 'confirmee': return 'bg-success';
        case 'en_preparation': return 'bg-info';
        case 'expediee': return 'bg-primary';
        case 'livree': return 'bg-success';
        case 'annulee': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'en_attente': return 'En attente';
        case 'confirmee': return 'Confirmée';
        case 'en_preparation': return 'En préparation';
        case 'expediee': return 'Expédiée';
        case 'livree': return 'Livrée';
        case 'annulee': return 'Annulée';
        default: return 'Inconnu';
    }
}

function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        console.error('Alert container not found');
        return;
    }
    
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show shadow-sm rounded-3" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    setTimeout(() => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            alertElement.remove();
        }
    }, 5000);
}

const additionalStyles = `
<style>
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.hover-scale {
    transition: transform 0.2s ease;
}

.hover-scale:hover {
    transform: scale(1.05);
}

.bg-gradient-to-r {
    background: linear-gradient(to right, var(--tw-gradient-stops));
}

.from-green-50 { --tw-gradient-from: #f0fdf4; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(240, 253, 244, 0)); }
.to-emerald-50 { --tw-gradient-to: #ecfdf5; }
.from-green-500 { --tw-gradient-from: #22c55e; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(34, 197, 94, 0)); }
.to-emerald-600 { --tw-gradient-to: #059669; }
.from-red-500 { --tw-gradient-from: #ef4444; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 68, 68, 0)); }
.to-red-600 { --tw-gradient-to: #dc2626; }

.text-green-800 { color: #166534; }
.text-green-700 { color: #15803d; }
.text-green-600 { color: #16a34a; }
.text-emerald-800 { color: #065f46; }
.text-emerald-600 { color: #059669; }
.text-teal-800 { color: #115e59; }
.text-gray-800 { color: #1f2937; }
.text-gray-600 { color: #4b5563; }
.text-gray-500 { color: #6b7280; }
.text-gray-400 { color: #9ca3af; }
.text-red-500 { color: #ef4444; }

.bg-green-100 { background-color: #dcfce7; }
.bg-emerald-100 { background-color: #d1fae5; }
.bg-teal-100 { background-color: #ccfbf1; }
.bg-gray-50 { background-color: #f9fafb; }
.bg-green-50 { background-color: #f0fdf4; }
.bg-emerald-50 { background-color: #ecfdf5; }
.bg-green-200 { background-color: #bbf7d0; }
.border-green-100 { border-color: #dcfce7; }

.rounded-3 { border-radius: 0.75rem; }
.rounded-pill { border-radius: 50rem; }
.fw-bold { font-weight: 700; }
.fw-semibold { font-weight: 600; }
</style>
`;

// Inject additional styles
document.head.insertAdjacentHTML('beforeend', additionalStyles);
</script>
@endpush
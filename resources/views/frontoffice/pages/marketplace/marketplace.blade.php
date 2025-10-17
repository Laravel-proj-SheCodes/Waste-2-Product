@extends('frontoffice.layouts.layoutfront')

@section('content')

<section class="py-5 bg-gradient-to-r from-green-50 to-emerald-50 border-bottom">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-green-800 mb-3">
                <i class="bi bi-recycle me-3 text-green-600"></i>
                Marketplace des Déchets
            </h1>
            <p class="lead text-green-700 mb-0">Transformez vos déchets en opportunités commerciales</p>
            <div class="mt-4">
                <span class="badge bg-green-100 text-green-800 px-3 py-2 rounded-pill me-2">
                    <i class="bi bi-leaf me-1"></i>Écologique
                </span>
                <span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill me-2">
                    <i class="bi bi-currency-euro me-1"></i>Rentable
                </span>
                <span class="badge bg-purple-100 text-purple-800 px-3 py-2 rounded-pill">
                    <i class="bi bi-people me-1"></i>Communautaire
                </span>
            </div>
        </div>
    </div>
</section>

<section class="py-5 border-bottom bg-white">
    <div class="container px-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
                    <div class="card-header bg-gradient-to-r from-green-500 to-emerald-600 text-white py-4">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-plus-circle me-2"></i>Créer une Annonce
                        </h5>
                        <small class="opacity-90">Publiez votre déchet et commencez à vendre</small>
                    </div>
                    <div class="card-body p-4">
                        <form id="createAnnonceForm">
                            @csrf
                            <div class="row gx-4">
                                <div class="col-md-6 mb-4">
                                    <label for="post_dechet_id" class="form-label fw-semibold text-gray-700">
                                        <i class="bi bi-box me-1"></i>Sélectionner votre déchet
                                    </label>
                                    <select class="form-select form-select-lg border-2 rounded-2" id="post_dechet_id" name="post_dechet_id" required>
                                        <option value="">Choisir un déchet...</option>
                                        
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="prix" class="form-label fw-semibold text-gray-700">
                                        <i class="bi bi-currency-euro me-1"></i>Prix (€)
                                    </label>
                                    <input type="number" class="form-control form-control-lg border-2 rounded-2" id="prix" name="prix" step="0.01" required>
                                    <div class="invalid-feedback" id="prix-error"></div>
                                    
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg rounded-2 py-3 fw-semibold">
                                    <i class="bi bi-check-circle me-2"></i>Créer l'Annonce
                                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-5 bg-gray-50">
    <div class="container px-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-gray-800 mb-3">
                <i class="bi bi-person-badge me-2 text-blue-600"></i>Mes Annonces
            </h2>
            <p class="lead text-gray-600 mb-0">Gérez vos annonces marketplace</p>
        </div>
        
         <CHANGE> 
        <div class="row justify-content-center mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <label for="currencySelector" class="form-label fw-semibold text-gray-700 mb-2">
                            <i class="bi bi-currency-exchange me-2"></i>Afficher les prix en:
                        </label>
                        <select class="form-select form-select-lg border-2 rounded-2" id="currencySelector">
                            <option value="EUR" selected>Euro (€)</option>
                            <option value="USD">Dollar US ($)</option>
                            <option value="GBP">Livre Sterling (£)</option>
                            <option value="CHF">Franc Suisse (CHF)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="annoncesContainer" class="row gx-4 gy-4">
        </div>
        
        <div id="emptyState" class="text-center py-5" style="display: none;">
            <div class="mb-4">
                <i class="bi bi-inbox text-gray-400" style="font-size: 5rem;"></i>
            </div>
            <h4 class="text-gray-600 mb-3">Aucune annonce trouvée</h4>
            <p class="text-gray-500 mb-4">Créez votre première annonce pour commencer à vendre vos déchets</p>
            <button class="btn btn-outline-success rounded-pill px-4" onclick="document.getElementById('post_dechet_id').focus()">
                <i class="bi bi-plus-circle me-2"></i>Créer une annonce
            </button>
        </div>
    </div>
</section>

<section class="py-5 border-bottom bg-white">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-gray-800 mb-3">
                <i class="bi bi-shop me-2 text-purple-600"></i>Toutes les Annonces
            </h2>
            <p class="lead text-gray-600 mb-0">Découvrez les opportunités disponibles</p>
        </div>
        
        
        <div class="row mb-5">
            <div class="col-md-4 mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-gray-500"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchFilter" placeholder="Rechercher par titre, localisation...">
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="vendue">Vendu</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-outline-success w-100 rounded-2" id="applyFilters">
                    <i class="bi bi-funnel me-2"></i>Filtrer
                </button>
            </div>
        </div>
        
     
        <div id="allAnnoncesContainer" class="row gx-4 gy-4">
             Will be populated by JavaScript 
        </div>
    </div>
</section>


<div class="modal fade" id="editAnnonceModal" tabindex="-1" aria-labelledby="editAnnonceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-top-3">
                <h5 class="modal-title fw-bold" id="editAnnonceModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modifier l'Annonce
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAnnonceForm">
                <div class="modal-body p-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_annonce_id" name="annonce_id">
                    <div class="mb-4">
                        <label for="edit_prix" class="form-label fw-semibold">
                            <i class="bi bi-currency-euro me-1"></i>Prix (€)
                        </label>
                        <input type="number" class="form-control form-control-lg border-2 rounded-2" id="edit_prix" name="prix" step="0.01" min="0" required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_statut" class="form-label fw-semibold">
                            <i class="bi bi-flag me-1"></i>Statut
                        </label>
                        <select class="form-select form-select-lg border-2 rounded-2" id="edit_statut" name="statut_annonce" required>
                            <option value="active">Actif</option>
                            <option value="inactive">Inactif</option>
                            <option value="vendue">Vendu</option>
                            <option value="expiree">Expiré</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-2 px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success rounded-2 px-4 fw-semibold">
                        <i class="bi bi-check-lg me-2"></i>Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-top-3">
                <h5 class="modal-title fw-bold" id="orderModalLabel">
                    <i class="bi bi-cart-plus me-2"></i>Passer une Commande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="orderForm">
                <div class="modal-body p-4">
                    @csrf
                    <input type="hidden" id="order_annonce_id" name="annonce_marketplace_id">
                    
                   
                    <div class="card border-2 border-green-100 bg-green-50 mb-4 rounded-3">
                        <div class="card-body p-4">
                            <h6 class="card-title fw-bold text-green-800 mb-2" id="order_annonce_title">Titre de l'annonce</h6>
                            <p class="card-text text-green-700 mb-3" id="order_annonce_description">Description</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h4 text-success mb-0 fw-bold" id="order_prix_unitaire">0€</span>
                                <small class="text-green-600 fw-semibold" id="order_localisation">
                                    <i class="bi bi-geo-alt me-1"></i>Localisation
                                </small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-green-200 text-green-800 px-3 py-2 rounded-pill">
                                    <i class="bi bi-box me-1"></i>
                                    Stock: <span id="order_stock_disponible">0</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                     Quantity Input 
                    <div class="mb-4">
                        <label for="order_quantite" class="form-label fw-semibold">
                            <i class="bi bi-123 me-1"></i>Quantité
                        </label>
                        <input type="number" class="form-control form-control-lg border-2 rounded-2" id="order_quantite" name="quantite" min="1" required>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Quantité maximale disponible: <span id="max_quantite" class="fw-semibold">0</span>
                        </div>
                    </div>
                    
                     Total Price 
                    <div class="mb-4">
                        <div class="card bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-blue-800 h5 mb-0">
                                        <i class="bi bi-calculator me-2"></i>Prix total:
                                    </strong>
                                    <strong class="text-success h3 mb-0 fw-bold" id="order_prix_total">0€</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light rounded-2 px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success btn-lg rounded-2 px-4 fw-semibold">
                        <i class="bi bi-cart-check me-2"></i>Confirmer la Commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="alertContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
   
</div>

 
<script>
    // Pass current user ID from Laravel to JavaScript
    window.currentUserId = "{{ auth()->id() ?? '' }}";
</script>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let userAnnonceIds = [];
let selectedCurrency = 'EUR';

document.addEventListener('DOMContentLoaded', function() {
    // Load user's waste posts for the dropdown
    loadUserPostDechets();
    
    // Load user's announcements first to get ownership data
    loadMesAnnonces().then(() => {
        // Then load all announcements with proper ownership detection
        loadAllAnnonces();
    });
    
    // Setup form handlers
    setupFormHandlers();
    
    // Setup filter handlers
    setupFilterHandlers();
    
    setupOrderHandlers();
    setupCurrencySelector();
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let userAnnonceIds = [];
let selectedCurrency = 'EUR';

document.addEventListener('DOMContentLoaded', function() {
    // Load user's waste posts for the dropdown
    loadUserPostDechets();
    
    // Load user's announcements first to get ownership data
    loadMesAnnonces().then(() => {
        // Then load all announcements with proper ownership detection
        loadAllAnnonces();
    });
    
    // Setup form handlers
    setupFormHandlers();
    
    // Setup filter handlers
    setupFilterHandlers();
    
    setupOrderHandlers();
    setupCurrencySelector();
});


function setupFormHandlers() {
    // Create announcement form
    document.getElementById('createAnnonceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const spinner = submitButton.querySelector('.spinner-border');
        
        const prixInput = document.getElementById('prix');
        const prixError = document.getElementById('prix-error');
        prixInput.classList.remove('is-invalid');
        prixError.textContent = '';

        submitButton.disabled = true;
        spinner.classList.remove('d-none');
        
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
            if (response.status === 422) {
                return response.json().then(data => {
                    throw { validationErrors: data.errors };
                });
            }
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
                loadMesAnnonces().then(() => {
                    loadAllAnnonces();
                });
            }
        })
        .catch(error => {
            console.error('Error creating annonce:', error);
            
            if (error.validationErrors) {
                if (error.validationErrors.prix) {
                    prixInput.classList.add('is-invalid');
                    prixError.textContent = error.validationErrors.prix[0];
                }
                if (error.validationErrors.post_dechet_id) {
                    showAlert(error.validationErrors.post_dechet_id[0], 'danger');
                }
            } else {
                showAlert('Erreur lors de la création de l\'annonce', 'danger');
            }
        })
        .finally(() => {
            submitButton.disabled = false;
            spinner.classList.add('d-none');
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
                loadMesAnnonces().then(() => {
                    loadAllAnnonces();
                });
            }
        })
        .catch(error => {
            console.error('Error updating annonce:', error);
            showAlert('Erreur lors de la mise à jour', 'danger');
        });
    });
}

});

function setupCurrencySelector() {
    const currencySelector = document.getElementById('currencySelector');
    if (currencySelector) {
        currencySelector.addEventListener('change', function() {
            selectedCurrency = this.value;
            console.log('[v0] Currency changed to:', selectedCurrency);
            
            // Recharge les deux listes avec la nouvelle devise
            Promise.all([
                loadMesAnnonces(),
                
            ]);
        });
    }
}


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
    const url = `/mes-annonces?to=${selectedCurrency}`;
    
    return fetch(url, {
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
        
        userAnnonceIds = data.map(annonce => annonce.id);
        console.log('[v0] User announcement IDs:', userAnnonceIds);
        console.log('[v0] Loaded announcements with currency:', selectedCurrency);
        
        displayMesAnnonces(data);
    })
    .catch(error => {
        console.error('Error loading mes annonces:', error);
        document.getElementById('emptyState').style.display = 'block';
        showAlert('Erreur lors du chargement des annonces', 'danger');
    });
}

// <CHANGE> Mise à jour de displayMesAnnonces pour afficher le prix converti
function displayMesAnnonces(annonces) {
    const container = document.getElementById('annoncesContainer');
    if (!annonces || annonces.length === 0) {
        container.innerHTML = '';
        document.getElementById('emptyState').style.display = 'block';
        return;
    }

    document.getElementById('emptyState').style.display = 'none';

    container.innerHTML = annonces.map(annonce => {
        const convertedPrice = annonce.converted_price ?? annonce.prix;
        const priceWithCurrency = `${Number(convertedPrice).toFixed(2)} ${getCurrencySymbol(selectedCurrency)}`;
        const hasOrders = annonce.commandes && annonce.commandes.length > 0;

        return `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden hover-lift">
                    <div class="card-header bg-white border-0 p-3">
                        <span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill">
                            <i class="bi bi-person me-1"></i>Votre annonce
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-gray-800 mb-2">${annonce.post_dechet?.titre || 'Titre non disponible'}</h5>
                        <p class="card-text text-gray-600 mb-3">
                            ${annonce.post_dechet?.description ? annonce.post_dechet.description.substring(0,100)+'...' : ''}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h4 text-success mb-0 fw-bold">${priceWithCurrency}</span>
                            <small class="text-gray-500 fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>${annonce.post_dechet?.localisation || ''}
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm rounded-2 w-100" 
                                onclick="editAnnonce(${annonce.id}, ${annonce.prix}, '${annonce.statut_annonce}')">
                                <i class="bi bi-pencil-square me-1"></i>Modifier
                            </button>

                            ${!hasOrders ? `
                                <button class="btn btn-outline-danger btn-sm rounded-2 w-100" 
                                    onclick="deleteAnnonce(${annonce.id})">
                                    <i class="bi bi-trash3 me-1"></i>Supprimer
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}



// <CHANGE> Nouvelle fonction pour obtenir le symbole de devise
function getCurrencySymbol(currency) {
    const symbols = {
        'EUR': '€',
        'USD': '$',
        'GBP': '£',
        'CHF': 'CHF'
    };
    return symbols[currency] || currency;
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



function displayAllAnnonces(annonces) {
    const container = document.getElementById('allAnnoncesContainer');
    
    if (!annonces || annonces.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-gray-400" style="font-size: 4rem;"></i>
                <h4 class="text-gray-600 mt-3">Aucune annonce disponible</h4>
                <p class="text-gray-500">Essayez de modifier vos filtres de recherche</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = annonces.map(annonce => {
        const isOwnAnnouncement = userAnnonceIds.includes(annonce.id);
        
        return `
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden hover-lift">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge ${getStatusBadgeClass(annonce.statut_annonce)} px-3 py-2 rounded-pill">${getStatusText(annonce.statut_annonce)}</span>
                            ${isOwnAnnouncement ? '<span class="badge bg-blue-100 text-blue-800 px-3 py-2 rounded-pill"><i class="bi bi-person me-1"></i>Votre annonce</span>' : ''}
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-gray-800 mb-2">${annonce.post_dechet?.titre || 'Titre non disponible'}</h5>
                        <p class="card-text text-gray-600 mb-3">${annonce.post_dechet?.description ? annonce.post_dechet.description.substring(0, 100) + '...' : ''}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h4 text-success mb-0 fw-bold">${annonce.prix}€</span>
                            <small class="text-gray-500 fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>${annonce.post_dechet?.localisation || ''}
                            </small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <small class="text-gray-600 fw-semibold">
                                <i class="bi bi-person me-1"></i>
                                Par: ${annonce.post_dechet?.user?.name || 'Utilisateur'}
                            </small>
                            <span class="badge bg-gray-100 text-gray-700 px-2 py-1 rounded-pill small">
                                <i class="bi bi-box me-1"></i>
                                Stock: ${annonce.post_dechet?.quantite || 0}
                            </span>
                        </div>
                        <div class="d-grid">
                            ${getActionButton(annonce, isOwnAnnouncement)}
                        </div>
                    </div>
                    <div class="card-footer bg-gray-50 border-0 p-3">
                        <small class="text-gray-500">
                            <i class="bi bi-calendar me-1"></i>
                            Créé le ${new Date(annonce.created_at).toLocaleDateString('fr-FR')}
                        </small>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function getActionButton(annonce, isOwnAnnouncement) {
    if (isOwnAnnouncement) {
        return `
            <button class="btn btn-outline-primary btn-sm rounded-2" disabled>
                <i class="bi bi-person-check me-1"></i>Votre annonce
            </button>
        `;
    }
    
    if (annonce.statut_annonce === 'active') {
        return `
            <button class="btn btn-success btn-sm rounded-2 fw-semibold hover-scale" onclick="openOrderModal(${annonce.id})">
                <i class="bi bi-cart-plus me-1"></i>Commander
            </button>
        `;
    }
    
    return `
        <button class="btn btn-secondary btn-sm rounded-2" disabled>
            <i class="bi bi-x-circle me-1"></i>Non disponible
        </button>
    `;
}

function setupFormHandlers() {
    // Create announcement form
    document.getElementById('createAnnonceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const spinner = submitButton.querySelector('.spinner-border');

        submitButton.disabled = true;
        spinner.classList.remove('d-none');
        
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
                loadMesAnnonces().then(() => {
                    loadAllAnnonces();
                });
            }
        })
        .catch(error => {
            console.error('Error creating annonce:', error);
            showAlert('Erreur lors de la création de l\'annonce', 'danger');
        })
        .finally(() => {
            submitButton.disabled = false;
            spinner.classList.add('d-none');
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
                loadMesAnnonces().then(() => {
                    loadAllAnnonces();
                });
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

function setupOrderHandlers() {
    // Order form submission
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Debug log to check form data
        console.log('[v0] Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(`[v0] ${key}: ${value}`);
        }
        
        fetch('/commandes', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('[v0] Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('[v0] Error response:', text);
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('[v0] Success response:', data);
            if (data.error) {
                showAlert(data.error, 'danger');
            } else {
                showAlert('Commande passée avec succès! Vous pouvez suivre son statut dans <a href="/commandes-page" class="alert-link">vos commandes</a>.', 'success');
                bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
                // Refresh announcements to update stock
                loadAllAnnonces();
            }
        })
        .catch(error => {
            console.error('Error creating order:', error);
            showAlert('Erreur lors de la création de la commande: ' + error.message, 'danger');
        });
    });
    
    // Quantity change handler for price calculation
    document.getElementById('order_quantite').addEventListener('input', function() {
        calculateTotalPrice();
    });
}

function openOrderModal(annonceId) {
    // Fetch annonce details
    fetch(`/annonces/${annonceId}`, {
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
    .then(annonce => {
        if (annonce.error) {
            showAlert(annonce.error, 'danger');
            return;
        }
        
        // Populate modal with annonce details
        document.getElementById('order_annonce_id').value = annonce.id;
        document.getElementById('order_annonce_title').textContent = annonce.post_dechet?.titre || 'Titre non disponible';
        document.getElementById('order_annonce_description').textContent = annonce.post_dechet?.description || 'Description non disponible';
        document.getElementById('order_prix_unitaire').textContent = annonce.prix + '€';
        document.getElementById('order_localisation').innerHTML = '<i class="bi bi-geo-alt me-1"></i>' + (annonce.post_dechet?.localisation || 'Localisation non disponible');
        document.getElementById('order_stock_disponible').textContent = annonce.post_dechet?.quantite || 0;
        document.getElementById('max_quantite').textContent = annonce.post_dechet?.quantite || 0;
        
        // Set max quantity for input
        const quantiteInput = document.getElementById('order_quantite');
        quantiteInput.max = annonce.post_dechet?.quantite || 0;
        quantiteInput.value = 1;
        
        // Store price for calculation
        quantiteInput.dataset.prixUnitaire = annonce.prix;
        
        // Calculate initial total
        calculateTotalPrice();
        
        // Show modal
        new bootstrap.Modal(document.getElementById('orderModal')).show();
    })
    .catch(error => {
        console.error('Error loading annonce details:', error);
        showAlert('Erreur lors du chargement des détails de l\'annonce', 'danger');
    });
}

function calculateTotalPrice() {
    const quantiteInput = document.getElementById('order_quantite');
    const quantite = parseInt(quantiteInput.value) || 0;
    const prixUnitaire = parseFloat(quantiteInput.dataset.prixUnitaire) || 0;
    const total = quantite * prixUnitaire;
    
    document.getElementById('order_prix_total').textContent = total.toFixed(2) + '€';
}

function editAnnonce(id, prix, statut) {
    document.getElementById('edit_annonce_id').value = id;
    document.getElementById('edit_prix').value = prix;
    document.getElementById('edit_statut').value = statut;
    
    new bootstrap.Modal(document.getElementById('editAnnonceModal')).show();
}

function deleteAnnonce(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette annonce?')) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/annonces/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Erreur inconnue');
        return data;
    })
    .then(data => {
        showAlert(data.message || 'Annonce supprimée avec succès!', 'success');
        loadMesAnnonces().then(() => loadAllAnnonces());
    })
    .catch(error => {
        console.error('Error deleting annonce:', error);
        showAlert(error.message || 'Erreur lors de la suppression', 'danger');
    });
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
.from-blue-500 { --tw-gradient-from: #3b82f6; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(59, 130, 246, 0)); }
.to-blue-600 { --tw-gradient-to: #2563eb; }
.from-blue-50 { --tw-gradient-from: #eff6ff; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(239, 246, 255, 0)); }
.to-indigo-50 { --tw-gradient-to: #eef2ff; }

.text-green-800 { color: #166534; }
.text-green-700 { color: #15803d; }
.text-green-600 { color: #16a34a; }
.text-blue-600 { color: #2563eb; }
.text-purple-600 { color: #9333ea; }
.text-gray-800 { color: #1f2937; }
.text-gray-700 { color: #374151; }
.text-gray-600 { color: #4b5563; }
.text-gray-500 { color: #6b7280; }
.text-gray-400 { color: #9ca3af; }

.bg-green-100 { background-color: #dcfce7; }
.bg-blue-100 { background-color: #dbeafe; }
.bg-purple-100 { background-color: #e9d5ff; }
.bg-gray-50 { background-color: #f9fafb; }
.bg-gray-100 { background-color: #f3f4f6; }
.bg-green-50 { background-color: #f0fdf4; }
.bg-green-200 { background-color: #bbf7d0; }
.bg-blue-200 { background-color: #bfdbfe; }

.border-green-100 { border-color: #dcfce7; }
.border-blue-200 { border-color: #bfdbfe; }

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

@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <div class="mb-3">
                <a href="{{ route('annonces.index') }}" class="btn btn-sm btn-outline-dark">
                    <i class="material-symbols-rounded">arrow_back</i>
                    Back to Announcements
                </a>
            </div>
<br>
            <div class="card mb-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Announcement Details</h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-2">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-1">Title</p>
                            <h6 class="mb-0">{{ 
                                $annonce->postDechet->titre ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-1">Price</p>
                            <h6 class="mb-0">{{ number_format($annonce->prix, 2) }} DH</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-1">Quantity</p>
                            <h6 class="mb-0">{{ $annonce->postDechet->quantite ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-1">Status</p>
                            @php
                                $badgeClass = match($annonce->statut_annonce) {
                                    'active' => 'bg-gradient-success',
                                    'inactive' => 'bg-gradient-secondary',
                                    'vendue' => 'bg-gradient-info',
                                    'expiree' => 'bg-gradient-danger',
                                    default => 'bg-gradient-warning'
                                };
                            @endphp
                            <span class="badge badge-sm {{ $badgeClass }}">{{ ucfirst($annonce->statut_annonce) }}</span>
                        </div>
                    </div>
                </div>
            </div>
<br>
            <div class="card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">
                            Orders ({{ $annonce->commandes->count() }})
                        </h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Buyer</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Quantity</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Price</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($annonce->commandes as $commande)
                                    <tr class="order-row" 
                                        style="cursor: pointer; transition: background-color 0.2s;"
                                        onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                        onmouseout="this.style.backgroundColor=''"
                                        onclick="openOrderModal('{{ $commande->statut_commande }}')">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">#{{ $commande->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('assets-backoffice/img/default-avatar.png') }}" 
                                                         class="avatar avatar-sm me-3 border-radius-lg" 
                                                         alt="buyer" 
                                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM5OTk5OTkiLz4KPC9zdmc+';">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $commande->acheteur->name ?? 'N/A' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $commande->acheteur->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $commande->quantite }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($commande->prix_total, 2) }} DH</span>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @php
                                                $statusBadgeClass = match($commande->statut_commande) {
                                                    'en_attente' => 'bg-gradient-warning',
                                                    'confirmee' => 'bg-gradient-success',
                                                    'en_preparation' => 'bg-gradient-info',
                                                    'expediee' => 'bg-gradient-primary',
                                                    'livree' => 'bg-gradient-success',
                                                    'annulee' => 'bg-gradient-danger',
                                                    default => 'bg-gradient-secondary'
                                                };
                                            @endphp
                                            <span class="badge badge-sm {{ $statusBadgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $commande->statut_commande)) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ $commande->created_at?->format('d/m/Y H:i') ?? '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-secondary">
                                                <p class="text-sm font-weight-normal mb-0">No orders found for this announcement.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Order Status Pipeline Modal --}}
<div class="modal fade" id="orderStatusModal" tabindex="-1" aria-labelledby="orderStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="orderStatusModalLabel">
                    <i class="material-symbols-rounded me-2" style="vertical-align: middle;">local_shipping</i>
                    Order Status Pipeline
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Horizontal Journey-Style Pipeline --}}
                <h6 class="mb-4 text-center">Order Journey</h6>
                <div class="journey-pipeline px-4 py-3">
                    <div class="journey-container">
                        {{-- Pending --}}
                        <div class="journey-step" data-status="en_attente">
                            <div class="step-icon-wrapper">
                                <div class="step-icon bg-warning">
                                    <i class="material-symbols-rounded text-white">schedule</i>
                                </div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step-label">
                                <h6 class="mb-0 text-xs font-weight-bold">Pending</h6>
                                <p class="text-xxs text-secondary mb-0 mt-1">Awaiting</p>
                            </div>
                        </div>

                        {{-- Confirmed --}}
                        <div class="journey-step" data-status="confirmee">
                            <div class="step-icon-wrapper">
                                <div class="step-icon bg-secondary">
                                    <i class="material-symbols-rounded text-white">check_circle</i>
                                </div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step-label">
                                <h6 class="mb-0 text-xs font-weight-bold">Confirmed</h6>
                                <p class="text-xxs text-secondary mb-0 mt-1">Accepted</p>
                            </div>
                        </div>

                        {{-- In Preparation --}}
                        <div class="journey-step" data-status="en_preparation">
                            <div class="step-icon-wrapper">
                                <div class="step-icon bg-secondary">
                                    <i class="material-symbols-rounded text-white">inventory_2</i>
                                </div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step-label">
                                <h6 class="mb-0 text-xs font-weight-bold">Preparing</h6>
                                <p class="text-xxs text-secondary mb-0 mt-1">Packing</p>
                            </div>
                        </div>

                        {{-- Shipped --}}
                        <div class="journey-step" data-status="expediee">
                            <div class="step-icon-wrapper">
                                <div class="step-icon bg-secondary">
                                    <i class="material-symbols-rounded text-white">local_shipping</i>
                                </div>
                                <div class="step-connector"></div>
                            </div>
                            <div class="step-label">
                                <h6 class="mb-0 text-xs font-weight-bold">Shipped</h6>
                                <p class="text-xxs text-secondary mb-0 mt-1">In Transit</p>
                            </div>
                        </div>

                        {{-- Delivered --}}
                        <div class="journey-step" data-status="livree">
                            <div class="step-icon-wrapper">
                                <div class="step-icon bg-secondary">
                                    <i class="material-symbols-rounded text-white">done_all</i>
                                </div>
                            </div>
                            <div class="step-label">
                                <h6 class="mb-0 text-xs font-weight-bold">Delivered</h6>
                                <p class="text-xxs text-secondary mb-0 mt-1">Complete</p>
                            </div>
                        </div>
                    </div>

                    {{-- Cancelled (shown separately when applicable) --}}
                    <div class="cancelled-notice mt-4 p-3 bg-light border-radius-lg" id="cancelled-notice" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="step-icon bg-danger me-3">
                                <i class="material-symbols-rounded text-white">cancel</i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-sm font-weight-bold text-danger">Order Cancelled</h6>
                                <p class="text-xs text-secondary mb-0 mt-1">This order has been cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Horizontal Journey Pipeline Styles */
.journey-pipeline {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 12px;
    overflow-x: auto;
}

.journey-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    min-width: 600px;
    padding: 20px 0;
}

.journey-step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.step-icon-wrapper {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: center;
    position: relative;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.step-icon i {
    font-size: 24px;
}

.step-connector {
    flex: 1;
    height: 4px;
    background-color: #dee2e6;
    position: relative;
    margin-left: 8px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.journey-step:last-child .step-connector {
    display: none;
}

.step-label {
    text-align: center;
    margin-top: 12px;
    max-width: 100px;
}

.step-label h6 {
    color: #344767;
}

/* Active/Completed States */
.journey-step.completed .step-icon {
    background: linear-gradient(135deg, #66bb6a 0%, #43a047 100%) !important;
    transform: scale(1.1);
    box-shadow: 0 6px 12px rgba(67, 160, 71, 0.3);
}

.journey-step.completed .step-connector {
    background: linear-gradient(90deg, #66bb6a 0%, #43a047 100%);
    height: 4px;
}

.journey-step.current .step-icon {
    background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%) !important;
    transform: scale(1.2);
    box-shadow: 0 8px 16px rgba(94, 114, 228, 0.4);
    animation: pulse 2s infinite;
}

.journey-step.current .step-label h6 {
    color: #5e72e4;
    font-weight: 700;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 8px 16px rgba(94, 114, 228, 0.4);
    }
    50% {
        box-shadow: 0 8px 20px rgba(94, 114, 228, 0.6);
    }
}

.journey-step.pending .step-icon {
    background-color: #e9ecef !important;
    opacity: 0.6;
}

.journey-step.pending .step-label {
    opacity: 0.5;
}

/* Cancelled Notice */
.cancelled-notice .step-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
}

.cancelled-notice .step-icon i {
    font-size: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .journey-container {
        min-width: 500px;
    }
    
    .step-icon {
        width: 40px;
        height: 40px;
    }
    
    .step-icon i {
        font-size: 20px;
    }
    
    .step-label {
        max-width: 80px;
    }
}
</style>

<script>
function openOrderModal(status) {
    // Update pipeline
    updatePipeline(status);
    
    // Open modal
    const modal = new bootstrap.Modal(document.getElementById('orderStatusModal'));
    modal.show();
}

function updatePipeline(currentStatus) {
    const statuses = ['en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree'];
    const steps = document.querySelectorAll('.journey-step');
    const cancelledNotice = document.getElementById('cancelled-notice');
    
    // Handle cancelled status
    if (currentStatus === 'annulee') {
        steps.forEach(step => {
            const stepStatus = step.getAttribute('data-status');
            if (stepStatus === 'en_attente') {
                step.classList.add('completed');
                step.classList.remove('current', 'pending');
            } else {
                step.classList.add('pending');
                step.classList.remove('completed', 'current');
            }
        });
        cancelledNotice.style.display = 'block';
        return;
    }
    
    // Hide cancelled notice for normal orders
    cancelledNotice.style.display = 'none';
    
    const currentIndex = statuses.indexOf(currentStatus);
    
    steps.forEach(step => {
        const stepStatus = step.getAttribute('data-status');
        const stepIndex = statuses.indexOf(stepStatus);
        
        // Remove all state classes
        step.classList.remove('completed', 'current', 'pending');
        
        if (stepIndex < currentIndex) {
            // Completed step
            step.classList.add('completed');
        } else if (stepIndex === currentIndex) {
            // Current step
            step.classList.add('current');
        } else {
            // Pending/future step
            step.classList.add('pending');
        }
    });
}
</script>
@endsection
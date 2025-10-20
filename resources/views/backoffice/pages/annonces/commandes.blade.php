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
{{-- Analytics Dashboard Section --}}
<div class="row mb-4">
    {{-- Total Orders --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Total Orders</p>
                        <h5 class="font-weight-bolder text-dark mb-0">
                            {{ $analytics['total_orders'] }}
                        </h5>
                        <p class="mb-0 text-xs">
                            <span class="text-success font-weight-bold">
                                {{ $analytics['delivered_orders'] }}
                            </span> delivered
                        </p>
                    </div>
                    <div class="icon icon-shape bg-gradient-primary text-center rounded-circle shadow">
                        <i class="material-symbols-rounded text-white">shopping_cart</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Total Revenue</p>
                        <h5 class="font-weight-bolder text-success mb-0">
                            {{ number_format($analytics['total_revenue'], 2) }} €
                        </h5>
                        <p class="mb-0 text-xs">
                            <span class="text-secondary font-weight-bold">
                                Avg: {{ number_format($analytics['avg_revenue'], 2) }} €
                            </span>
                        </p>
                    </div>
                    <div class="icon icon-shape bg-gradient-success text-center rounded-circle shadow">
                        <i class="material-symbols-rounded text-white">payments</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Quantity Sold --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Total Quantity</p>
                        <h5 class="font-weight-bolder text-info mb-0">
                            {{ $analytics['total_quantity'] }}
                        </h5>
                        <p class="mb-0 text-xs">
                            <span class="text-secondary font-weight-bold">
                                {{ number_format($analytics['avg_quantity'], 1) }} avg/order
                            </span>
                        </p>
                    </div>
                    <div class="icon icon-shape bg-gradient-info text-center rounded-circle shadow">
                        <i class="material-symbols-rounded text-white">inventory</i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Orders --}}
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Pending Orders</p>
                        <h5 class="font-weight-bolder text-warning mb-0">
                            {{ $analytics['pending_orders'] }}
                        </h5>
                        <p class="mb-0 text-xs">
                            <span class="text-danger font-weight-bold">
                                {{ $analytics['cancelled_orders'] }}
                            </span> cancelled
                        </p>
                    </div>
                    <div class="icon icon-shape bg-gradient-warning text-center rounded-circle shadow">
                        <i class="material-symbols-rounded text-white">schedule</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4">
    {{-- Order Status Distribution --}}
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0">
                <h6>Order Status Distribution</h6>
                <p class="text-sm mb-0">Current orders by status</p>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Over Time --}}
    <div class="col-lg-8 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0">
                <h6>Revenue Trend</h6>
                <p class="text-sm mb-0">Daily revenue for the last 7 days</p>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Top Buyers and Recent Activity Row --}}
<div class="row mb-4">
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0">
                <h6>Top Buyers</h6>
                <p class="text-sm mb-0">Customers with highest order values</p>
            </div>
            <div class="card-body p-3">
                <ul class="list-group">
                    @forelse($analytics['top_buyers'] as $buyerData)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                                    <i class="material-symbols-rounded text-white opacity-10">person</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">{{ $buyerData['buyer']->name ?? 'N/A' }}</h6>
                                    <span class="text-xs">{{ $buyerData['orders'] }} order(s)</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-sm font-weight-bold">
                                {{ number_format($buyerData['total'], 2) }} €
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item border-0 text-center text-secondary">
                            No buyers yet
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- Order Timeline --}}
    <div class="col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header pb-0">
                <h6>Recent Activity</h6>
                <p class="text-sm mb-0">Latest order updates</p>
            </div>
            <div class="card-body p-3">
                <div class="timeline timeline-one-side">
                    @forelse($annonce->commandes->sortByDesc('created_at')->take(5) as $commande)
                        @php
                            $statusColor = match($commande->statut_commande) {
                                'livree' => 'success',
                                'annulee' => 'danger',
                                'expediee' => 'primary',
                                'en_preparation' => 'info',
                                default => 'warning'
                            };
                            
                            $statusIcon = match($commande->statut_commande) {
                                'livree' => 'check_circle',
                                'annulee' => 'cancel',
                                'expediee' => 'local_shipping',
                                'en_preparation' => 'inventory_2',
                                default => 'schedule'
                            };
                        @endphp
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="material-symbols-rounded text-{{ $statusColor }} text-gradient">
                                    {{ $statusIcon }}
                                </i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    Order #{{ $commande->id }} - {{ ucfirst(str_replace('_', ' ', $commande->statut_commande)) }}
                                </h6>
                                <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                    {{ $commande->acheteur->name ?? 'N/A' }} • {{ number_format($commande->prix_total, 2) }} €
                                </p>
                                <p class="text-secondary font-weight-bold text-xs mb-0">
                                    {{ $commande->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-secondary">
                            <p class="text-sm mb-0">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

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
                            <h6 class="mb-0">{{ number_format($annonce->prix, 2) }} €</h6>
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
                    <div class="d-flex justify-content-end pe-4 mb-3">
                     <a href="{{ route('annonces.exportOrders', $annonce->id) }}" 
                       class="btn btn-sm btn-success">
                    <i class="material-symbols-rounded me-1">download</i> 
                           Export Orders CSV
                          </a>
                      </div>
                      
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
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($commande->prix_total, 2) }} €</span>
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
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statusData = @json($analytics['status_distribution']);
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Confirmed', 'Preparing', 'Shipped', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [
                        statusData.en_attente,
                        statusData.confirmee,
                        statusData.en_preparation,
                        statusData.expediee,
                        statusData.livree,
                        statusData.annulee
                    ],
                    backgroundColor: [
                        '#fb8c00',
                        '#66bb6a',
                        '#29b6f6',
                        '#5e72e4',
                        '#43a047',
                        '#ef5350'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($analytics['daily_revenue']);
        const labels = Object.keys(revenueData).slice(-7);
        const data = Object.values(revenueData).slice(-7);
        
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Revenue (€)',
                    data: data,
                    borderColor: '#43a047',
                    backgroundColor: 'rgba(67, 160, 71, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#43a047',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
@extends('backoffice.layouts.layout')

@section('content')
<!-- Stats Overview Table: Normal vs Advanced -->
<div class="container-fluid py-4">
    <!-- Two main columns side-by-side -->
    <div class="row g-4 align-items-stretch">
        <!-- ===== Donations per Status (Left Column) ===== -->
        <div class="col-lg-6 col-md-12 d-flex">
            <div class="card shadow-sm border-0 flex-fill">
                <div class="card-header bg-gradient-light p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark text-uppercase fw-bold mb-0">Donations per Status</h6>
                        <p class="text-xs text-secondary mb-0">Overview of current donation statuses</p>
                    </div>
                    <span class="badge bg-gradient-dark text-white px-3 py-2" id="stats-total">Total Donations: {{ $stats['total'] ?? 0 }}</span>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="row g-3 flex-grow-1">
                        <!-- Pending -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Pending</p>
                                        <h5 class="fw-bold text-warning" id="stats-pending">{{ $stats['pending'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-warning">
                                        <i class="material-symbols-rounded">schedule</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Accepted -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Accepted</p>
                                        <h5 class="fw-bold text-success" id="stats-accepted">{{ $stats['accepted'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-success">
                                        <i class="material-symbols-rounded">check_circle</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Rejected -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Rejected</p>
                                        <h5 class="fw-bold text-danger" id="stats-rejected">{{ $stats['rejected'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-danger">
                                        <i class="material-symbols-rounded">cancel</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Taken -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Taken</p>
                                        <h5 class="fw-bold text-info" id="stats-taken">{{ $stats['taken'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-info">
                                        <i class="material-symbols-rounded">shopping_bag</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ===== Advanced Analytics (Right Column) ===== -->
        <div class="col-lg-6 col-md-12 d-flex">
            <div class="card shadow-sm border-0 flex-fill">
                <div class="card-header bg-gradient-light p-3">
                    <div>
                        <h6 class="text-dark text-uppercase fw-bold mb-0">Advanced Analytics</h6>
                        <p class="text-xs text-secondary mb-0">Deeper metrics for trend and performance tracking</p>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="row g-3 flex-grow-1">
                        <!-- Total Quantity -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Total Quantity</p>
                                        <h5 class="fw-bold" id="advanced-total-quantity">{{ $advancedStats['total_quantity'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-primary">
                                        <i class="material-symbols-rounded">stack</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Avg Quantity -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Avg Quantity</p>
                                        <h5 class="fw-bold text-secondary" id="advanced-avg-quantity">{{ $advancedStats['average_quantity'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-secondary">
                                        <i class="material-symbols-rounded">trending_up</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Unique Donors -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Unique Donors</p>
                                        <h5 class="fw-bold text-success" id="advanced-unique-donors">{{ $advancedStats['unique_donors'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-success">
                                        <i class="material-symbols-rounded">people</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Completion Rate -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Completion Rate</p>
                                        <h5 class="fw-bold text-info" id="advanced-completion-rate">{{ $advancedStats['completion_rate'] ?? 0 }}%</h5>
                                    </div>
                                    <div class="icon bg-gradient-info">
                                        <i class="material-symbols-rounded">check_box</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Avg Days -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Avg Days</p>
                                        <h5 class="fw-bold text-warning" id="advanced-avg-days">{{ $advancedStats['average_days_to_completion'] ?? 0 }}</h5>
                                    </div>
                                    <div class="icon bg-gradient-warning">
                                        <i class="material-symbols-rounded">schedule</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Type Distribution -->
                        <div class="col-sm-6">
                            <div class="stat-card h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-xs text-muted mb-0">Type Distribution</p>
                                        <h5 class="fw-bold" id="advanced-type-dist">
                                            <span class="badge bg-gradient-success">♻️ {{ $advancedStats['recyclable_count'] ?? 0 }}</span>
                                            <span class="badge bg-gradient-info">🌿 {{ $advancedStats['renewable_count'] ?? 0 }}</span>
                                        </h5>
                                    </div>
                                    <div class="icon bg-gradient-dark">
                                        <i class="material-symbols-rounded">pie_chart</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 10px;
        color: white;
    }
    .card-header {
        border-bottom: 1px solid #eee;
    }
    .badge {
        font-size: 0.8rem;
    }
    /* === Equal height fix === */
    .row.g-4 {
        display: flex;
        align-items: stretch;
    }
    .row.g-4 > [class*="col-"] > .card {
        height: 100%;
    }
    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Donations Table</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible text-white mx-4" role="alert">
                            <span class="text-sm">{{ session('success') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible text-white mx-4" role="alert">
                            <span class="text-sm">{{ session('error') }}</span>
                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="px-4 pb-2">
                        <form id="filter-form" class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-symbols-rounded">search</i></span>
                                    <input type="text" name="search" id="search-input" class="form-control" placeholder="Search by product name" value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" id="type-select" class="form-select">
                                    <option value="all" {{ request('type') === 'all' || !request('type') ? 'selected' : '' }}>All Types</option>
                                    <option value="recyclable" {{ request('type') === 'recyclable' ? 'selected' : '' }}>♻️ Recyclable donations</option>
                                    <option value="renewable" {{ request('type') === 'renewable' ? 'selected' : '' }}>🌿 Renewable donations</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <button type="button" id="clear-btn" class="btn bg-gradient-secondary mb-0">Clear Filters</button>
                                <a href="{{ route('donations.exportPdf') }}" class="btn bg-gradient-success mb-0 ms-2">Export to PDF</a>
                            </div>
                        </form>
                        <a href="{{ route('donations.create') }}" class="btn bg-gradient-dark mb-0 mt-3">Add Donation</a>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="donations-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Donor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Product</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Location</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="donations-table-body">
                                @forelse ($donations as $donation)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-sm font-weight-bold mb-0">{{ $donation->user->name ?? 'Anonymous' }}</p>
                                                    <p class="text-xs text-muted mb-0">{{ $donation->user->email ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $donation->product_name }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $donation->quantity }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ ucfirst($donation->type) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $donation->location }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $donation->donation_date }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @if ($donation->status === 'accepted')
                                                <span class="badge badge-sm bg-gradient-success">{{ ucfirst($donation->status) }}</span>
                                            @elseif ($donation->status === 'rejected')
                                                <span class="badge badge-sm bg-gradient-danger">{{ ucfirst($donation->status) }}</span>
                                            @elseif ($donation->status === 'taken')
                                                <span class="badge badge-sm bg-gradient-info">{{ ucfirst($donation->status) }}</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">{{ ucfirst($donation->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('donations.show', $donation) }}" class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="View Donation">
                                                <i class="material-symbols-rounded">visibility</i>
                                            </a>
                                            <a href="{{ route('donations.backedit', $donation) }}" class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Edit Donation">
                                                <i class="material-symbols-rounded">edit</i>
                                            </a>
                                            <a href="{{ route('donations.showRequests', $donation) }}" class="btn btn-link text-primary text-sm mb-0" data-toggle="tooltip" data-original-title="View Requests">
                                                <i class="material-symbols-rounded">group</i>
                                            </a>
                                            <form action="{{ route('donations.destroy', $donation) }}" method="POST" class="delete-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn" data-donation-id="{{ $donation->id }}">
                                                    <i class="material-symbols-rounded">delete</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-sm py-3">No donations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4" id="pagination-container">
                        {{ $donations->appends(['search' => request('search'), 'type' => request('type')])->links('vendor.pagination.material-dashboard') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .table th:nth-child(1) {
        width: 25%; /* Donor */
    }
    .table th:nth-child(2) {
        width: 20%; /* Product */
    }
    .table th:nth-child(3) {
        width: 10%; /* Quantity */
    }
    .table th:nth-child(4) {
        width: 10%; /* Type */
    }
    .table th:nth-child(5) {
        width: 15%; /* Location */
    }
    .table th:nth-child(6) {
        width: 10%; /* Date */
    }
    .table th:nth-child(7) {
        width: 10%; /* Status */
    }
    .table th:nth-child(8) {
        width: 20%; /* Actions */
    }
    .table td:nth-child(1) p.text-xs {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
    .form-select, .form-control {
        border-radius: 0.375rem;
    }
    #loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let debounceTimeout;

        // Function to fetch donations via AJAX
        function fetchDonations(search = '', type = 'all', page = 1) {
            document.getElementById('loading-spinner')?.remove();

            const spinner = document.createElement('div');
            spinner.id = 'loading-spinner';
            spinner.innerHTML = '<i class="material-symbols-rounded">refresh</i> Loading...';
            document.getElementById('donations-table').after(spinner);

            const params = new URLSearchParams({
                search: search,
                type: type,
                page: page,
                ajax: true
            });

            fetch(`{{ route('donations.index') }}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                updateTable(data.donations.data);
                updateStats(data.stats, data.advancedStats);
                updatePagination(data.donations);
                spinner.remove();
            })
            .catch(error => {
                console.error('Error fetching donations:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load donations. Please try again. Status: ' + error.message,
                });
                spinner.remove();
            });
        }

        // Function to update table content
        function updateTable(donations) {
            const tbody = document.getElementById('donations-table-body');
            tbody.innerHTML = '';
            if (donations.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-sm py-3">No donations found.</td></tr>';
                return;
            }
            donations.forEach(donation => {
                const statusClass = {
                    'accepted': 'bg-gradient-success',
                    'rejected': 'bg-gradient-danger',
                    'taken': 'bg-gradient-info',
                    'pending': 'bg-gradient-warning'
                }[donation.status] || 'bg-gradient-warning';
                
                const deleteRoute = `{{ route('donations.destroy', ['donation' => '__ID__']) }}`.replace('__ID__', donation.id);
                
                const row = `
                    <tr>
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <p class="text-sm font-weight-bold mb-0">${donation.user?.name || 'Anonymous'}</p>
                                    <p class="text-xs text-muted mb-0">${donation.user?.email || 'N/A'}</p>
                                </div>
                            </div>
                        </td>
                        <td><p class="text-sm font-weight-bold mb-0">${donation.product_name}</p></td>
                        <td><p class="text-sm font-weight-bold mb-0">${donation.quantity}</p></td>
                        <td><p class="text-sm font-weight-bold mb-0">${donation.type.charAt(0).toUpperCase() + donation.type.slice(1)}</p></td>
                        <td><p class="text-sm font-weight-bold mb-0">${donation.location}</p></td>
                        <td><p class="text-sm font-weight-bold mb-0">${donation.donation_date}</p></td>
                        <td class="align-middle text-center text-sm">
                            <span class="badge badge-sm ${statusClass}">${donation.status.charAt(0).toUpperCase() + donation.status.slice(1)}</span>
                        </td>
                        <td class="align-middle text-center">
                            <a href="{{ route('donations.show', ['donation' => '__ID__']) }}".replace('__ID__', donation.id) class="btn btn-link text-info text-sm mb-0" data-toggle="tooltip" data-original-title="View Donation">
                                <i class="material-symbols-rounded">visibility</i>
                            </a>
                            <a href="{{ route('donations.backedit', ['donation' => '__ID__']) }}".replace('__ID__', donation.id) class="btn btn-link text-warning text-sm mb-0" data-toggle="tooltip" data-original-title="Edit Donation">
                                <i class="material-symbols-rounded">edit</i>
                            </a>
                            <a href="{{ route('donations.showRequests', ['donation' => '__ID__']) }}".replace('__ID__', donation.id) class="btn btn-link text-primary text-sm mb-0" data-toggle="tooltip" data-original-title="View Requests">
                                <i class="material-symbols-rounded">group</i>
                            </a>
                            <form action="${deleteRoute}" method="POST" class="delete-form d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link text-danger text-sm mb-0 delete-btn" data-donation-id="${donation.id}">
                                    <i class="material-symbols-rounded">delete</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            // Reattach delete button event listeners
            attachDeleteListeners();
        }

        function attachDeleteListeners() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.removeEventListener('click', handleDeleteClick); // Remove existing listener to prevent duplicates
                button.addEventListener('click', handleDeleteClick);
            });
        }

        function handleDeleteClick(e) {
            e.preventDefault();
            const form = this.closest('form');
            const donationId = this.getAttribute('data-donation-id');

            Swal.fire({
                title: 'Confirm Deletion',
                text: `Are you sure you want to delete donation #${donationId}? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'animated fadeInDown',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                didOpen: () => {
                    // Ensure the popup is properly styled and visible
                    document.body.classList.add('swal2-shown', 'swal2-height-auto');
                },
                didClose: () => {
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    try {
                        form.submit();
                    } catch (error) {
                        console.error('Form submission error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete donation. Please try again or check the console for details.',
                        });
                    }
                }
            }).catch(error => {
                console.error('SweetAlert2 error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please check the console.',
                });
            });
        }

        // Function to update stats
        function updateStats(stats, advancedStats) {
            const totalElement = document.getElementById('stats-total');
            if (totalElement) totalElement.textContent = 'Total Donations: ' + (stats.total || 0);

            const pendingElement = document.getElementById('stats-pending');
            if (pendingElement) pendingElement.textContent = stats.pending || 0;

            const acceptedElement = document.getElementById('stats-accepted');
            if (acceptedElement) acceptedElement.textContent = stats.accepted || 0;

            const rejectedElement = document.getElementById('stats-rejected');
            if (rejectedElement) rejectedElement.textContent = stats.rejected || 0;

            const takenElement = document.getElementById('stats-taken');
            if (takenElement) takenElement.textContent = stats.taken || 0;

            // Update advanced stats
            const advTotalQty = document.getElementById('advanced-total-quantity');
            if (advTotalQty) advTotalQty.textContent = advancedStats.total_quantity || 0;

            const advAvgQty = document.getElementById('advanced-avg-quantity');
            if (advAvgQty) advAvgQty.textContent = advancedStats.average_quantity || 0;

            const advUniqueDonors = document.getElementById('advanced-unique-donors');
            if (advUniqueDonors) advUniqueDonors.textContent = advancedStats.unique_donors || 0;

            const advCompletionRate = document.getElementById('advanced-completion-rate');
            if (advCompletionRate) advCompletionRate.textContent = (advancedStats.completion_rate || 0) + '%';

            const advAvgDays = document.getElementById('advanced-avg-days');
            if (advAvgDays) advAvgDays.textContent = advancedStats.average_days_to_completion || 0;

            const advTypeDist = document.getElementById('advanced-type-dist');
            if (advTypeDist) advTypeDist.innerHTML = `
                <span class="badge bg-gradient-success">♻️ ${advancedStats.recyclable_count || 0}</span>
                <span class="badge bg-gradient-info">🌿 ${advancedStats.renewable_count || 0}</span>
            `;
        }

        // Function to update pagination
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');
            container.innerHTML = '';
            if (pagination.last_page > 1) {
                const ul = document.createElement('ul');
                ul.className = 'pagination';
                for (let i = 1; i <= pagination.last_page; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    ul.appendChild(li);
                }
                container.appendChild(ul);
                container.querySelectorAll('.page-link').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const page = this.getAttribute('data-page');
                        fetchDonations(
                            document.getElementById('search-input').value,
                            document.getElementById('type-select').value,
                            page
                        );
                    });
                });
            }
        }

        // Event listeners for filter inputs
        document.getElementById('search-input').addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                fetchDonations(this.value, document.getElementById('type-select').value);
            }, 300);
        });

        document.getElementById('type-select').addEventListener('change', function () {
            fetchDonations(document.getElementById('search-input').value, this.value);
        });

        document.getElementById('clear-btn').addEventListener('click', function () {
            document.getElementById('search-input').value = '';
            document.getElementById('type-select').value = 'all';
            fetchDonations('', 'all');
        });

        // Initial attachment of delete listeners for static rows
        attachDeleteListeners();
    });
</script>
<style>
    .swal2-popup {
        border-radius: 10px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    .swal2-title {
        color: #343a40 !important;
        font-weight: 600 !important;
    }
    .swal2-html-container {
        color: #6c757d !important;
    }
    .swal2-confirm.btn-danger {
        background-color: #dc3545 !important;
        color: white !important;
        border: none !important;
        padding: 8px 20px !important;
        border-radius: 5px !important;
    }
    .swal2-cancel.btn-secondary {
        background-color: #6c757d !important;
        color: white !important;
        border: none !important;
        padding: 8px 20px !important;
        border-radius: 5px !important;
    }
    .swal2-confirm.btn-danger:hover {
        background-color: #c82333 !important;
    }
    .swal2-cancel.btn-secondary:hover {
        background-color: #5a6268 !important;
    }
    .animated.fadeInDown {
        animation: fadeInDown 0.5s ease-out;
    }
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    .swal2-hide {
        animation: fadeOutUp 0.3s ease-in forwards !important;
    }
    @keyframes fadeOutUp {
        from {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
        to {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
    }
</style>
@endsection
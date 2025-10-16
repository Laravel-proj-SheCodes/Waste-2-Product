@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    {{-- Stat Cards --}}
    <div class="row mb-4">
        {{-- Total Announcements --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Total Announcements</p>
                            <h5 class="font-weight-bolder text-dark mb-0">
                                {{ $stats['total'] }}
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-primary text-center rounded-circle shadow">
                            <i class="material-symbols-rounded text-white">storefront</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Active</p>
                            <h5 class="font-weight-bolder text-success mb-0">
                                {{ $stats['active'] }}
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-success text-center rounded-circle shadow">
                            <i class="material-symbols-rounded text-white">check_circle</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sold --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-sm mb-0 text-uppercase font-weight-bold text-secondary">Sold</p>
                            <h5 class="font-weight-bolder text-info mb-0">
                                {{ $stats['vendue'] }}
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-info text-center rounded-circle shadow">
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
                            <h5 class="font-weight-bolder text-warning mb-0">
                                {{ number_format($stats['revenue'], 2) }} DH
                            </h5>
                        </div>
                        <div class="icon icon-shape bg-gradient-warning text-center rounded-circle shadow">
                            <i class="material-symbols-rounded text-white">payments</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


{{-- Compact Search and Filter Section --}}
{{-- Enhanced Search and Filter Section with Material Dashboard 3 Styling --}}
<div class="card shadow-lg border-0 mb-4">
    

    <div class="card-body px-4 py-4">
        <form method="GET" action="{{ route('annonces.index') }}" id="filterForm">
            <div class="row align-items-end g-3">

                {{-- Search --}}
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Search</label>
                    <div class="input-group input-group-outline">
                        <input type="text"
                            class="form-control"
                            name="search"
                            placeholder="Search announcements..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Status</label>
                    <select class="form-select form-control" name="status">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="vendue" {{ request('status') == 'vendue' ? 'selected' : '' }}>Sold</option>
                        <option value="expiree" {{ request('status') == 'expiree' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                {{-- Price Range --}}
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Price</label>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number"
                            class="form-control form-control-sm"
                            name="min_price"
                            placeholder="Min"
                            value="{{ request('min_price') }}">
                        <span class="text-secondary text-xs font-weight-bold">–</span>
                        <input type="number"
                            class="form-control form-control-sm"
                            name="max_price"
                            placeholder="Max"
                            value="{{ request('max_price') }}">
                    </div>
                </div>

                {{-- Sort By --}}
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Sort By</label>
                    <select class="form-select form-control" name="sort_by">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date</option>
                        <option value="prix" {{ request('sort_by') == 'prix' ? 'selected' : '' }}>Price</option>
                        <option value="statut_annonce" {{ request('sort_by') == 'statut_annonce' ? 'selected' : '' }}>Status</option>
                    </select>
                </div>

                {{-- Sort Order --}}
                <div class="col-lg-1 col-md-2 col-sm-6">
                    <label class="form-label text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 mb-2">Order</label>
                    <select class="form-select form-control" name="sort_order">
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Desc</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Asc</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="col-lg-2 col-md-3 col-sm-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn bg-gradient-dark text-white mb-0 font-weight-bold w-100">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">filter_alt</i>
                       
                    </button>
                    <a href="{{ route('annonces.index') }}" class="btn btn-outline-secondary mb-0 font-weight-bold">
                        <i class="material-symbols-rounded" style="font-size: 16px;">refresh</i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>



    {{-- Announcements Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Marketplace Announcements</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="annonces-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Announcement</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Waste Post</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Price</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created Date</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($annonces as $annonce)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('assets-backoffice/img/default-avatar.png') }}" 
                                                         class="avatar avatar-sm me-3 border-radius-lg" 
                                                         alt="user" 
                                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM5OTk5OTkiLz4KPC9zdmc+';">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $annonce->postDechet->user->name ?? 'Unknown User' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $annonce->postDechet->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $annonce->postDechet->titre ?? 'Unknown Post' }}</p>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $annonce->postDechet->description ? Str::limit($annonce->postDechet->description, 50) : '' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            @php
                                                $badgeClass = match($annonce->statut_annonce) {
                                                    'active' => 'bg-gradient-success',
                                                    'inactive' => 'bg-gradient-secondary',
                                                    'vendue' => 'bg-gradient-info',
                                                    'expiree' => 'bg-gradient-danger',
                                                    default => 'bg-gradient-warning'
                                                };
                                                $badgeText = match($annonce->statut_annonce) {
                                                    'active' => 'Active',
                                                    'inactive' => 'Inactive',
                                                    'vendue' => 'Sold',
                                                    'expiree' => 'Expired',
                                                    default => 'Unknown'
                                                };
                                            @endphp
                                            <span class="badge badge-sm {{ $badgeClass }}">{{ $badgeText }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($annonce->prix, 2) }} DH</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $annonce->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('annonces.commandes', $annonce->id) }}" 
                                               class="btn btn-link text-primary text-gradient px-3 mb-0"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="View all orders for this announcement">
                                                <i class="material-symbols-rounded text-sm me-2">shopping_cart</i>
                                                Orders ({{ $annonce->commandes->count() }})
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-center">
                                                <i class="material-symbols-rounded opacity-5" style="font-size: 3rem;">inventory_2</i>
                                                <p class="text-secondary text-sm font-weight-normal mb-0 mt-2">No announcements found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Enhanced Material Dashboard Pagination --}}
                    @if($annonces->hasPages())
                        <div class="px-3 py-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                {{-- Showing results text --}}
                                <div class="text-sm text-secondary">
                                    Showing <span class="font-weight-bold">{{ $annonces->firstItem() }}</span> 
                                    to <span class="font-weight-bold">{{ $annonces->lastItem() }}</span> 
                                    of <span class="font-weight-bold">{{ $annonces->total() }}</span> results
                                </div>
                                
                                {{-- Pagination buttons --}}
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-primary mb-0">
                                        {{-- Previous Button --}}
                                        @if ($annonces->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="material-symbols-rounded">chevron_left</i>
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $annonces->previousPageUrl() }}" rel="prev">
                                                    <i class="material-symbols-rounded">chevron_left</i>
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Page Numbers --}}
                                        @foreach ($annonces->getUrlRange(1, $annonces->lastPage()) as $page => $url)
                                            @if ($page == $annonces->currentPage())
                                                <li class="page-item active" aria-current="page">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Next Button --}}
                                        @if ($annonces->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $annonces->nextPageUrl() }}" rel="next">
                                                    <i class="material-symbols-rounded">chevron_right</i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="material-symbols-rounded">chevron_right</i>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
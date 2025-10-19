@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    :root {
        --emerald-600: #059669;
        --teal-600: #0d9488;
        --slate-900: #0f172a;
        --slate-600: #475569;
    }
    
    .header {
        background: linear-gradient(to right, var(--emerald-600), var(--teal-600));
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .breadcrumb {
        margin-bottom: 2rem;
    }
    
    .breadcrumb-item {
        display: inline-block;
        color: var(--slate-600);
        margin: 0 0.5rem;
    }
    
    .breadcrumb-item a {
        color: var(--emerald-600);
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--slate-900);
        margin: 0;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: var(--emerald-600);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--teal-600);
        box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);
    }
    
    .btn-secondary {
        background: #e2e8f0;
        color: var(--slate-900);
    }
    
    .btn-secondary:hover {
        background: #cbd5e1;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .product-image {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #f0fdf4, #dbeafe);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        overflow: hidden;
    }
    
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-content {
        padding: 1.5rem;
    }
    
    .product-name {
        font-size: 1.125rem;
        font-weight: bold;
        color: var(--slate-900);
        margin-bottom: 0.5rem;
    }
    
    .product-description {
        font-size: 0.9rem;
        color: var(--slate-600);
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .product-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }
    
    .stat-item {
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 6px;
    }
    
    .stat-label {
        color: var(--slate-600);
        font-weight: 500;
    }
    
    .stat-value {
        color: var(--emerald-600);
        font-weight: bold;
        margin-top: 0.25rem;
    }
    
    .product-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .product-actions button,
    .product-actions a {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .btn-edit {
        background: #fef3c7;
        color: #92400e;
    }
    
    .btn-edit:hover {
        background: #fcd34d;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .btn-delete:hover {
        background: #fca5a5;
    }
    
    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .empty-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--slate-900);
        margin-bottom: 0.5rem;
    }
    
    .empty-text {
        color: var(--slate-600);
        margin-bottom: 1.5rem;
    }
    
    .filters {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    
    .filter-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .filter-input {
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.9rem;
    }
</style>

<div class="header">
    <div class="container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Produits Transformés</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Gérez vos produits issus de la transformation</p>
    </div>
</div>

<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('front.transformation.propositions.index') }}" class="breadcrumb-item">📋 Propositions</a>
        <span class="breadcrumb-item">/</span>
        <span class="breadcrumb-item">Produits</span>
    </div>

    <!-- Section Header -->
    <div class="section-header">
        <h2 class="section-title">🎁 Liste des Produits</h2>
        <a href="{{ route('front.transformation.produits.create') }}" class="btn btn-primary">
            ➕ Ajouter un Produit
        </a>
    </div>

    <!-- Filters -->
    <div class="filters">
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Rechercher</label>
                <input type="text" class="filter-input" placeholder="Nom du produit..." id="searchInput" onkeyup="filterProducts()">
            </div>
            <div class="filter-group">
                <label class="filter-label">Trier par</label>
                <select class="filter-input" id="sortSelect" onchange="filterProducts()">
                    <option value="recent">Plus récent</option>
                    <option value="price-high">Prix: Plus élevé</option>
                    <option value="price-low">Prix: Plus bas</option>
                    <option value="value">Valeur ajoutée</option>
                </select>
            </div>
        </div>
    </div>

    @if($produits && $produits->count() > 0)
        <div class="product-grid">
            @foreach($produits as $produit)
                <div class="product-card" data-name="{{ strtolower($produit->nom_produit) }}" data-price="{{ $produit->prix_vente }}">
                    <div class="product-image">
                        @if($produit->photo)
                            <img src="{{ asset('storage/' . $produit->photo) }}" alt="{{ $produit->nom_produit }}">
                        @else
                            🎁
                        @endif
                    </div>
                    
                    <div class="product-content">
                        <div class="product-name">{{ $produit->nom_produit }}</div>
                        <div class="product-description">
                            {{ Str::limit($produit->description ?? 'Aucune description', 80) }}
                        </div>
                        
                        <div class="product-stats">
                            <div class="stat-item">
                                <div class="stat-label">Quantité</div>
                                <div class="stat-value">{{ $produit->quantite_produite }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Prix Vente</div>
                                <div class="stat-value">{{ number_format($produit->prix_vente, 2) }} DT</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Valeur Ajoutée</div>
                                <div class="stat-value">{{ number_format($produit->valeur_ajoutee, 2) }} DT</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label">Processus</div>
                                <div class="stat-value">#{{ $produit->processus_id }}</div>
                            </div>
                        </div>
                        
                        <div class="product-actions">
                            <a href="{{ route('front.transformation.produits.edit', $produit->id) }}" class="btn-edit">
                                ✏️ Modifier
                            </a>
                            <form method="POST" action="{{ route('front.transformation.produits.destroy', $produit->id) }}" style="flex: 1;" onsubmit="return confirm('Êtes-vous sûr?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="width: 100%;">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($produits->hasPages())
            <div style="display: flex; justify-content: center; margin-top: 2rem;">
                {{ $produits->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <div class="empty-title">Aucun Produit</div>
            <div class="empty-text">Vous n'avez pas encore créé de produits transformés. Commencez par en ajouter un!</div>
            <a href="{{ route('front.transformation.produits.create') }}" class="btn btn-primary">
                ➕ Créer le Premier Produit
            </a>
        </div>
    @endif
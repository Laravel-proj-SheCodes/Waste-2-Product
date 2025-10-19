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
        max-width: 1000px;
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
    
    .content-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--slate-900);
        margin: 0 0 1rem 0;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .detail-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        gap: 2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .detail-row:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 600;
        color: var(--slate-900);
    }
    
    .detail-value {
        color: var(--slate-600);
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid;
    }
    
    .badge-en_cours {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }
    
    .badge-terminé {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }
    
    .badge-en_attente {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }
    
    .info-box {
        background: #f0fdf4;
        border-left: 4px solid var(--emerald-600);
        padding: 1rem;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .stat-box {
        background: #f8fafc;
        border-left: 4px solid var(--emerald-600);
        padding: 1rem;
        border-radius: 8px;
    }
    
    .stat-label {
        font-size: 0.85rem;
        color: var(--slate-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--slate-900);
        margin-top: 0.5rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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
    
    .timeline {
        position: relative;
        padding: 1rem 0 1rem 2rem;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background: var(--emerald-600);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    
    .timeline-item:before {
        content: '';
        position: absolute;
        left: -2.5rem;
        top: 0.3rem;
        width: 12px;
        height: 12px;
        background: var(--emerald-600);
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 0 0 2px var(--emerald-600);
    }
    
    .timeline-content {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
    }
</style>

<div class="header">
    <div class="container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Processus de Transformation #{{ $processusTransformation->id }}</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Détails du processus de transformation</p>
    </div>
</div>

<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('front.transformation.propositions.index') }}" class="breadcrumb-item">📋 Propositions</a>
        <span class="breadcrumb-item">/</span>
        <a href="{{ route('front.transformation.propositions.show', $processusTransformation->propositionTransformation->id) }}" class="breadcrumb-item">
            Proposition #{{ $processusTransformation->propositionTransformation->id }}
        </a>
        <span class="breadcrumb-item">/</span>
        <span class="breadcrumb-item">Processus</span>
    </div>

    <!-- Main Information -->
    <div class="content-card">
        <div class="section-title">Informations Principales</div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Durée Estimée</div>
                <div class="stat-value">{{ $processusTransformation->duree_estimee ?? 'N/A' }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Coût Total</div>
                <div class="stat-value">{{ number_format($processusTransformation->cout ?? 0, 2) }} DT</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Statut</div>
                <div style="margin-top: 0.5rem;">
                    <span class="status-badge badge-{{ strtolower(str_replace(' ', '_', $processusTransformation->statut)) }}">
                        {{ ucfirst($processusTransformation->statut ?? 'N/A') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Équipements</div>
            <div class="detail-value">{{ $processusTransformation->equipements ?? 'Non spécifiés' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Créé le</div>
            <div class="detail-value">{{ $processusTransformation->created_at->format('d/m/Y à H:i') }}</div>
        </div>
    </div>

    <!-- Waste Input Information -->
    <div class="content-card">
        <div class="section-title">📥 Déchet Entrant</div>

        <div class="info-box">
            <div style="font-weight: 600; margin-bottom: 0.5rem;">{{ $processusTransformation->dechetEntrant->title ?? 'Post Déchet' }}</div>
            <div style="font-size: 0.9rem; color: var(--slate-600);">
                {{ $processusTransformation->dechetEntrant->description ?? '' }}
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Quantité</div>
            <div class="detail-value">{{ $processusTransformation->dechetEntrant->quantite ?? 'N/A' }} {{ $processusTransformation->dechetEntrant->unité_mesure ?? '' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Catégorie</div>
            <div class="detail-value">{{ $processusTransformation->dechetEntrant->categorie ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="content-card">
        <div class="section-title">🎁 Produits Générés ({{ $processusTransformation->produits->count() ?? 0 }})</div>

        @if($processusTransformation->produits && $processusTransformation->produits->count() > 0)
            <div class="timeline">
                @foreach($processusTransformation->produits as $produit)
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div style="font-weight: 600; color: var(--slate-900); margin-bottom: 0.5rem;">
                                {{ $produit->nom_produit }}
                            </div>
                            <p style="color: var(--slate-600); margin: 0.5rem 0; font-size: 0.9rem;">
                                {{ $produit->description ?? 'Aucune description' }}
                            </p>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem; font-size: 0.9rem;">
                                <div>
                                    <strong>Quantité:</strong>
                                    <div style="color: var(--emerald-600);">{{ $produit->quantite_produite }}</div>
                                </div>
                                <div>
                                    <strong>Valeur Ajoutée:</strong>
                                    <div style="color: var(--emerald-600);">{{ number_format($produit->valeur_ajoutee, 2) }} DT</div>
                                </div>
                                <div>
                                    <strong>Prix de Vente:</strong>
                                    <div style="color: var(--emerald-600);">{{ number_format($produit->prix_vente, 2) }} DT</div>
                                </div>
                            </div>
                            @if($produit->photo)
                                <div style="margin-top: 1rem;">
                                    <img src="{{ asset('storage/' . $produit->photo) }}" alt="{{ $produit->nom_produit }}" style="max-width: 100%; max-height: 200px; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 2rem;">
                <a href="{{ route('front.transformation.produits.index') }}" class="btn btn-primary">
                    Gérer les produits →
                </a>
            </div>
        @else
            <div class="info-box">
                <p style="margin: 0;">Aucun produit n'a été créé pour ce processus. Commencez par ajouter les produits transformés.</p>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="content-card">
        <div class="action-buttons">
            <a href="{{ route('front.transformation.propositions.show', $processusTransformation->propositionTransformation->id) }}" class="btn btn-secondary">
                ← Retour à la Proposition
            </a>
            <a href="{{ route('front.transformation.produits.index') }}" class="btn btn-primary">
                Ajouter un Produit +
            </a>
        </div>
    </div>
</div>

@endsection
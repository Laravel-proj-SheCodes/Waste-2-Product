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
        grid-template-columns: 150px 1fr;
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
    
    .badge-en_attente {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }
    
    .badge-accepté {
        background: #dcfce7;
        color: #166534;
        border-color: #bbf7d0;
    }
    
    .badge-refusé {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }
    
    .post-info {
        background: #f0fdf4;
        border-left: 4px solid var(--emerald-600);
        padding: 1rem;
        border-radius: 8px;
        margin: 1rem 0;
    }
    
    .post-info-title {
        font-weight: 600;
        color: var(--slate-900);
        margin-bottom: 0.5rem;
    }
    
    .post-info-text {
        color: var(--slate-600);
        font-size: 0.95rem;
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
</style>

<div class="header">
    <div class="container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Proposition de Transformation #{{ $propositionTransformation->id }}</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Détails complets de votre proposition</p>
    </div>
</div>

<div class="container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('front.transformation.propositions.index') }}" class="breadcrumb-item">📋 Propositions</a>
        <span class="breadcrumb-item">/</span>
        <span class="breadcrumb-item">#{{ $propositionTransformation->id }}</span>
    </div>

    <!-- Main Information -->
    <div class="content-card">
        <div class="section-title">Informations Principales</div>

        <div class="detail-row">
            <div class="detail-label">Statut</div>
            <div class="detail-value">
                <span class="status-badge badge-{{ str_replace(' ', '_', strtolower($propositionTransformation->statut)) }}">
                    @switch($propositionTransformation->statut)
                        @case('en_attente')
                            ⏳ En Attente
                            @break
                        @case('accepté')
                            ✅ Acceptée
                            @break
                        @case('refusé')
                            ❌ Refusée
                            @break
                    @endswitch
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Créée le</div>
            <div class="detail-value">{{ $propositionTransformation->created_at->format('d/m/Y à H:i') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Mise à jour</div>
            <div class="detail-value">{{ $propositionTransformation->updated_at->format('d/m/Y à H:i') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Transformateur</div>
            <div class="detail-value">{{ $propositionTransformation->transformateur->name ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Post Information -->
    <div class="content-card">
        <div class="section-title">📋 Post Déchet</div>

        <div class="post-info">
            <div class="post-info-title">{{ $propositionTransformation->proposition->postDechet->title ?? 'Post Déchet' }}</div>
            <div class="post-info-text">{{ $propositionTransformation->proposition->postDechet->description ?? '' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Catégorie</div>
            <div class="detail-value">{{ $propositionTransformation->proposition->postDechet->categorie ?? 'N/A' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Quantité</div>
            <div class="detail-value">{{ $propositionTransformation->proposition->postDechet->quantite ?? 'N/A' }} {{ $propositionTransformation->proposition->postDechet->unité_mesure ?? '' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Localisation</div>
            <div class="detail-value">{{ $propositionTransformation->proposition->postDechet->localisation ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Transformation Description -->
    <div class="content-card">
        <div class="section-title">🔄 Description de la Transformation</div>

        <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; color: var(--slate-600); line-height: 1.6;">
            {{ $propositionTransformation->description ?? 'Aucune description disponible' }}
        </div>
    </div>

    <!-- Processus Section -->
    @if($propositionTransformation->processus)
        <div class="content-card">
            <div class="section-title">⚙️ Processus de Transformation</div>

            <div class="detail-row">
                <div class="detail-label">Durée Estimée</div>
                <div class="detail-value">{{ $propositionTransformation->processus->duree_estimee ?? 'N/A' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Coût</div>
                <div class="detail-value">{{ number_format($propositionTransformation->processus->cout ?? 0, 2) }} DT</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Équipements</div>
                <div class="detail-value">{{ $propositionTransformation->processus->equipements ?? 'N/A' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Statut</div>
                <div class="detail-value">
                    <span class="status-badge badge-{{ strtolower($propositionTransformation->processus->statut) }}">
                        {{ ucfirst($propositionTransformation->processus->statut ?? 'N/A') }}
                    </span>
                </div>
            </div>

            <a href="{{ route('front.transformation.processus.show', $propositionTransformation->processus->id) }}" style="display: inline-block; background: var(--emerald-600); color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; margin-top: 1rem;">
                Voir les détails du processus →
            </a>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="content-card">
        <div class="action-buttons">
            @if($propositionTransformation->statut === 'en_attente')
                <a href="{{ route('front.transformation.propositions.edit', $propositionTransformation->id) }}" class="btn btn-primary">
                    ✏️ Modifier
                </a>
            @endif

            <a href="{{ route('front.transformation.propositions.index') }}" class="btn btn-secondary">
                ← Retour
            </a>
        </div>
    </div>
</div>

@endsection
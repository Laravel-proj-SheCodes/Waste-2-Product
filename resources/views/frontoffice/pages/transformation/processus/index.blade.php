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
    
    .processus-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .processus-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: var(--emerald-600);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }
    
    .card-title {
        font-size: 1.125rem;
        font-weight: bold;
        color: var(--slate-900);
    }
    
    .card-subtitle {
        color: var(--slate-600);
        font-size: 0.9rem;
        margin-top: 0.25rem;
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
    
    .card-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .stat {
        background: #f8fafc;
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
    }
    
    .stat-label {
        color: var(--slate-600);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        color: var(--emerald-600);
        font-weight: bold;
        font-size: 1.25rem;
        margin-top: 0.25rem;
    }
    
    .card-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .btn-small {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .btn-edit {
        background: #fef3c7;
        color: #92400e;
    }
    
    .btn-edit:hover {
        background: #fcd34d;
    }
    
    .btn-view {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .btn-view:hover {
        background: #93c5fd;
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
</style>

<div class="header">
    <div class="container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Mes Processus de Transformation</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Gérez vos processus de transformation en cours</p>
    </div>
</div>

<div class="container">
    <div class="section-header">
        <h2 class="section-title">⚙️ Liste des Processus</h2>
        <a href="{{ route('front.transformation.propositions.index') }}" class="btn btn-primary">
            ← Retour aux Propositions
        </a>
    </div>

    @if($processus && $processus->count() > 0)
        @foreach($processus as $proc)
            <div class="processus-card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Processus #{{ $proc->id }}</div>
                        <div class="card-subtitle">
                            Proposition: {{ $proc->propositionTransformation->proposition->postDechet->title ?? 'Post Déchet' }}
                        </div>
                    </div>
                    <span class="status-badge badge-{{ str_replace(' ', '_', strtolower($proc->statut)) }}">
                        @switch($proc->statut)
                            @case('en_attente')
                                ⏳ En Attente
                                @break
                            @case('en_cours')
                                🔄 En Cours
                                @break
                            @case('terminé')
                                ✅ Terminé
                                @break
                            @default
                                {{ $proc->statut }}
                        @endswitch
                    </span>
                </div>

                <div class="card-stats">
                    <div class="stat">
                        <div class="stat-label">Durée</div>
                        <div class="stat-value">{{ $proc->duree_estimee }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Coût</div>
                        <div class="stat-value">{{ number_format($proc->cout, 2) }} DT</div>
                    </div>
                    <div class="stat">
                        <div class="stat-label">Produits</div>
                        <div class="stat-value">{{ $proc->produits->count() }}</div>
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 0.75rem; border-radius: 8px; margin: 1rem 0; font-size: 0.9rem; color: var(--slate-600);">
                    <strong>Équipements:</strong> {{ $proc->equipements ?? 'Non spécifiés' }}
                </div>

                <div class="card-actions">
                    <a href="{{ route('front.transformation.processus.show', $proc->id) }}" class="btn btn-small btn-view">
                        👁️ Voir
                    </a>
                    <a href="{{ route('front.transformation.processus.edit', $proc->id) }}" class="btn btn-small btn-edit">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('front.transformation.produits.index') }}" class="btn btn-small btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                        ➕ Ajouter Produit
                    </a>
                </div>
            </div>
        @endforeach

        @if($processus->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $processus->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-icon">⚙️</div>
            <div class="empty-title">Aucun Processus</div>
            <p style="color: var(--slate-600); margin-bottom: 1.5rem;">
                Vous n'avez pas encore créé de processus. Créez d'abord une proposition acceptée.
            </p>
            <a href="{{ route('front.transformation.propositions.index') }}" class="btn btn-primary">
                ← Retour aux Propositions
            </a>
        </div>
    @endif
</div>

@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    :root {
        --emerald-600: #059669;
        --teal-600: #0d9488;
        --slate-900: #0f172a;
        --slate-600: #475569;
    }
    
    .dashboard-header {
        background: linear-gradient(to right, var(--emerald-600), var(--teal-600));
        color: white;
        padding: 2rem;
        border-radius: 0;
    }
    
    .stat-card {
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }
    
    .stat-card.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
    .stat-card.yellow { background: linear-gradient(135deg, #eab308, #ca8a04); }
    .stat-card.green { background: linear-gradient(135deg, #10b981, #047857); }
    .stat-card.purple { background: linear-gradient(135deg, #a855f7, #7e22ce); }
    
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    .tab-button {
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border: none;
        background: none;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }
    
    .tab-button.active {
        color: var(--emerald-600);
        border-bottom-color: var(--emerald-600);
    }
    
    .tab-button:hover {
        color: var(--slate-900);
    }
    
    .proposition-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .proposition-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        border-color: var(--emerald-600);
    }
    
    .badge {
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
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 1.1rem;
        text-decoration: none;
    }
    
    .btn-icon:hover {
        transform: scale(1.1);
    }
    
    .btn-view { background: #dbeafe; color: #1e40af; }
    .btn-edit { background: #fef3c7; color: #92400e; }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    
    .btn-primary {
        background: var(--emerald-600);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-primary:hover {
        background: var(--teal-600);
        box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .metric-bar {
        background: #e2e8f0;
        border-radius: 8px;
        height: 12px;
        overflow: hidden;
    }
    
    .metric-bar-fill {
        background: linear-gradient(to right, var(--emerald-600), var(--teal-600));
        height: 100%;
    }
    
    .recommendation-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .recommendation-card:hover {
        border-color: var(--emerald-600);
        background: #f0fdf4;
    }
    
    .match-score {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        padding: 1rem;
        border-radius: 8px;
    }
    
    .match-high { background: #dcfce7; color: #166534; }
    .match-medium { background: #dbeafe; color: #1e40af; }
    .match-low { background: #fed7aa; color: #92400e; }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .empty-state {
        text-align: center;
        padding: 2rem;
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .analytics-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .analytics-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }
    
    .metric-item {
        margin-bottom: 1.5rem;
    }
    
    .metric-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
    }
</style>

<div class="dashboard-header">
    <div style="max-width: 1200px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Tableau de Bord Transformation</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Gérez vos propositions et suivez votre activité de transformation</p>
    </div>
</div>

<div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    
    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="stat-card blue">
            <div class="stat-label">📋 Propositions Totales</div>
            <div class="stat-value">{{ $totalPropositions }}</div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-label">⏳ En Attente</div>
            <div class="stat-value">{{ $pendingCount }}</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">✅ Acceptées</div>
            <div class="stat-value">{{ $acceptedCount }}</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">💰 Revenu Total</div>
            <div class="stat-value">{{ number_format($totalRevenue, 0) }} DT</div>
        </div>
    </div>

    <!-- Success Messages -->
    @if(session('success'))
        <div style="background: #dcfce7; border-left: 4px solid #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; color: #166534;">
            <strong>✓ Succès:</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; border-left: 4px solid #dc3545; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; color: #991b1b;">
            <strong>✗ Erreur:</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div style="border-bottom: 1px solid #e2e8f0; display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <button class="tab-button active" onclick="switchTab('overview')">📋 Vue d'ensemble</button>
        <button class="tab-button" onclick="switchTab('analytics')">📊 Analytique Avancée</button>
        <button class="tab-button" onclick="switchTab('recommendations')">🎯 Recommandations IA</button>
    </div>

    <!-- TAB 1: Overview -->
    <div id="overview" class="tab-content active">
        <div class="section-header">
            <h2 style="font-size: 1.5rem; font-weight: bold; margin: 0;">Mes Propositions de Transformation</h2>
            <a href="{{ route('front.transformation.propositions.create') }}" class="btn-primary">
                ➕ Nouvelle Proposition
            </a>
        </div>

        @if($propositions->isEmpty())
            <div class="empty-state">
                <p style="color: var(--slate-600); font-size: 1rem; margin: 0;">Aucune proposition en cours. Commencez par créer une nouvelle proposition!</p>
            </div>
        @else
            @foreach($propositions as $proposition)
                <div class="proposition-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div style="flex: 1; min-width: 250px;">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: var(--slate-900); margin: 0 0 0.5rem 0;">
                                {{ $proposition->proposition->postDechet->title ?? 'Post Déchet' }}
                            </h3>
                            <span class="badge badge-{{ str_replace(' ', '_', strtolower($proposition->statut)) }}">
                                @switch($proposition->statut)
                                    @case('en_attente')
                                        ⏳ En Attente
                                        @break
                                    @case('accepté')
                                        ✅ Acceptée
                                        @break
                                    @case('refusé')
                                        ❌ Refusée
                                        @break
                                    @default
                                        {{ $proposition->statut }}
                                @endswitch
                            </span>
                        </div>
                        <div class="action-buttons">
                            <a href="{{ route('front.transformation.propositions.show', $proposition->id) }}" class="btn-icon btn-view" title="Voir">
                                👁️
                            </a>
                            @if($proposition->statut === 'en_attente')
                                <a href="{{ route('front.transformation.propositions.edit', $proposition->id) }}" class="btn-icon btn-edit" title="Modifier">
                                    ✏️
                                </a>
                            @endif
                            <form method="POST" action="{{ route('front.transformation.propositions.destroy', $proposition->id) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-delete" title="Supprimer">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </div>

                    <p style="color: var(--slate-600); margin: 0.5rem 0; font-size: 0.9rem;">
                        {{ $proposition->description ?? 'Pas de description' }}
                    </p>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; flex-wrap: wrap; gap: 1rem;">
                        <small style="color: #94a3b8;">Créée le {{ $proposition->created_at->format('d/m/Y à H:i') }}</small>
                        @if($proposition->statut === 'accepté' && $proposition->processus)
                            <div style="color: var(--emerald-600); font-weight: 600;">
                                💚 Revenu: {{ number_format($proposition->processus->cout ?? 0, 0) }} DT
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Pagination -->
        @if($propositions->hasPages())
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $propositions->links() }}
            </div>
        @endif
    </div>

    <!-- TAB 2: Advanced Analytics -->
    <div id="analytics" class="tab-content">
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1.5rem;">📊 Analytique Avancée</h2>
        
        <div class="analytics-grid">
            <!-- Revenue Trends -->
            <div class="analytics-card">
                <div class="analytics-title">💵 Revenu par Mois</div>
                @foreach($revenueByMonth as $month => $amount)
                    <div class="metric-item">
                        <div class="metric-label">
                            <span>{{ $month }}</span>
                            <span style="font-weight: bold; color: var(--emerald-600);">{{ number_format($amount, 0) }} DT</span>
                        </div>
                        <div class="metric-bar">
                            <div class="metric-bar-fill" style="width: {{ min(100, ($amount / ($maxRevenue ?? 5000)) * 100) }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Performance Metrics -->
            <div class="analytics-card">
                <div class="analytics-title">⚡ Performance</div>
                
                <div class="metric-item">
                    <div class="metric-label">
                        <span>Taux d'acceptation</span>
                        <span style="font-weight: bold;">{{ $acceptanceRate ?? 0 }}%</span>
                    </div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: {{ $acceptanceRate ?? 0 }}%;"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">
                        <span>Délai moyen (jours)</span>
                        <span style="font-weight: bold;">{{ round($avgDays ?? 0, 1) }}</span>
                    </div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: 82%;"></div>
                    </div>
                </div>

                <div class="metric-item">
                    <div class="metric-label">
                        <span>Satisfaction client</span>
                        <span style="font-weight: bold;">{{ $avgRating ?? 4.8 }}/5</span>
                    </div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: 96%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: AI Recommendations -->
    <div id="recommendations" class="tab-content">
        <h2 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">🎯 Posts Recommandés pour Vous</h2>
        <p style="color: var(--slate-600); margin-bottom: 1.5rem;">Basé sur votre historique et spécialisation <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600;">IA</span></p>

        @if($recommendations->isEmpty())
            <div class="empty-state">
                <p style="color: var(--slate-600);">Pas de recommandations disponibles pour le moment.</p>
            </div>
        @else
            @foreach($recommendations as $rec)
                <div class="recommendation-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 2rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 250px;">
                            <h4 style="font-size: 1rem; font-weight: 600; color: var(--slate-900); margin: 0 0 0.5rem 0;">
                                {{ $rec->postDechet->title ?? 'Post' }}
                            </h4>
                            <p style="color: var(--slate-600); margin: 0.5rem 0; font-size: 0.9rem;">
                                <strong>Catégorie:</strong> {{ $rec->postDechet->categorie ?? 'N/A' }}
                            </p>
                            <p style="color: var(--slate-600); margin: 0; font-size: 0.9rem;">
                                {{ Str::limit($rec->postDechet->description ?? '', 100) }}
                            </p>
                        </div>
                        <div style="text-align: center; min-width: 150px;">
                            <div class="match-score match-{{ $rec->match_score >= 90 ? 'high' : ($rec->match_score >= 75 ? 'medium' : 'low') }}">
                                {{ $rec->match_score }}%
                            </div>
                            <small style="color: var(--slate-600); display: block; margin-top: 0.5rem;">
                                @if($rec->match_score >= 90)
                                    Très compatible
                                @elseif($rec->match_score >= 75)
                                    Compatible
                                @else
                                    À considérer
                                @endif
                            </small>
                            <a href="{{ route('front.transformation.propositions.create', ['postId' => $rec->post_id]) }}" class="btn-primary" style="margin-top: 1rem; width: 100%; justify-content: center;">
                                Proposer
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>

<script>
    function switchTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        // Remove active from all buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
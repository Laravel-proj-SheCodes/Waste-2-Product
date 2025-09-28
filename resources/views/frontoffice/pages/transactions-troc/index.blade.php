@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    /* Cartes modernes avec touche de vert */
    .transaction-card {
        border: 1px solid rgba(25, 135, 84, 0.2); /* Bordure verte subtile */
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .transaction-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15);
        border-color: #198754; /* Vert plus marqué au survol */
    }
    .transaction-card.livre {
        border: 2px solid #198754; /* Bordure verte pour les transactions livrées */
    }
    .transaction-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(to right, #198754, transparent); /* Dégradé vert en haut */
        opacity: 0.3;
    }
    .card-title {
        color: #198754; /* Titre en vert */
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }
    .status-badge {
        padding: 0.3rem 0.6rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .status-en_cours { background: #fff3cd; color: #856404; }
    .status-livre { background: #d0f0d0; color: #198754; }
    .status-annule { background: #f8d7da; color: #dc3545; }
    .action-btn {
        padding: 0.4rem 1rem;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .action-btn i {
        margin-right: 0.3rem;
    }
    .btn-outline-primary {
        border-color: #198754;
        color: #198754;
    }
    .btn-outline-primary:hover {
        background: #198754;
        color: #fff;
    }
    .btn-outline-success {
        border-color: #198754;
        color: #198754;
    }
    .btn-outline-success:hover {
        background: #198754;
        color: #fff;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="h4 text-success">Mes Transactions Troc</h1>
        <a href="{{ route('postdechets.troc.front') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-arrow-left"></i> Retour au Troc
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($transactions->isEmpty())
        <p class="text-muted">Aucune transaction en cours.</p>
    @else
        <div class="row g-4">
            @foreach($transactions as $transaction)
                <div class="col-md-6 col-lg-4">
                    <div class="transaction-card {{ $transaction->statut_livraison == 'livre' ? 'livre' : '' }}">
                        <h5 class="card-title">Transaction #{{ $transaction->id }}</h5>
                        <p class="mb-1"><strong>Offre :</strong> {{ \Illuminate\Support\Str::limit($transaction->offreTroc->description, 50) }}</p>
                        <p class="mb-1"><strong>Post :</strong> {{ \Illuminate\Support\Str::limit($transaction->offreTroc->postDechet->titre, 50) }}</p>
                        <p class="mb-1"><strong>Date d'accord :</strong> {{ $transaction->date_accord ? $transaction->date_accord->translatedFormat('d/m/Y') : 'Non définie' }}</p>
                        <p class="mb-3">Statut : 
                            <span class="status-badge status-{{ $transaction->statut_livraison }}">
                                <i class="bi {{ $transaction->statut_livraison == 'livre' ? 'bi-check-circle-fill' : ($transaction->statut_livraison == 'annule' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }}"></i>
                                {{ ucfirst($transaction->statut_livraison) }}
                            </span>
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('transactions-troc.show.front', $transaction->id) }}" class="btn btn-outline-primary action-btn">
                                <i class="bi bi-eye"></i> Détails
                            </a>
                            @if($transaction->statut_livraison !== 'livre' && $transaction->statut_livraison !== 'annule')
                                <a href="{{ route('transactions-troc.edit.front', $transaction->id) }}" class="btn btn-outline-success action-btn">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Inclure Bootstrap Icons pour les icônes -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection

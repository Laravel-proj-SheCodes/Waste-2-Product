@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    /* Styles inspirés : hero vert, cartes soft, badges */
    .hero {
        background: #198754;
        color: #fff;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .card-soft {
        border: 0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(0,0,0,.08);
        padding: 2rem;
    }
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }
    .status-en_cours { background: #fff3cd; color: #856404; }
    .status-livre { background: #d0f0d0; color: #198754; }
    .status-annule { background: #f8d7da; color: #dc3545; }
</style>

<div class="container py-5">
    <a href="{{ route('transactions-troc.index.front') }}" class="text-success mb-3 d-inline-block">&larr; Retour aux transactions</a>

    <div class="hero">
        <h1 class="h4 mb-2">Transaction #{{ $transaction->id }}</h1>
        <p class="mb-0">Date d'accord : {{ $transaction->date_accord->translatedFormat('d/m/Y H:i') }}</p>
    </div>

    <div class="card-soft">
        <p><strong>Offre Troc :</strong> {{ $transaction->offreTroc->description }}</p>
        <p><strong>Post Déchet :</strong> {{ $transaction->offreTroc->postDechet->titre }}</p>
        <p><strong>Proposant :</strong> {{ $transaction->offreTroc->user->name }}</p>
        <p><strong>Acceptant :</strong> {{ $transaction->utilisateurAcceptant->name }}</p>
        <p><strong>Statut Livraison :</strong> 
            <span class="status-badge status-{{ $transaction->statut_livraison }}">
                {{ ucfirst($transaction->statut_livraison) }}
            </span>
        </p>
        <p><strong>Évaluation Mutuelle :</strong> {{ $transaction->evaluation_mutuelle ?? 'Aucune évaluation' }}</p>

        @if($transaction->statut_livraison !== 'livre' && $transaction->statut_livraison !== 'annule')
            <a href="{{ route('transactions-troc.edit.front', $transaction->id) }}" class="btn btn-success">Modifier Statut / Évaluation</a>
        @endif
    </div>
</div>
@endsection
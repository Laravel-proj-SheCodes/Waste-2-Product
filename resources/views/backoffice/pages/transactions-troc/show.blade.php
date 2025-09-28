@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-5 pb-4">
                        <div class="text-center">
                            <h4 class="text-white font-weight-bold mb-1">Détails de la Transaction Troc</h4>
                            <p class="text-white-50 mb-0 text-sm">Visualisez les détails de la transaction ci-dessous</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 py-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-id-badge text-primary me-2"></i>
                                <span class="text-dark font-weight-bold">ID:</span>
                                <span class="text-dark ms-2">{{ $transaction->id }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-box text-success me-2"></i>
                                <span class="text-dark font-weight-bold">Offre Troc:</span>
                                <span class="text-dark ms-2">{{ $transaction->offreTroc->description }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-box-open text-info me-2"></i>
                                <span class="text-dark font-weight-bold">Post Déchet:</span>
                                <span class="text-dark ms-2">{{ $transaction->offreTroc->postDechet->titre }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user text-primary me-2"></i>
                                <span class="text-dark font-weight-bold">Proposant:</span>
                                <span class="text-dark ms-2">{{ $transaction->offreTroc->user->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user-check text-success me-2"></i>
                                <span class="text-dark font-weight-bold">Acceptant:</span>
                                <span class="text-dark ms-2">{{ $transaction->utilisateurAcceptant->name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-calendar-alt text-danger me-2"></i>
                                <span class="text-dark font-weight-bold">Date d'accord:</span>
                                <span class="text-dark ms-2">{{ $transaction->date_accord->translatedFormat('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-shipping-fast text-info me-2"></i>
                                <span class="text-dark font-weight-bold">Statut Livraison:</span>
                                <span class="ms-2">
                                    @if ($transaction->statut_livraison === 'livre')
                                        <span class="badge badge-sm bg-gradient-success">{{ $transaction->statut_livraison }}</span>
                                    @elseif ($transaction->statut_livraison === 'annule')
                                        <span class="badge badge-sm bg-gradient-danger">{{ $transaction->statut_livraison }}</span>
                                    @else
                                        <span class="badge badge-sm bg-gradient-warning">{{ $transaction->statut_livraison }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-comment-dots text-secondary me-2"></i>
                                <span class="text-dark font-weight-bold">Évaluation Mutuelle:</span>
                                <span class="text-dark ms-2">{{ $transaction->evaluation_mutuelle ?? 'Aucune évaluation' }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Action buttons
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="{{ route('transactions-troc.edit', $transaction->id) }}" 
                                   class="btn btn-outline-warning btn-lg px-4 me-2" 
                                   data-toggle="tooltip" 
                                   data-original-title="Modifier Transaction">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <form action="{{ route('transactions-troc.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-lg px-4 me-2" 
                                            data-toggle="tooltip" 
                                            data-original-title="Supprimer Transaction" 
                                            onclick="return confirm('Voulez-vous vraiment supprimer cette transaction ?')">
                                        <i class="fas fa-trash me-2"></i>Supprimer
                                    </button>
                                </form> -->
                                <a href="{{ route('transactions-troc.index') }}" 
                                   class="btn btn-outline-secondary btn-lg px-4" 
                                   data-toggle="tooltip" 
                                   data-original-title="Retour à la Liste">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
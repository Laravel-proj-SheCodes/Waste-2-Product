@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Commandes Marketplace</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="commandes-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acheteur</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Annonce</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantité</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prix Total</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date Commande</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commandes as $commande)
                                    <tr>
                                        {{-- Acheteur --}}
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('assets-backoffice/img/default-avatar.png') }}" 
                                                         class="avatar avatar-sm me-3 border-radius-lg" 
                                                         alt="user"
                                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM5OTk5OTkiLz4KPC9zdmc+';">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $commande->acheteur->name ?? 'Utilisateur inconnu' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $commande->acheteur->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Annonce --}}
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $commande->annonceMarketplace->postDechet->titre ?? 'Annonce inconnue' }}</p>
                                            <p class="text-xs text-secondary mb-0">
                                                {{ $commande->annonceMarketplace->postDechet->description ? Str::limit($commande->annonceMarketplace->postDechet->description, 50) : '' }}
                                            </p>
                                        </td>

                                        {{-- Quantité --}}
                                        <td>
                                            <span class="text-secondary text-xs font-weight-bold">{{ $commande->quantite }}</span>
                                        </td>

                                        {{-- Prix total --}}
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($commande->prix_total, 2) }} €</span>
                                        </td>

                                        {{-- Statut commande --}}
                                        <td class="align-middle text-center text-sm">
                                            @php
                                                $badgeClass = match($commande->statut_commande) {
                                                    'en_attente' => 'bg-gradient-warning',
                                                    'validee' => 'bg-gradient-success',
                                                    'annulee' => 'bg-gradient-danger',
                                                    default => 'bg-gradient-secondary'
                                                };
                                                $badgeText = match($commande->statut_commande) {
                                                    'en_attente' => 'En attente',
                                                    'validee' => 'Validée',
                                                    'annulee' => 'Annulée',
                                                    default => 'Inconnu'
                                                };
                                            @endphp
                                            <span class="badge badge-sm {{ $badgeClass }}">{{ $badgeText }}</span>
                                        </td>

                                        {{-- Date --}}
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $commande->date_commande?->format('d/m/Y') ?? '' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Aucune commande trouvée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($commandes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $commandes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

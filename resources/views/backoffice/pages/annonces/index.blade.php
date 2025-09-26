@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Annonces Marketplace</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="annonces-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Annonce</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Post Déchet</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Statut</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prix</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date de Création</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Use server-side data instead of JavaScript fetch --}}
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
                                                    <h6 class="mb-0 text-sm">{{ $annonce->postDechet->user->nom ?? 'Utilisateur inconnu' }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $annonce->postDechet->user->email ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $annonce->postDechet->titre ?? 'Post inconnu' }}</p>
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
                                                    'vendue' => 'Vendue',
                                                    'expiree' => 'Expirée',
                                                    default => 'Inconnu'
                                                };
                                            @endphp
                                            <span class="badge badge-sm {{ $badgeClass }}">{{ $badgeText }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ number_format($annonce->prix, 2) }} €</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $annonce->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('annonces.show', $annonce->id) }}" 
                                               class="text-secondary font-weight-bold text-xs" 
                                               data-toggle="tooltip" 
                                               data-original-title="Voir détails">
                                                Voir
                                            </a>
                                            <a href="{{ route('annonces.edit', $annonce->id) }}" 
                                               class="text-secondary font-weight-bold text-xs ms-2" 
                                               data-toggle="tooltip" 
                                               data-original-title="Modifier">
                                                Modifier
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            Aucune annonce trouvée
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Add pagination links --}}
                    @if($annonces->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $annonces->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

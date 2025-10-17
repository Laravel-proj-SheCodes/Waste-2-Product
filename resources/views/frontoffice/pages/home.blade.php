@extends('frontoffice.layouts.layoutfront')

@section('content')
<!-- Section fonctionnalités -->
<section class="py-5 border-bottom" id="features">
    <div class="container px-5 my-5">
        <div class="row gx-5">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                    <i class="bi bi-collection"></i>
                </div>
                <h2 class="h4 fw-bolder">Titre en vedette</h2>
                <p>Paragraphe de texte sous le titre...</p>
                <a class="text-success text-decoration-none" href="{{ url('/services') }}">
                    Voir plus
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="col-lg-4 mb-5 mb-lg-0">
                <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                    <i class="bi bi-building"></i>
                </div>
                <h2 class="h4 fw-bolder">Titre en vedette</h2>
                <p>Paragraphe de texte sous le titre...</p>
                <a class="text-success text-decoration-none" href="{{ url('/services') }}">
                    Voir plus
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="col-lg-4">
                <div class="feature bg-success bg-gradient text-white rounded-3 mb-3">
                    <i class="bi bi-toggles2"></i>
                </div>
                <h2 class="h4 fw-bolder">Titre en vedette</h2>
                <p>Paragraphe de texte sous le titre...</p>
                <a class="text-success text-decoration-none" href="{{ url('/services') }}">
                    Voir plus
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section tarifs -->
<section class="bg-light py-5 border-bottom">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Payez selon votre usage</h2>
            <p class="lead mb-0">Avec nos plans tarifaires sans tracas</p>
        </div>
        <div class="row gx-5 justify-content-center">
            <!-- Carte Free -->
            <div class="col-lg-6 col-xl-4">
                <div class="card mb-5 mb-xl-0">
                    <div class="card-body p-5">
                        <div class="small text-uppercase fw-bold text-muted">Gratuit</div>
                        <div class="mb-3">
                            <span class="display-4 fw-bold">0$</span>
                            <span class="text-muted">/ mois</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check text-primary"></i><strong>1 utilisateur</strong></li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>5 Go de stockage</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Projets publics illimités</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Accès à la communauté</li>
                            <li class="mb-2 text-muted"><i class="bi bi-x"></i>Projets privés illimités</li>
                            <li class="mb-2 text-muted"><i class="bi bi-x"></i>Support dédié</li>
                            <li class="mb-2 text-muted"><i class="bi bi-x"></i>Domaine lié gratuit</li>
                            <li class="text-muted"><i class="bi bi-x"></i>Rapports mensuels</li>
                        </ul>
                        <div class="d-grid">
                            <a class="btn btn-outline-primary" href="{{ url('/sign-up') }}">Choisir le plan</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Pro -->
            <div class="col-lg-6 col-xl-4">
                <div class="card mb-5 mb-xl-0">
                    <div class="card-body p-5">
                        <div class="small text-uppercase fw-bold"><i class="bi bi-star-fill text-warning"></i> Pro</div>
                        <div class="mb-3">
                            <span class="display-4 fw-bold">9$</span>
                            <span class="text-muted">/ mois</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check text-primary"></i><strong>5 utilisateurs</strong></li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>5 Go de stockage</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Projets publics illimités</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Accès à la communauté</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Projets privés illimités</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Support dédié</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Domaine lié gratuit</li>
                            <li class="text-muted"><i class="bi bi-x"></i>Rapports mensuels</li>
                        </ul>
                        <div class="d-grid">
                            <!-- Exemple de visibilité du bouton selon condition -->
                            @auth
                                <a class="btn btn-primary" href="{{ url('/sign-up') }}">Choisir le plan</a>
                            @else
                                <button class="btn btn-primary" disabled>Connectez-vous pour choisir</button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Enterprise -->
            <div class="col-lg-6 col-xl-4">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="small text-uppercase fw-bold text-muted">Entreprise</div>
                        <div class="mb-3">
                            <span class="display-4 fw-bold">49$</span>
                            <span class="text-muted">/ mois</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2"><i class="bi bi-check text-primary"></i><strong>Utilisateurs illimités</strong></li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>5 Go de stockage</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Projets publics illimités</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Accès à la communauté</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Projets privés illimités</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Support dédié</li>
                            <li class="mb-2"><i class="bi bi-check text-primary"></i>Domaine lié illimité</li>
                            <li class="text-muted"><i class="bi bi-check text-primary"></i>Rapports mensuels</li>
                        </ul>
                        <div class="d-grid">
                            @auth
                                <a class="btn btn-outline-primary" href="{{ url('/sign-up') }}">Choisir le plan</a>
                            @else
                                <button class="btn btn-outline-primary" disabled>Connectez-vous pour choisir</button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
@endsection

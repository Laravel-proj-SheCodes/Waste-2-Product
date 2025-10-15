@extends('frontoffice.layouts.layoutfront')

@section('content')
    <header class="py-5 bg-dark text-white text-center border-bottom">
        <h1 class="fw-bold mb-0">Vérifiez votre email</h1>
        <p class="text-white-50 mb-0">Un lien de vérification a été envoyé à votre adresse email.</p>
    </header>

    <section class="py-5">
        <div class="container px-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/logo-w2p.png') }}" alt="Logo" style="height:56px" class="mb-2">
                                <div class="fs-5 fw-semibold">Waste2Product</div>
                            </div>

                            @if (session('message'))
                                <div class="alert alert-success rounded-3">
                                    {{ session('message') }}
                                </div>
                            @endif

                            <p class="text-center">
                                Avant de continuer, veuillez vérifier votre email pour un lien de vérification.
                                Si vous n'avez pas reçu l'email, nous pouvons vous en renvoyer un.
                            </p>

                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 py-2">
                                    <i class="bi bi-envelope me-1"></i> Renvoyer l'email de vérification
                                </button>
                            </form>

                            <hr class="my-4">
                            <p class="mb-0 text-center">
                                <a href="{{ route('logout') }}" class="text-success fw-semibold"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Se déconnecter
                                </a>
                            </p>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
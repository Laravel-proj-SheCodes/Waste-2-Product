@extends('frontoffice.layouts.layoutfront')

@section('content')
    <!-- Thank You Modal for Troc Offer -->
    <div class="modal fade show" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true" style="display: block; background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light rounded-4 shadow-lg">
                <div class="modal-body text-center p-5">
                    <!-- Animation -->
                    <div class="exchange-animation mb-3">
                        <div class="arrows"></div>
                    </div>

                    <h2 class="fw-bolder text-success mb-3">Merci, {{ Auth::user()->name ?? 'Cher utilisateur' }} !</h2>
                    <p class="lead mb-4 text-muted">
                        Votre <strong>offre de troc</strong> a bien été envoyée 🤝.  
                        Elle représente une belle opportunité d’échange équitable et durable.  
                        Ensemble, nous favorisons la réutilisation et la solidarité 🌍✨.
                    </p>

                    <a href="{{ url('/home/troc') }}" class="btn btn-success btn-lg">Retour au Troc</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS for Exchange Animation -->
    <style>
        .exchange-animation {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .arrows {
            width: 0; 
            height: 0;
            border-top: 20px solid transparent;
            border-bottom: 20px solid transparent;
            border-left: 30px solid #28a745;
            position: absolute;
            left: 35px;
            top: 20px;
            animation: swap 1.5s infinite alternate;
        }

        @keyframes swap {
            0% { transform: rotate(0deg) translateX(0); }
            50% { transform: rotate(180deg) translateX(10px); }
            100% { transform: rotate(360deg) translateX(0); }
        }
    </style>

    <!-- JS to Handle Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const backButton = document.querySelector('.btn-success');
            backButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
        });
    </script>
@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
    <!-- Thank You Modal -->
    <div class="modal fade show" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true" style="display: block; background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-light rounded-4 shadow-lg">
                <div class="modal-body text-center p-5">
                    <div class="heart-animation mb-3">
                        <div class="heart"></div>
                    </div>
                    <h2 class="fw-bolder text-success mb-3">Thank You, {{ Auth::user()->name ?? 'Kind Soul' }}!</h2>
                    <p class="lead mb-4 text-muted">Your generous donation is a beautiful gift to our planet. Like a tiny seed, it will grow into a greener, healthier world for all. We’re so grateful for your kindness—your efforts light up the future!</p>
                    <a href="{{ route('donate.donationpage') }}" class="btn btn-success btn-lg">Back to Donations</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS for Heart Animation -->
    <style>
        .heart-animation {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .heart {
            position: absolute;
            width: 50px;
            height: 50px;
            background-color: #ff6b6b;
            transform: rotate(-45deg);
            animation: heartbeat 1.5s infinite;
            top: 25px;
            left: 25px;
        }

        .heart:before,
        .heart:after {
            content: "";
            position: absolute;
            width: 50px;
            height: 50px;
            background-color: #ff6b6b;
            border-radius: 50%;
        }

        .heart:before {
            top: -25px;
            left: 0;
        }

        .heart:after {
            left: 25px;
            top: 0;
        }

        @keyframes heartbeat {
            0% { transform: scale(1) rotate(-45deg); }
            50% { transform: scale(1.2) rotate(-45deg); }
            100% { transform: scale(1) rotate(-45deg); }
        }
    </style>

    <!-- JavaScript to Handle Modal Close on Button Click -->
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
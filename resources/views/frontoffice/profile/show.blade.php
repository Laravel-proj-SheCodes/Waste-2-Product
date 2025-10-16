@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
        border-radius: 1rem;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .profile-icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
        color: #6c757d;
    }
    .form-section {
        transition: all 0.3s ease;
    }
    .form-section:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .alert-dismissible .btn-close {
        padding: 0.75rem;
    }
    @media (max-width: 767px) {
        .profile-overview, .form-section {
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="container py-5">
    <!-- Page Header -->
    <h2 class="mb-5 text-center fw-bold text-dark">Mon Profil</h2>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif
    @if($showWelcomeBack)
        <div class="alert alert-info alert-dismissible fade show rounded-3" role="alert">
            Bienvenue de retour ! Votre compte a été réactivé.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
        </div>
    @endif

    <div class="row">
        <!-- Profile Overview -->
        <div class="col-lg-4 col-md-12">
            <div class="profile-header">
                <h4 class="fw-bold mb-4">Aperçu du profil</h4>
                <div class="mb-3">
                    <i class="bi bi-person-fill profile-icon" aria-hidden="true"></i>
                    <span class="fw-medium">Nom : </span>{{ $user->name }}
                </div>
                <div class="mb-3">
                    <i class="bi bi-envelope-fill profile-icon" aria-hidden="true"></i>
                    <span class="fw-medium">Email : </span>{{ $user->email }}
                </div>
                <div class="mb-3">
                    <i class="bi bi-shield-fill profile-icon" aria-hidden="true"></i>
                    <span class="fw-medium">Rôle : </span>{{ $user->isAdmin() ? 'Administrateur' : 'Client' }}
                </div>
                <div>
                    <i class="bi bi-check-circle-fill profile-icon" aria-hidden="true"></i>
                    <span class="fw-medium">Statut : </span>
                    <span class="{{ $user->is_active ? 'text-success' : 'text-danger' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="col-lg-8 col-md-12">
            <!-- Update Profile Info -->
            <div class="card mb-4 shadow-sm rounded-4 form-section">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Modifier les informations</h5>
                    <form action="{{ route('profile.update') }}" method="POST" aria-labelledby="profile-info-form">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Entrez votre nom complet" aria-describedby="name-error">
                                @error('name')
                                    <div id="name-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Entrez votre email" aria-describedby="email-error">
                                @error('email')
                                    <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Mettre à jour</button>
                    </form>
                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4 shadow-sm rounded-4 form-section">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Modifier le mot de passe</h5>
                    <form action="{{ route('profile.updatePassword') }}" method="POST" aria-labelledby="password-form">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                                <input type="password" name="current_password" id="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       placeholder="Entrez votre mot de passe actuel"
                                       aria-describedby="current-password-error">
                                @error('current_password')
                                    <div id="current-password-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Entrez votre nouveau mot de passe"
                                       aria-describedby="password-error">
                                @error('password')
                                    <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" placeholder="Confirmez votre nouveau mot de passe">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Changer le mot de passe</button>
                    </form>
                </div>
            </div>

            <!-- Deactivate Account -->
            <div class="card shadow-sm rounded-4 form-section">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Désactiver le compte</h5>
                    <p class="text-muted mb-3">Désactiver votre compte masquera votre profil et vos activités. Vous pourrez le réactiver en vous reconnectant.</p>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                        Désactiver mon compte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivation Confirmation Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel">Confirmer la désactivation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir désactiver votre compte ? Vous pourrez le réactiver en vous reconnectant ultérieurement.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('profile.deactivate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Désactiver</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    // Ensure alerts auto-dismiss after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            setTimeout(() => {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>
@endsection
@endsection
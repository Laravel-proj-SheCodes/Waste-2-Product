@extends('frontoffice.layouts.layoutfront')

@section('content')
<style>
    :root {
        --emerald-600: #059669;
        --teal-600: #0d9488;
        --slate-900: #0f172a;
        --slate-600: #475569;
    }
    
    .header {
        background: linear-gradient(to right, var(--emerald-600), var(--teal-600));
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--slate-900);
        font-size: 0.95rem;
    }
    
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
    }
    
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--emerald-600);
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
    }
    
    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }
    
    .form-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: flex-end;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: var(--emerald-600);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--teal-600);
        box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3);
    }
    
    .btn-secondary {
        background: #e2e8f0;
        color: var(--slate-900);
    }
    
    .btn-secondary:hover {
        background: #cbd5e1;
    }
    
    .error-message {
        background: #fee2e2;
        border-left: 4px solid #dc3545;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        color: #991b1b;
    }
    
    .error-list {
        list-style: none;
        padding: 0;
        margin: 0.5rem 0 0 0;
    }
    
    .error-list li {
        padding: 0.25rem 0;
    }
    
    .error-list li:before {
        content: "• ";
        margin-right: 0.5rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--slate-900);
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .status-badge {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-block;
    }
    
    .info-box {
        background: #f0fdf4;
        border-left: 4px solid var(--emerald-600);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .help-text {
        font-size: 0.85rem;
        color: var(--slate-600);
        margin-top: 0.25rem;
    }
</style>

<div class="header">
    <div class="form-container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Modifier la Proposition de Transformation</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Mise à jour #{{ $propositionTransformation->id }}</p>
    </div>
</div>

<div class="form-container">
    @if ($errors->any())
        <div class="error-message">
            <strong>Des erreurs ont été détectées:</strong>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-card">
        <div class="info-box">
            <p style="margin: 0;">
                <strong>Statut Actuel:</strong>
                <span class="status-badge">⏳ {{ ucfirst($propositionTransformation->statut) }}</span>
            </p>
            <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #166534;">
                Une fois acceptée, cette proposition ne pourra plus être modifiée.
            </p>
        </div>

        <!-- IMPORTANT: Use route('front.transformation.propositions.update') NOT the old routes -->
        <form action="{{ route('front.transformation.propositions.update', $propositionTransformation->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Section 1: Post Information -->
            <div class="section-title">📋 Post Déchet</div>

            <div class="info-box">
                <strong>{{ $propositionTransformation->proposition->postDechet->title ?? 'Post Déchet' }}</strong>
                <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">{{ $propositionTransformation->proposition->postDechet->description ?? '' }}</p>
            </div>

            <!-- Section 2: Edit Transformation Description -->
            <div class="section-title">🔄 Modifier la Description</div>

            <div class="form-group">
                <label for="description" class="form-label">Description de votre Proposition *</label>
                <textarea id="description" name="description" class="form-textarea" required>{{ old('description', $propositionTransformation->description) }}</textarea>
                <p class="help-text">Mise à jour de votre approche de transformation</p>
            </div>

            <!-- Buttons -->
            <div class="form-buttons">
                <!-- IMPORTANT: Redirect to index, not show -->
                <a href="{{ route('front.transformation.propositions.index') }}" class="btn btn-secondary">
                    ← Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    ✓ Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
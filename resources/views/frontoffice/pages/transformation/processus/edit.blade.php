
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
        min-height: 100px;
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
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Modifier le Processus de Transformation</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Processus #{{ $processusTransformation->id }}</p>
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
            <strong>Post Déchet:</strong> {{ $processusTransformation->propositionTransformation->proposition->postDechet->title ?? 'Post' }}
        </div>

        <form action="{{ route('front.transformation.processus.update', $processusTransformation->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="section-title">⚙️ Détails du Processus</div>

            <div class="form-group">
                <label for="duree_estimee" class="form-label">Durée Estimée *</label>
                <input type="text" id="duree_estimee" name="duree_estimee" class="form-input" required value="{{ old('duree_estimee', $processusTransformation->duree_estimee) }}" placeholder="Ex: 15 jours, 1 mois">
                <p class="help-text">Spécifiez la durée estimée du processus</p>
            </div>

            <div class="form-group">
                <label for="cout" class="form-label">Coût Total (DT) *</label>
                <input type="number" id="cout" name="cout" class="form-input" required step="0.01" value="{{ old('cout', $processusTransformation->cout) }}" placeholder="Ex: 500.00">
                <p class="help-text">Coût estimé du processus de transformation</p>
            </div>

            <div class="form-group">
                <label for="equipements" class="form-label">Équipements Utilisés *</label>
                <textarea id="equipements" name="equipements" class="form-textarea" required placeholder="Listez les équipements nécessaires...">{{ old('equipements', $processusTransformation->equipements) }}</textarea>
                <p class="help-text">Décrivez tous les équipements nécessaires</p>
            </div>

            <div class="form-group">
                <label for="statut" class="form-label">Statut *</label>
                <select id="statut" name="statut" class="form-select" required>
                    <option value="en_attente" {{ $processusTransformation->statut === 'en_attente' ? 'selected' : '' }}>⏳ En Attente</option>
                    <option value="en_cours" {{ $processusTransformation->statut === 'en_cours' ? 'selected' : '' }}>🔄 En Cours</option>
                    <option value="terminé" {{ $processusTransformation->statut === 'terminé' ? 'selected' : '' }}>✅ Terminé</option>
                </select>
                <p class="help-text">Sélectionnez l'état actuel du processus</p>
            </div>

            <div class="form-buttons">
                <a href="{{ route('front.transformation.processus.show', $processusTransformation->id) }}" class="btn btn-secondary">
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
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
    
    .post-preview {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.5rem;
    }
    
    .post-preview-title {
        font-weight: 600;
        color: var(--slate-900);
        margin-bottom: 0.25rem;
    }
    
    .post-preview-text {
        font-size: 0.9rem;
        color: var(--slate-600);
        margin: 0.25rem 0;
    }
    
    .post-category-badge {
        display: inline-block;
        background: var(--emerald-600);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-top: 0.5rem;
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
    
    .help-text {
        font-size: 0.85rem;
        color: var(--slate-600);
        margin-top: 0.25rem;
    }
</style>

<div class="header">
    <div class="form-container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">Créer une Nouvelle Proposition de Transformation</h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">Proposez votre expertise pour transformer un déchet en ressource</p>
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
        <form action="{{ route('front.transformation.propositions.store') }}" method="POST">
            @csrf

            <!-- Section 1: Select Post -->
            <div class="section-title">📋 Sélectionner un Post Déchet</div>

            <div class="form-group">
                <label for="proposition_id" class="form-label">Post Déchet *</label>
                <select id="proposition_id" name="proposition_id" class="form-select" required onchange="updatePostPreview()">
                    <option value="">-- Sélectionner un post --</option>
                    @foreach($posts as $post)
                        <option value="{{ $post->id }}" data-title="{{ $post->postDechet->title ?? '' }}" data-description="{{ $post->postDechet->description ?? '' }}" data-category="{{ $post->postDechet->categorie ?? '' }}">
                            {{ $post->postDechet->title ?? 'Post' }} - {{ $post->postDechet->categorie ?? '' }}
                        </option>
                    @endforeach
                </select>
                <p class="help-text">Choisissez le post déchet que vous souhaitez transformer</p>
            </div>

            <!-- Post Preview -->
            <div id="postPreview" class="post-preview" style="display: none;">
                <div class="post-preview-title" id="previewTitle"></div>
                <div class="post-preview-text" id="previewDescription"></div>
                <span class="post-category-badge" id="previewCategory"></span>
            </div>

            <!-- Section 2: Transformation Details -->
            <div class="section-title">🔄 Détails de la Transformation</div>

            <div class="form-group">
                <label for="description" class="form-label">Description de votre Proposition *</label>
                <textarea id="description" name="description" class="form-textarea" required placeholder="Décrivez votre approche de transformation, les étapes, techniques utilisées...">{{ old('description') }}</textarea>
                <p class="help-text">Expliquez comment vous allez transformer ce déchet (minimum 20 caractères)</p>
            </div>

            <!-- Section 3: Hidden Fields -->
            <input type="hidden" name="transformateur_id" value="{{ Auth::id() }}">
        <!-- Section 3: Status Selection -->
<div class="section-title">✅ Statut Initial</div>

<div class="form-group">
    <label for="statut" class="form-label">Statut *</label>
    <select id="statut" name="statut" class="form-select" required>
        <option value="en_attente" selected>⏳ En Attente (Défaut)</option>
        <option value="accepté">✅ Acceptée</option>
        <option value="refusé">❌ Refusée</option>
    </select>
    <p class="help-text">Sélectionnez le statut initial de la proposition</p>
</div>

<!-- Hidden Fields -->
<input type="hidden" name="transformateur_id" value="{{ Auth::id() }}">

            <!-- Buttons -->
            <div class="form-buttons">
                <a href="{{ route('front.transformation.propositions.index') }}" class="btn btn-secondary">
                    ← Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    ✓ Créer la Proposition
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updatePostPreview() {
        const select = document.getElementById('proposition_id');
        const option = select.options[select.selectedIndex];
        const preview = document.getElementById('postPreview');
        
        if (option.value) {
            document.getElementById('previewTitle').textContent = option.dataset.title;
            document.getElementById('previewDescription').textContent = option.dataset.description;
            document.getElementById('previewCategory').textContent = option.dataset.category;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
</script>

@endsection
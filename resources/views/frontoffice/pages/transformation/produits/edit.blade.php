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
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .form-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
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
    
    .form-label .required {
        color: #dc3545;
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
        grid-column: 1 / -1;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--slate-900);
        margin: 2rem 0 1.5rem 0;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .section-title:first-child {
        margin-top: 0;
    }
    
    .help-text {
        font-size: 0.85rem;
        color: var(--slate-600);
        margin-top: 0.25rem;
    }
    
    .form-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: flex-end;
        flex-wrap: wrap;
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
    
    .file-upload-wrapper {
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .file-upload-wrapper:hover {
        border-color: var(--emerald-600);
        background: #f0fdf4;
    }
    
    .file-upload-wrapper input[type="file"] {
        display: none;
    }
    
    .file-preview {
        margin-top: 1rem;
        text-align: center;
    }
    
    .file-preview img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
    }
    
    .info-box {
        background: #f0fdf4;
        border-left: 4px solid var(--emerald-600);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
</style>

<div class="header">
    <div class="form-container">
        <h1 style="font-size: 2rem; font-weight: bold; margin: 0;">
            @if(isset($produit))
                Modifier le Produit Transformé
            @else
                Créer un Nouveau Produit Transformé
            @endif
        </h1>
        <p style="opacity: 0.9; margin: 0.5rem 0 0 0;">
            @if(isset($produit))
                Mise à jour du produit #{{ $produit->id }}
            @else
                Enregistrez votre produit issu de la transformation
            @endif
        </p>
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
        @if(isset($produit))
            <form action="{{ route('front.transformation.produits.update', $produit->id) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
        @else
            <form action="{{ route('front.transformation.produits.store') }}" method="POST" enctype="multipart/form-data">
        @endif
            @csrf

            <!-- Section 1: Processus Selection -->
            <div class="section-title">⚙️ Sélectionner le Processus</div>
            
            <div class="form-group">
                <label for="processus_id" class="form-label">Processus de Transformation <span class="required">*</span></label>
                <select id="processus_id" name="processus_id" class="form-select" required>
                    <option value="">-- Sélectionner un processus --</option>
                    @foreach($processus as $proc)
                        <option value="{{ $proc->id }}" {{ (isset($produit) && $produit->processus_id == $proc->id) ? 'selected' : '' }}>
                            Processus #{{ $proc->id }} - {{ $proc->propositionTransformation->proposition->postDechet->title ?? 'Post' }}
                        </option>
                    @endforeach
                </select>
                <p class="help-text">Sélectionnez le processus auquel ce produit appartient</p>
            </div>

            <!-- Section 2: Product Information -->
            <div class="section-title">📦 Informations du Produit</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nom_produit" class="form-label">Nom du Produit <span class="required">*</span></label>
                    <input type="text" id="nom_produit" name="nom_produit" class="form-input" required value="{{ old('nom_produit', $produit->nom_produit ?? '') }}" placeholder="Ex: Papier recyclé">
                    <p class="help-text">Donnez un nom explicite à votre produit</p>
                </div>

                <div class="form-group">
                    <label for="quantite_produite" class="form-label">Quantité Produite <span class="required">*</span></label>
                    <input type="number" id="quantite_produite" name="quantite_produite" class="form-input" required step="0.01" value="{{ old('quantite_produite', $produit->quantite_produite ?? '') }}" placeholder="Ex: 100">
                    <p class="help-text">En kg, litres, unités, etc.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description du Produit</label>
                <textarea id="description" name="description" class="form-textarea" placeholder="Décrivez votre produit, ses caractéristiques, ses usages...">{{ old('description', $produit->description ?? '') }}</textarea>
                <p class="help-text">Optionnel: Décrivez le produit en détail</p>
            </div>

            <!-- Section 3: Financial Information -->
            <div class="section-title">💰 Informations Financières</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="valeur_ajoutee" class="form-label">Valeur Ajoutée (DT) <span class="required">*</span></label>
                    <input type="number" id="valeur_ajoutee" name="valeur_ajoutee" class="form-input" required step="0.01" value="{{ old('valeur_ajoutee', $produit->valeur_ajoutee ?? '') }}" placeholder="Ex: 150.00">
                    <p class="help-text">Valeur créée par la transformation</p>
                </div>

                <div class="form-group">
                    <label for="prix_vente" class="form-label">Prix de Vente (DT) <span class="required">*</span></label>
                    <input type="number" id="prix_vente" name="prix_vente" class="form-input" required step="0.01" value="{{ old('prix_vente', $produit->prix_vente ?? '') }}" placeholder="Ex: 250.00">
                    <p class="help-text">Prix de vente proposé par unité</p>
                </div>
            </div>

            <!-- Section 4: Product Image -->
            <div class="section-title">📸 Photo du Produit</div>

            <div class="form-group">
                <label for="photo" class="form-label">Ajouter une Photo</label>
                <div class="file-upload-wrapper" onclick="document.getElementById('photo').click();">
                    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewImage(event)">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📷</div>
                    <div style="font-weight: 600; color: var(--slate-900);">Cliquez pour ajouter une photo</div>
                    <div style="color: var(--slate-600); font-size: 0.9rem; margin-top: 0.25rem;">ou glissez-déposez une image</div>
                </div>
                <p class="help-text">Formats acceptés: JPG, PNG (Max: 5MB)</p>

                @if(isset($produit) && $produit->photo)
                    <div class="file-preview">
                        <p style="color: var(--slate-600); font-size: 0.9rem;">Photo actuelle:</p>
                        <img src="{{ asset('storage/' . $produit->photo) }}" alt="{{ $produit->nom_produit }}">
                    </div>
                @endif

                <div id="imagePreview" class="file-preview"></div>
            </div>

            <!-- Buttons -->
            <div class="form-buttons">
                <a href="{{ route('front.transformation.produits.index') }}" class="btn btn-secondary">
                    ← Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    @if(isset($produit))
                        ✓ Enregistrer les modifications
                    @else
                        ✓ Créer le Produit
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <p style="color: var(--slate-600); font-size: 0.9rem;">Aperçu:</p>
                    <img src="${e.target.result}" alt="Preview">
                `;
            };
            reader.readAsDataURL(file);
        }
    }
</script>

@endsection
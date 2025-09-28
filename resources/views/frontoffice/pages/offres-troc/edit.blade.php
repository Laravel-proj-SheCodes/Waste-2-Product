@extends('frontoffice.layouts.layoutfront')

@section('content')
    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-success text-white text-center py-4">
                            <h4 class="mb-0">Modifier l'Offre</h4>
                            <p class="mb-0 text-white-50">Mettez à jour les détails de votre offre</p>
                        </div>
                        <div class="card-body p-5">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('offres-troc.update.front', $offre->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row gx-4">
                                    <div class="col-md-6 mb-3">
                                        <label for="categorie" class="form-label">Catégorie</label>
                                        <input type="text" name="categorie" id="categorie" class="form-control @error('categorie') is-invalid @enderror" value="{{ old('categorie', $offre->categorie) }}" required>
                                        @error('categorie')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantite" class="form-label">Quantité</label>
                                        <input type="number" name="quantite" id="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', $offre->quantite) }}" required>
                                        @error('quantite')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unite_mesure" class="form-label">Unité de mesure</label>
                                        <input type="text" name="unite_mesure" id="unite_mesure" class="form-control @error('unite_mesure') is-invalid @enderror" value="{{ old('unite_mesure', $offre->unite_mesure) }}" required>
                                        @error('unite_mesure')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="etat" class="form-label">État</label>
                                        <input type="text" name="etat" id="etat" class="form-control @error('etat') is-invalid @enderror" value="{{ old('etat', $offre->etat) }}" required>
                                        @error('etat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="localisation" class="form-label">Localisation</label>
                                        <input type="text" name="localisation" id="localisation" class="form-control @error('localisation') is-invalid @enderror" value="{{ old('localisation', $offre->localisation) }}" required>
                                        @error('localisation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $offre->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="photos" class="form-label">Photos</label>
                                        <input type="file" name="photos[]" id="photos" class="form-control @error('photos.*') is-invalid @enderror" multiple>
                                        @error('photos.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($offre->photos)
                                            <div class="mt-2">
                                                @foreach(json_decode($offre->photos, true) as $photo)
                                                    <img src="{{ asset('storage/' . $photo) }}" alt="Photo" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ url('/home/offres-troc', $offre->post_dechet_id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
                                    <button type="submit" class="btn btn-success">Mettre à jour</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

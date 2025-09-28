@extends('frontoffice.layouts.layoutfront') 

@section('content')
<section class="py-5">
    <div class="container px-5 my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Détails du post -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $post->titre }}</h6>
                            <small><i class="bi bi-geo-alt-fill"></i> {{ $post->localisation }}</small>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center">
                            <!-- Images -->
                            <div class="col-md-4 text-center">
                                @php
                                    $photoPaths = $post->photos ?? [];
                                @endphp

                                @if (!empty($photoPaths))
                                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                                        @foreach ($photoPaths as $index => $photo)
                                            <img src="{{ asset('storage/' . $photo) }}" 
                                                 alt="{{ $post->titre }}" 
                                                 class="rounded img-fluid"
                                                 style="max-height: 100px; cursor: pointer; object-fit: cover;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal" 
                                                 data-bs-slide-to="{{ $index }}">
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                        style="height: 120px; width: 100%; color: #2a5d3a;">
                                        Pas d'image
                                    </div>
                                @endif
                            </div>

                            <!-- Attributs -->
                            <div class="col-md-8">
                                <p class="mb-1"><strong>Description :</strong> {{ Str::limit($post->description, 100) }}</p>
                                <p class="mb-1"><strong>Quantité :</strong> {{ $post->quantite }} {{ $post->unite_mesure }}</p>
                                <p class="mb-1"><strong>État :</strong> {{ ucfirst($post->etat) }}</p>
                                <p class="mb-1"><strong>Statut :</strong> {{ ucfirst($post->statut) }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Formulaire Offre de Troc -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white text-center py-4">
                        <h4 class="mb-0">Créer une Offre de Troc</h4>
                        <p class="mb-0 text-white-50">Proposez votre offre pour ce post</p>
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

                    <form action="{{ route('offres-troc.storeFront', $post->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="from_front" value="1"> <!-- pour détecter frontoffice -->
                                                @csrf
                            <div class="row gx-4">
                                <div class="col-md-6 mb-3">
                                    <label for="categorie" class="form-label">Catégorie</label>
                                    <input type="text" id="categorie" name="categorie" class="form-control" value="{{ old('categorie') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="quantite" class="form-label">Quantité</label>
                                    <input type="number" id="quantite" name="quantite" class="form-control" value="{{ old('quantite') }}" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unite_mesure" class="form-label">Unité de Mesure</label>
                                    <select id="unite_mesure" name="unite_mesure" class="form-select" required>
                                        <option value="kg" {{ old('unite_mesure') == 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="litres" {{ old('unite_mesure') == 'litres' ? 'selected' : '' }}>litres</option>
                                        <option value="unités" {{ old('unite_mesure') == 'unités' ? 'selected' : '' }}>unités</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="etat" class="form-label">État</label>
                                    <select id="etat" name="etat" class="form-select" required>
                                        <option value="neuf" {{ old('etat') == 'neuf' ? 'selected' : '' }}>Neuf</option>
                                        <option value="usagé" {{ old('etat') == 'usagé' ? 'selected' : '' }}>Usagé</option>
                                        <option value="endommagé" {{ old('etat') == 'endommagé' ? 'selected' : '' }}>Endommagé</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="localisation" class="form-label">Localisation</label>
                                    <input type="text" id="localisation" name="localisation" class="form-control" value="{{ old('localisation') }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="photos" class="form-label">Photos</label>
                                    <input type="file" id="photos" name="photos[]" class="form-control" multiple>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ url('/home/troc') }}" class="btn btn-outline-secondary me-2">Annuler</a>
                                <button type="submit" class="btn btn-success">Créer l'Offre</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Modal Images -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark">
      <div class="modal-body p-0">
        <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            @foreach ($post->photos ?? [] as $index => $photo)
              <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $photo) }}" 
                     class="d-block w-100 rounded" 
                     style="max-height: 600px; object-fit: contain;">
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

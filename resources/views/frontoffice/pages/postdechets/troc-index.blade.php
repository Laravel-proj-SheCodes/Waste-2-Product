@extends('frontoffice.layouts.layoutfront')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
    <style>
        .troc-card {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .troc-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.14);
        }
        .troc-thumb {
            width: 100% !important; /* Ensure width is constrained to parent */
            height: 220px !important; /* Fixed height to match w2p-thumb */
            object-fit: cover;
            background: #f3f5f7;
            display: block;
            max-width: 100%; /* Prevent image from exceeding container */
        }
        .troc-chip {
            font-weight: 600;
            font-size: 0.85rem;
        }
        .troc-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .btn-outline-purple {
            border-color: #9b59b6;
            color: #9b59b6;
        }
        .btn-outline-purple:hover {
            background: #9b59b6;
            color: #fff;
        }
        /* Ensure the image container respects card boundaries */
        .troc-card .position-relative {
            width: 100%;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title" style="color: #2a5d3a;">Posts de Troc</h1>
            @auth
                <a href="{{ route('postdechets.create') }}" class="btn btn-success btn-sm">Nouveau post</a>
            @endauth
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            @forelse ($posts as $post)
                @php
                    $paths = is_array($post->photos) ? $post->photos : [];
                    $firstPath = count($paths) ? str_replace('\\', '/', $paths[0]) : null;
                    $imgUrl = $firstPath ? (Storage::disk('public')->exists(ltrim($firstPath, '/')) ? Storage::disk('public')->url(ltrim($firstPath, '/')) : (file_exists(public_path('storage/' . ltrim($firstPath, '/'))) ? asset('storage/' . ltrim($firstPath, '/')) : asset('images/placeholder.jpg'))) : asset('images/placeholder.jpg');
                    $inlineFallback = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                @endphp
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="troc-card h-100">
                        <div class="position-relative">
                            <img
                                src="{{ $imgUrl }}"
                                alt=""
                                class="troc-thumb"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ $inlineFallback }}';"
                            >
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-light text-dark troc-chip">{{ $post->categorie ?? '—' }}</span>
                            </div>
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge bg-success troc-chip">{{ $post->quantite }} {{ $post->unite_mesure }}</span>
                            </div>
                        </div>
                        <div class="p-3">
                            <h5 class="fw-semibold mb-2" style="font-size: 1.1rem;">{{ $post->titre }}</h5>
                            <p class="text-muted mb-3" style="min-height: 48px; font-size: 0.95rem;">
                                {{ \Illuminate\Support\Str::limit($post->description, 120) }}
                            </p>
                            <div class="d-flex justify-content-between troc-meta mb-3">
                                <span><i class="bi bi-geo-alt"></i> {{ $post->localisation ?? '—' }}</span>
                                <span class="text-capitalize">{{ $post->etat ?? '—' }}</span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-success btn-sm" href="{{ route('postdechets.show', $post->id) }}">Voir</a>
                                @auth
                                    @if(auth()->id() === $post->user_id)
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('postdechets.edit', $post->id) }}">Modifier</a>
                                    @endif
                                @endauth
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('offres-troc.create.front', $post->id) }}">Proposer une Offre</a>
                                <a class="btn btn-outline-purple btn-sm" href="{{ route('offres-troc.show.front', $post->id) }}">Voir Offres</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">Aucun post de troc disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $posts->withQueryString()->links() }}
        </div>
    </div>
@endsection
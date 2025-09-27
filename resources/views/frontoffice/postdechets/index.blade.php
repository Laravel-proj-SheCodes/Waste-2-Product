@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Posts déchets</h1>
      @auth
<a href="{{ route('front.waste-posts.create') }}" class="btn btn-success">Nouveau post</a>
@endauth



    </div>

    <div class="row g-4">
        @forelse ($posts as $post)
            <div class="col-md-4">
                <div class="card h-100">
                    @php $img = is_array($post->photos) && count($post->photos) ? asset('storage/'.$post->photos[0]) : null; @endphp
                    @if($img)
                        <img src="{{ $img }}" class="card-img-top" alt="photo">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $post->titre }}</h5>
                        <p class="card-text text-muted mb-2">{{ Str::limit($post->description, 120) }}</p>
                        <a class="btn btn-outline-success btn-sm"
   href="{{ route('front.waste-posts.show', $post) }}">Voir</a>

                    </div>
                </div>
            </div>
        @empty
            <p>Aucun post trouvé.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
    <a href="{{ route('front.waste-posts.index') }}" class="text-success">&larr; Retour</a>

    <div class="row mt-3">
        <div class="col-lg-7">
            @php $photos = is_array($postDechet->photos) ? $postDechet->photos : []; @endphp
            @if(count($photos))
                <img src="{{ asset('storage/'.$photos[0]) }}" class="img-fluid rounded mb-3" alt="">
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($photos as $p)
                        <img src="{{ asset('storage/'.$p) }}" style="height:80px" class="rounded border" alt="">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <h2 class="h4">{{ $postDechet->titre }}</h2>
            <p class="text-muted">{{ $postDechet->description }}</p>

            <ul class="list-unstyled small">
                <li><strong>Catégorie :</strong> {{ $postDechet->categorie }}</li>
                <li><strong>Quantité :</strong> {{ $postDechet->quantite }} {{ $postDechet->unite_mesure }}</li>
                <li><strong>État :</strong> {{ $postDechet->etat }}</li>
                <li><strong>Localisation :</strong> {{ $postDechet->localisation }}</li>
            </ul>

            @auth
                @if(auth()->id() === $postDechet->user_id)
                    <a href="{{ route('front.waste-posts.edit', $postDechet) }}"
                       class="btn btn-outline-success me-2">Modifier</a>

                    <form class="d-inline"
                          method="POST"
                          action="{{ route('front.waste-posts.destroy', $postDechet) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger"
                                onclick="return confirm('Supprimer ?')">Supprimer</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
@endsection

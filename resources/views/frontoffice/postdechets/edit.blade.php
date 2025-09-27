@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
    <h1 class="h4 mb-4">Modifier le post</h1>

    <form method="POST"
          action="{{ route('front.waste-posts.update', $postDechet) }}"
          enctype="multipart/form-data"
          class="row g-3">
        @csrf
        @method('PUT')

        @include('frontoffice.postdechets.partials.form', ['post' => $postDechet])

        <div class="col-12">
            <button class="btn btn-success">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection

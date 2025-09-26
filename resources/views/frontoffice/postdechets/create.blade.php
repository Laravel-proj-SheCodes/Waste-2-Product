@extends('frontoffice.layouts.layoutfront')

@section('content')
<div class="container py-5">
    <h1 class="h4 mb-4">Créer un Post Déchet</h1>

    <form method="POST"
      action="{{ route('front.waste-posts.store') }}"
      enctype="multipart/form-data"
      class="row g-3">
    @csrf
    @include('frontoffice.postdechets.partials.form', ['post' => null])

    <div class="col-12">
        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('front.waste-posts.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
    </div>
</form>

</div>
@endsection

@extends('frontoffice.layouts.layoutfront')

@section('content')
    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-success text-white text-center py-4">
                            <h4 class="mb-0">Modifier la Transaction</h4>
                            <p class="mb-0 text-white-50">Mettez à jour le statut de livraison ou l'évaluation</p>
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

                            <form action="{{ route('transactions-troc.update.front', $transaction->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row gx-4">
                                    <div class="col-12 mb-3">
                                        <label for="statut_livraison" class="form-label">Statut de Livraison</label>
                                        <select name="statut_livraison" id="statut_livraison" class="form-select @error('statut_livraison') is-invalid @enderror" required>
                                            <option value="en_cours" {{ old('statut_livraison', $transaction->statut_livraison) == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                            <option value="livre" {{ old('statut_livraison', $transaction->statut_livraison) == 'livre' ? 'selected' : '' }}>Livré</option>
                                            <option value="annule" {{ old('statut_livraison', $transaction->statut_livraison) == 'annule' ? 'selected' : '' }}>Annulé</option>
                                        </select>
                                        @error('statut_livraison')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="evaluation_mutuelle" class="form-label">Évaluation Mutuelle (Optionnel)</label>
                                        <textarea name="evaluation_mutuelle" id="evaluation_mutuelle" class="form-control @error('evaluation_mutuelle') is-invalid @enderror" rows="4">{{ old('evaluation_mutuelle', $transaction->evaluation_mutuelle) }}</textarea>
                                        @error('evaluation_mutuelle')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('transactions-troc.show.front', $transaction->id) }}" class="btn btn-outline-secondary me-2">Annuler</a>
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
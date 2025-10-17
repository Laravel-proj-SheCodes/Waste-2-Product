<h3>{{ $status === 'accepted' ? 'Félicitations !' : 'Information importante' }}</h3>

<p>
    Votre offre pour le post <a href="{{ route('postdechets.show', $post->id) }}">{{ $post->titre }}</a> a été
    <strong>{{ $status === 'accepted' ? 'acceptée' : 'refusée' }}</strong>.
</p>

@if($status === 'accepted')
<p>
    Vous pouvez consulter vos transactions ici : <a href="{{ url('/home/transactions-troc') }}" >Mes transactions</a>
</p>
@endif

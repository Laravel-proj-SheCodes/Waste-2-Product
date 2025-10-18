@extends('backoffice.layouts.layout')

@section('content')
<div class="container-fluid py-3">

  <div class="row mb-3">
    <div class="col-md-8">
      <h3 class="mb-1">Tableau de bord</h3>
      <p class="text-muted mb-0">Vue d’ensemble Waste-2-Product</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
    </div>
  </div>

  <div class="row g-3">
    @php $cards = [
      ['label'=>'Utilisateurs','val'=>$kpis['users']],
      ['label'=>'Waste Posts','val'=>$kpis['wastePosts']],
      ['label'=>'Posts de Troc','val'=>$kpis['trocPosts']],
      ['label'=>'Propositions','val'=>$kpis['proposals']],
    ]; @endphp
    @foreach($cards as $c)
      <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100"><div class="card-body">
          <p class="text-sm text-muted mb-1">{{ $c['label'] }}</p>
          <h4 class="mb-0">{{ $c['val'] }}</h4>
        </div></div>
      </div>
    @endforeach
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-8">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Évolution des posts</h6></div>
        <div class="card-body"><canvas id="postsLineChart" height="100"></canvas></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Répartition</h6></div>
        <div class="card-body"><canvas id="shareDonutChart" height="220"></canvas></div>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between">
          <h6 class="mb-0">Derniers Waste Posts</h6>
          <a class="text-sm" href="{{ route('postdechets.index') }}">Tout voir</a>
        </div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead><tr><th>Id</th><th>Titre</th><th>Type</th><th>Créé</th></tr></thead>
            <tbody>
              @forelse($recentPosts as $p)
                <tr>
                  <td>#{{ $p->id }}</td>
                  <td class="text-truncate" style="max-width:220px">{{ $p->titre }}</td>
                  <td><span class="badge bg-success-subtle text-success">{{ $p->type_post }}</span></td>
                  <td>{{ $p->created_at->diffForHumans() }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Aucun post</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-header bg-white border-0"><h6 class="mb-0">Propositions récentes</h6></div>
        <ul class="list-group list-group-flush">
          @forelse($recentProps as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold">#{{ $r->id }} — {{ $r->postDechet->titre ?? 'Post supprimé' }}</div>
                <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small>
              </div>
              <span class="badge {{ ($r->statut==='accepted')?'bg-success':(($r->statut==='rejected')?'bg-danger':'bg-secondary') }}">
                {{ $r->statut ?? 'pending' }}
              </span>
            </li>
          @empty
            <li class="list-group-item text-center text-muted">Aucune proposition</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($chart['labels']);
const posts  = @json($chart['posts']);
const troc   = @json($chart['troc']);

new Chart(document.getElementById('postsLineChart'), {
  type: 'line',
  data: { labels, datasets:[
    { label:'Posts', data:posts, tension:.3, borderWidth:2, fill:false },
    { label:'Troc',  data:troc,  tension:.3, borderWidth:2, fill:false }
  ]},
  options:{ responsive:true, maintainAspectRatio:false }
});

new Chart(document.getElementById('shareDonutChart'), {
  type: 'doughnut',
  data: { labels:['Posts','Propositions','Transactions','Donations'],
    datasets:[{ data:[
      {{ $kpis['wastePosts'] }},
      {{ $kpis['proposals'] }},
      {{ $kpis['transactions'] }},
      {{ $kpis['donations'] }}
    ]}]},
  options:{ responsive:true, plugins:{ legend:{ position:'bottom' } } }
});
</script>
@endpush

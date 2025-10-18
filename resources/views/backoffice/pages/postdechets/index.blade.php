@extends('backoffice.layouts.layout')

@section('content')
<div class="container py-4">

  {{-- Titre + actions --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h4 class="m-0">Posts Déchets</h4>
    <a href="{{ route('postdechets.create') }}" class="btn btn-primary">Nouveau</a>
  </div>

  @if(session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
  @endif

  {{-- ===== KPIs ===== --}}
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted">Total posts</div>
          <div class="h3 mb-0">{{ number_format($totalPosts) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted">Quantité totale</div>
          <div class="h3 mb-0">{{ number_format($totalQuant) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted">CO₂ évité (kg)</div>
          <div class="h3 mb-0">{{ number_format($co2SavedKg, 1) }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Graphiques ===== --}}
  <div class="row g-3 mb-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><strong>Répartition par statut</strong></div>
        <div class="card-body"><canvas id="statusChart" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><strong>Répartition par catégorie</strong></div>
        <div class="card-body"><canvas id="catChart" height="160"></canvas></div>
      </div>
    </div>
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white"><strong>Créations par mois (12 derniers)</strong></div>
        <div class="card-body"><canvas id="monthlyChart" height="90"></canvas></div>
      </div>
    </div>
  </div>

  {{-- Compteur total --}}
  <div class="text-muted mb-2">
    {{ number_format($posts->total()) }} résultat{{ $posts->total() > 1 ? 's' : '' }}
  </div>

  {{-- ===== Table ===== --}}
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>Titre</th>
          <th>Type</th>
          <th>Catégorie</th>
          <th class="text-end">Qté</th>
          <th>Statut</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($posts as $p)
          <tr>
            <td class="fw-semibold">{{ $p->titre }}</td>
            <td><span class="badge text-bg-light">{{ $p->type_post ?? '—' }}</span></td>
            <td>{{ $p->categorie ?? '—' }}</td>
            <td class="text-end">{{ $p->quantite }} {{ $p->unite_mesure }}</td>
            <td>
              @php
                $status = (string)($p->statut ?? '—');
                $map = ['approved'=>'success','en_attente'=>'warning','rejeté'=>'danger','rejete'=>'danger'];
                $color = $map[strtolower($status)] ?? 'secondary';
              @endphp
              <span class="badge text-bg-{{ $color }}">{{ $status }}</span>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-info" href="{{ route('postdechets.show', $p) }}">Voir</a>
              <a class="btn btn-sm btn-warning" href="{{ route('postdechets.edit', $p) }}">Éditer</a>
              <form class="d-inline" method="POST" action="{{ route('postdechets.destroy', $p) }}">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">Suppr.</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Aucun post trouvé.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-3">
    {{ $posts->withQueryString()->links() }}
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Données injectées depuis le controller
  const stLabels = @json($chartStatusLabels);
  const stCounts = @json($chartStatusCounts);
  const catLabels = @json($chartCatLabels);
  const catCounts = @json($chartCatCounts);
  const moLabels = @json($chartMonthlyLabels);
  const moCounts = @json($chartMonthlyCounts);

  new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: stLabels, datasets: [{ data: stCounts }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('catChart'), {
    type: 'bar',
    data: { labels: catLabels, datasets: [{ label: 'Posts', data: catCounts }] },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });

  new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: { labels: moLabels, datasets: [{ label: 'Posts / mois', data: moCounts, tension: .3 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });
</script>
@endpush

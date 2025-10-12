@php
  $notifications = auth()->user()?->unreadNotifications()->latest()->limit(10)->get();
@endphp

<div class="dropdown">
  <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
    🔔
    @if($notifications && $notifications->count())
      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ $notifications->count() }}
      </span>
    @endif
  </button>

  <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 300px;">
    <div class="list-group list-group-flush">
      @forelse($notifications as $notif)
        <a href="{{ route('notifications.read', $notif->id) }}" class="list-group-item list-group-item-action">
          <div class="fw-semibold">{{ $notif->data['message'] ?? 'Nouvelle notification' }}</div>
          <div class="small text-muted">{{ $notif->created_at->diffForHumans() }}</div>
        </a>
      @empty
        <div class="list-group-item text-muted">Aucune notification</div>
      @endforelse
    </div>
  </div>
</div>

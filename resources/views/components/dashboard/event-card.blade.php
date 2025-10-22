<div class="card stats-card widget-card-1">
    <div class="card-body">
        <div class="media d-flex align-items-center">
            <div class="widget-icon bg-info text-white"><i class="fas fa-calendar-alt"></i></div>
            <div class="media-body ms-3">
                <h4 class="mb-0">{{ $event->title }}</h4>
                <p class="mb-0 text-muted">
                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                    • {{ $event->location }}
                </p>
            </div>
        </div>
        <ul class="list-group list-group-flush mt-3">
            <li class="list-group-item d-flex justify-content-between">Going <span>{{ $event->going_count ?? 0 }}</span></li>
            <li class="list-group-item d-flex justify-content-between">Interested <span>{{ $event->interested_count ?? 0 }}</span></li>
            <li class="list-group-item d-flex justify-content-between">Invites Sent <span>{{ $event->invites->count() }}</span></li>
        </ul>
    </div>
</div>

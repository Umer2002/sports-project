<div class="bg-dark p-4 rounded-xl text-white">

    <!-- Right Sidebar Column: Coaches & Reminders -->
    <div class="col-xl-12 col-lg-12">
        <!-- Coaches & Management List -->
        <div class="card mb-4">
            <div class="card-header fw-semibold">Coaches & Management</div>
            <div class="card-body p-2">
                <input type="search" placeholder="Search..." class="form-control form-control-sm mb-3">
                <ul class="list-unstyled mb-0">
                    @foreach($coaches as $coach)
                        <li class="d-flex align-items-center py-1">
                            <img src="{{ $coach->avatarUrl }}" alt="{{ $coach->name }}" class="rounded-circle me-2" width="32" height="32">
                            <div>
                                <strong>{{ $coach->first_name }}</strong><br>
                                <small class="text-muted">{{ $coach->last_name }}</small>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Reminders Block -->

    </div>
</div>

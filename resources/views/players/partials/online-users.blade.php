<div class="card bg-dark text-white">
    <div class="card-body">
        <input type="text" class="form-control mb-3" placeholder="Search users...">
        @foreach($users as $user)
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset($user->avatar ?? 'images/avatar-default.png') }}" class="rounded-circle me-2" width="40" height="40">
                <div>
                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong><br>
                    <small class="{{ $user->isOnline() ? 'text-success' : 'text-danger' }}">
                        {{ $user->isOnline() ? 'online' : 'left ' . $user->last_seen->diffForHumans() }}
                    </small>
                </div>
            </div>
        @endforeach
    </div>
</div>

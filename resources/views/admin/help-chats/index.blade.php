@extends('layouts.admin')

@section('title', 'Help Chat Sessions')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Help Chat Sessions</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Intent</th>
                            <th>Stage</th>
                            <th>Status</th>
                            <th>Messages</th>
                            <th>Last Interaction</th>
                            <th>Tickets</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sessions as $session)
                            <tr>
                                <td>#{{ $session->id }}</td>
                                <td>
                                    @if ($session->user)
                                        <div class="fw-semibold">{{ $session->user->name }}</div>
                                        <div class="text-muted small">{{ $session->user->email }}</div>
                                    @elseif ($session->player)
                                        <div class="fw-semibold">{{ $session->player->name }}</div>
                                        <div class="text-muted small">Player</div>
                                    @else
                                        <span class="text-muted">Guest</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $session->role_label ?? \Illuminate\Support\Str::title($session->role ?? 'Unknown') }}</div>
                                    @if ($session->intent_group)
                                        <div class="text-muted small">{{ \Illuminate\Support\Str::title(str_replace('_', ' ', $session->intent_group)) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $session->intent_label ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $session->intent ?? '')) }}</div>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $session->stage ?: '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $session->status === 'escalated' ? 'warning' : ($session->status === 'closed' ? 'secondary' : 'success') }}">
                                        {{ \Illuminate\Support\Str::title($session->status) }}
                                    </span>
                                </td>
                                <td>{{ $session->messages_count }}</td>
                                <td>
                                    <div>{{ optional($session->last_interaction_at)->diffForHumans() ?? '—' }}</div>
                                    <div class="text-muted small">{{ optional($session->last_interaction_at)->format('M j, Y g:i A') }}</div>
                                </td>
                                <td>
                                    @forelse ($session->tickets as $ticket)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-info text-dark">{{ $ticket->ticket_number }}</span>
                                            <span class="text-muted small">{{ \Illuminate\Support\Str::title($ticket->status) }}</span>
                                        </div>
                                    @empty
                                        <span class="text-muted small">None</span>
                                    @endforelse
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No help chat sessions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $sessions->links() }}
        </div>
    </div>
@endsection

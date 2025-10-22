@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manage Clubs</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('volunteer.clubs.importForm') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-upload"></i> Import CSV</a>
            <a href="{{ route('volunteer.clubs.exportLogins') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-csv"></i> Export Login CSV</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Players (filtered)</div>
                            <div class="h4 mb-0">{{ number_format($totalPlayers) }}</div>
                        </div>
                        <i class="fas fa-users" style="font-size:24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form class="row g-2 mb-3" method="GET" action="{{ route('volunteer.clubs.index') }}">
        <div class="col-md-4">
            <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Search by name or email">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Players</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clubs as $club)
                            <tr>
                                <td>{{ $club->name }}</td>
                                <td>{{ $club->email }}</td>
                                <td>{{ $club->players_count }}</td>
                                <td>
                                    @if($club->is_registered)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <form class="d-inline resend-invite" method="POST" action="{{ route('volunteer.clubs.resendInvite', $club) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-paper-plane"></i> Send/Remind</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4">No clubs found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($clubs->hasPages())
        <div class="card-footer">{{ $clubs->links() }}</div>
        @endif
    </div>
</div>
@endsection

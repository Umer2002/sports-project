@extends('layouts.admin')
@section('title', 'Donations Management')
@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Donations Management</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-body bg-light">
                <h5>Total Donations</h5>
                <p class="h4">${{ number_format($stats['total_donations'], 2) }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body bg-light">
                <h5>Completed Donations</h5>
                <p class="h4">{{ $stats['total_count'] }}</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body bg-light">
                <h5>Pending Donations</h5>
                <p class="h4">{{ $stats['pending_count'] }}</p>
            </div>
        </div>
    </div>
    
    <!-- Filters and Export -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Donations</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.donations.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-download me-1"></i>Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-3">
                <div class="col-md-3">
                    <select name="club_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Clubs</option>
                        @foreach($clubs as $club)
                            <option value="{{ $club->id }}" @if(request('club_id') == $club->id) selected @endif>
                                {{ $club->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" @if(request('status') == 'pending') selected @endif>Pending</option>
                        <option value="completed" @if(request('status') == 'completed') selected @endif>Completed</option>
                        <option value="failed" @if(request('status') == 'failed') selected @endif>Failed</option>
                        <option value="cancelled" @if(request('status') == 'cancelled') selected @endif>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by donor name, email, or club" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Donor</th>
                            <th>Club</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donations as $donation)
                            <tr>
                                <td>{{ $donation->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $donation->donor_name }}</strong><br>
                                        <small class="text-muted">{{ $donation->donor_email }}</small>
                                    </div>
                                </td>
                                <td>{{ $donation->club->name }}</td>
                                <td>{{ $donation->formatted_amount }}</td>
                                <td>
                                    <span class="badge bg-{{ $donation->status === 'completed' ? 'success' : ($donation->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($donation->message)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $donation->message }}">
                                            {{ $donation->message }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.donations.show', $donation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No donations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $donations->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

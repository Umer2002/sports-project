@extends('layouts.coach-dashboard')

@section('title', 'Tournaments')
@section('page_title', 'Tournaments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tournament Management</h5>
                    <div class="card-tools">
                        <span class="badge bg-primary">{{ $tournaments->total() }} Tournaments</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($tournaments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tournament</th>
                                        <th>Sport</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Teams</th>
                                        <th>Matches</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tournaments as $tournament)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($tournament->logo)
                                                            <img src="{{ Storage::url($tournament->logo) }}" 
                                                                 alt="{{ $tournament->name }}" 
                                                                 class="rounded" 
                                                                 width="40" height="40">
                                                        @else
                                                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="fas fa-trophy"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0" style="color: #000;">{{ $tournament->name }}</h6>
                                                        <small class="text-muted">{{ $tournament->description ?? 'No description' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($tournament->sport)
                                                    <span class="badge bg-info">{{ $tournament->sport->name }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'TBD' }}</td>
                                            <td>{{ $tournament->end_date ? $tournament->end_date->format('M d, Y') : 'TBD' }}</td>
                                            <td>
                                                @php
                                                    $status = $tournament->status ?? 'upcoming';
                                                    $statusColors = [
                                                        'upcoming' => 'warning',
                                                        'ongoing' => 'success',
                                                        'completed' => 'secondary',
                                                        'cancelled' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $tournament->teams->count() }} teams
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $tournament->matches->count() }} matches
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('coach.tournaments.show', $tournament) }}" 
                                                       class="btn btn-sm btn-outline-primary composer-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $tournaments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-trophy fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">No Tournaments Found</h5>
                            <p class="text-muted">Your teams are not currently participating in any tournaments.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

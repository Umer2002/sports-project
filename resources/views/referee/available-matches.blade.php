@extends('layouts.referee-dashboard')

@section('title', 'Available Matches')

@section('content')
<div class="card">
    <div class="header">
        <h2>Available Matches</h2>
        @if(!$canApply)
            <div class="alert alert-warning">
                <strong>Daily Limit Reached:</strong> You have applied for {{ $todayApplications }} matches today. You can apply for up to 2 matches per day.
            </div>
        @endif
    </div>
    <div class="body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Teams</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Required Level</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eligibleMatches as $match)
                    <tr>
                        <td>
                            @if($match->tournament)
                                <strong>{{ $match->tournament->name }}</strong>
                            @else
                                <span class="text-muted">No Tournament</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $match->homeClub->name }}</strong> vs <strong>{{ $match->awayClub->name }}</strong>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($match->match_date)->format('M d, Y') }}</td>
                        <td>
                            @try
                                {{ \Carbon\Carbon::parse($match->match_time)->format('g:i A') }}
                            @catch(\Exception $e)
                                {{ $match->match_time }}
                            @endtry
                        </td>
                        <td>{{ $match->venue ?? 'TBD' }}</td>
                        <td>
                            @if($match->required_referee_level)
                                <span class="badge bg-info">Level {{ $match->required_referee_level }}</span>
                            @else
                                <span class="text-muted">Any Level</span>
                            @endif
                        </td>
                        <td>
                            @if($canApply)
                                <form method="POST" action="{{ route('referee.matches.apply', $match) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-primary btn-sm" type="submit">
                                        <i class="fas fa-hand-paper"></i> Apply
                                    </button>
                                </form>
                            @else
                                <button class="btn btn-secondary btn-sm" disabled>
                                    <i class="fas fa-ban"></i> Limit Reached
                                </button>
                            @endif
                            <a href="{{ route('referee.matches.view', $match) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No matches available</h5>
                                <p class="text-muted">There are currently no matches that match your availability and requirements.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($eligibleMatches->count() > 0)
<div class="card mt-3">
    <div class="header">
        <h3>Application Priority System</h3>
    </div>
    <div class="body">
        <div class="row">
            <div class="col-md-6">
                <h5>How it works:</h5>
                <ul>
                    <li><strong>First-come, first-serve:</strong> Early applications get priority</li>
                    <li><strong>Referee level:</strong> Higher level referees get bonus points</li>
                    <li><strong>Performance history:</strong> Good track record increases priority</li>
                    <li><strong>Daily limit:</strong> Maximum 2 applications per day</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5>Priority factors:</h5>
                <ul>
                    <li>✅ Referee level (×10 points)</li>
                    <li>✅ Completed matches (×5 points each)</li>
                    <li>❌ Recent cancellations (-20 points each)</li>
                    <li>⏰ Application timestamp (tiebreaker)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

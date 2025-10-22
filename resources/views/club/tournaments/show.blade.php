@extends('layouts.club-dashboard')

@section('title', 'Tournament Details')
@section('page_title', 'Tournament Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card rounded-2xl border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-trophy me-2"></i>
                            {{ $tournament->name }}
                        </h4>
                        <div>
                            <a href="{{ route('club.tournaments.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Tournaments
                            </a>
                            @php
                                $scheduleAllowed = ! $tournament->registration_cutoff_date || now()->greaterThanOrEqualTo($tournament->registration_cutoff_date);
                            @endphp
                            @if($scheduleAllowed)
                                <a href="{{ route('club.tournaments.schedule', $tournament) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-calendar-alt me-1"></i> Manage Schedule
                                </a>
                            @else
                                <span class="badge bg-warning text-dark">Scheduling opens {{ $tournament->registration_cutoff_date->format('M d, Y') }}</span>
                            @endif
                            <a href="{{ route('club.tournaments.edit', $tournament) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Tournament
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('invite_link'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-link me-2"></i>
                            Invitation link generated:
                            <a href="{{ session('invite_link') }}" class="fw-semibold text-decoration-underline" target="_blank" rel="noopener">{{ session('invite_link') }}</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Tournament Information --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Tournament Information
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-semibold">Host Club:</td>
                                        <td>{{ $tournament->hostClub->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Start Date:</td>
                                        <td>{{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">End Date:</td>
                                        <td>{{ $tournament->end_date ? $tournament->end_date->format('M d, Y') : 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Location:</td>
                                        <td>{{ $tournament->location }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Registration Cutoff:</td>
                                        <td>{{ $tournament->registration_cutoff_date ? $tournament->registration_cutoff_date->format('M d, Y') : 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Joining Fee:</td>
                                        <td>${{ number_format($tournament->joining_fee, 2) }} ({{ $tournament->joining_type === 'per_team' ? 'Per Team' : 'Per Club' }})</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Max Teams:</td>
                                        <td>{{ $tournament->max_teams ?? 'No limit' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Entry Fee:</td>
                                        <td>{{ $tournament->entry_fee ? '$' . number_format($tournament->entry_fee, 2) : 'Free' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-list me-2"></i>Tournament Details
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-semibold">Format:</td>
                                        <td>{{ $tournament->format->name ?? 'Not specified' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Status:</td>
                                        <td>
                                            @if($tournament->start_date && $tournament->start_date->isFuture())
                                                <span class="badge bg-warning">Upcoming</span>
                                            @elseif($tournament->end_date && $tournament->end_date->isPast())
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-info">Active</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Created:</td>
                                        <td>{{ $tournament->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold">Last Updated:</td>
                                        <td>{{ $tournament->updated_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($tournament->description)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-align-left me-2"></i>Description
                            </h5>
                            <div class="bg-light p-3 rounded">
                                {{ $tournament->description }}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Club Invitations --}}
                    <div class="row mb-5">
                        <div class="col-xl-5">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Invite New Clubs
                            </h5>
                            <div class="border rounded-2xl p-3 bg-light">
                                <p class="small text-muted mb-3">
                                    Invite clubs that aren't on Play2Earn yet. When they register through your link you'll earn a $1,000 reward, payable 90 days after they join.
                                </p>
                                <form method="POST" action="{{ route('club.tournaments.invites.store', $tournament) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Club Name *</label>
                                        <input type="text" name="invitee_club_name" value="{{ old('invitee_club_name') }}" class="form-control @error('invitee_club_name') is-invalid @enderror" placeholder="Club to invite" required>
                                        @error('invitee_club_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Contact Email (optional)</label>
                                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="coach@club.com">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notes (optional)</label>
                                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Add a message the club should know">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-1"></i> Generate Invite Link
                                    </button>
                                </form>
                                @if(!$scheduleAllowed)
                                    <div class="alert alert-warning mt-3 mb-0 small">
                                        <i class="fas fa-hourglass-half me-1"></i>
                                        Scheduling unlocks {{ optional($tournament->registration_cutoff_date)->format('M d, Y') }}. Use this time to invite clubs and collect registrations.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-xl-7 mt-4 mt-xl-0">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-list-check me-2"></i>Invitation Status
                            </h5>
                            @php
                                $inviteStatusLabels = [
                                    App\Models\ClubInvite::STATUS_PENDING => 'Pending',
                                    App\Models\ClubInvite::STATUS_REGISTERED => 'Registered',
                                    App\Models\ClubInvite::STATUS_DECLINED => 'Declined',
                                ];
                                $rewardStatusLabels = [
                                    App\Models\ClubInvite::REWARD_STATUS_PENDING => 'Pending',
                                    App\Models\ClubInvite::REWARD_STATUS_SCHEDULED => 'Scheduled',
                                    App\Models\ClubInvite::REWARD_STATUS_PAID => 'Paid',
                                ];
                            @endphp
                            <div class="table-responsive border rounded-2xl">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Club</th>
                                            <th>Status</th>
                                            <th>Reward</th>
                                            <th>Payout</th>
                                            <th>Registered Club</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invites as $invite)
                                            <tr>
                                                <td class="fw-semibold">
                                                    {{ $invite->invitee_club_name }}
                                                    <div class="small text-muted">{{ $invite->email ?: 'No email provided' }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $invite->status === App\Models\ClubInvite::STATUS_REGISTERED ? 'bg-success' : ($invite->status === App\Models\ClubInvite::STATUS_PENDING ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                        {{ $inviteStatusLabels[$invite->status] ?? ucfirst($invite->status) }}
                                                    </span>
                                                    @if($invite->registered_at)
                                                        <div class="small text-muted">{{ $invite->registered_at->format('M d, Y') }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($invite->reward_amount > 0)
                                                        ${{ number_format((float) $invite->reward_amount, 2) }}
                                                        <div class="badge bg-light text-dark mt-1">
                                                            {{ $rewardStatusLabels[$invite->reward_status] ?? ucfirst($invite->reward_status) }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not yet earned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($invite->reward_payout_scheduled_at)
                                                        {{ $invite->reward_payout_scheduled_at->format('M d, Y') }}
                                                    @else
                                                        <span class="text-muted">TBD</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $invite->registeredClub?->name ?? 'Not registered yet' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No invitations have been sent yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Registered Clubs --}}
                    <div class="row mb-5">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Registered Clubs
                            </h5>
                            <div class="table-responsive border rounded-2xl">
                                <table class="table table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Club</th>
                                            <th>Status</th>
                                            <th>Teams Purchased</th>
                                            <th>Amount Paid</th>
                                            <th>Teams Added</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($registrations as $registration)
                                            @php
                                                $clubTeamsAttached = $tournament->teams->where('club_id', $registration->club_id)->count();
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold">{{ $registration->club->name }}</td>
                                                <td>
                                                    <span class="badge bg-info text-uppercase">{{ str_replace('_', ' ', $registration->status) }}</span>
                                                    @if($registration->paid_at)
                                                        <div class="small text-muted">Paid {{ $registration->paid_at->format('M d, Y') }}</div>
                                                    @endif
                                                </td>
                                                <td>{{ $registration->team_quantity ?: '—' }}</td>
                                                <td>${{ number_format((float) $registration->amount_paid, 2) }}</td>
                                                <td>{{ $clubTeamsAttached }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No clubs have completed registration yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($scheduleAllowed)
                    {{-- Tournament Matches --}}
                    <div class="row">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-futbol me-2"></i>Tournament Matches
                            </h5>
                            @if($matches->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Match</th>
                                                <th>Teams</th>
                                                <th>Date</th>
                                                <th>Referee</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($matches as $match)
                                                @php
                                                    $start = $match->match_date
                                                        ? \Carbon\Carbon::parse($match->match_date->format('Y-m-d') . ' ' . ($match->match_time ?? '00:00:00'))
                                                        : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ 'Match ' . $loop->iteration }}</td>
                                                    <td>
                                                        @if($match->homeClub && $match->awayClub)
                                                            {{ $match->homeClub->name }} vs {{ $match->awayClub->name }}
                                                        @else
                                                            <span class="text-muted">Teams TBD</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($match->match_date)
                                                            {{ $match->match_date->format('M d, Y') }}
                                                            @if($match->match_time)
                                                                {{ \Carbon\Carbon::parse($match->match_time)->format('g:i A') }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">TBD</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($match->referee)
                                                            {{ $match->referee->full_name }}
                                                        @else
                                                            <span class="text-muted">Not assigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(! $start)
                                                            <span class="badge bg-secondary">TBD</span>
                                                        @elseif($start->isFuture())
                                                            <span class="badge bg-info">Scheduled</span>
                                                        @else
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($scheduleAllowed)
                                                            <a href="{{ route('club.tournaments.schedule', $tournament) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-calendar-alt"></i> Manage Schedule
                                                            </a>
                                                        @else
                                                            <span class="badge bg-secondary">Opens {{ $tournament->registration_cutoff_date->format('M d, Y') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($scheduledMatches->isNotEmpty())
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Matches are scheduled in the calendar but haven't been synced to match records yet.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Match</th>
                                                <th>Teams</th>
                                                <th>Date</th>
                                                <th>Venue</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($scheduledMatches as $match)
                                                @php
                                                    $scheduledAt = $match['match_date']
                                                        ? \Carbon\Carbon::parse($match['match_date'] . ' ' . ($match['match_time'] ?? '00:00:00'))
                                                        : null;
                                                @endphp
                                                <tr>
                                                    <td>{{ 'Match ' . $loop->iteration }}</td>
                                                    <td>{{ $match['home_name'] }} vs {{ $match['away_name'] }}</td>
                                                    <td>
                                                        @if($scheduledAt)
                                                            {{ $scheduledAt->format('M d, Y g:i A') }}
                                                        @else
                                                            <span class="text-muted">TBD</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $match['venue_name'] ?? 'TBD' }}</td>
                                                    <td>
                                                        @if(! $scheduledAt)
                                                            <span class="badge bg-secondary">TBD</span>
                                                        @elseif($scheduledAt->isFuture())
                                                            <span class="badge bg-info">Scheduled</span>
                                                        @else
                                                            <span class="badge bg-success">Completed</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($scheduleAllowed)
                                                            <a href="{{ route('club.tournaments.schedule', $tournament) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-sync"></i> Sync Matches
                                                            </a>
                                                        @else
                                                            <span class="badge bg-secondary">Opens {{ $tournament->registration_cutoff_date->format('M d, Y') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No matches have been created for this tournament yet.
                                </div>
                            @endif
                        </div>
                    </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-lock me-2"></i>
                            Tournament scheduling opens on {{ optional($tournament->registration_cutoff_date)->format('M d, Y') ?? 'the registration cutoff date' }}. Continue inviting clubs and collecting registrations until then.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.club-dashboard')

@section('title', 'Tournament Registration')
@section('page_title', 'Tournament Registration Summary')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            @if(session('invite_link'))
                <div class="alert alert-success">{{ session('invite_link') }}</div>
            @endif

            <div class="card border-0 shadow-sm rounded-2xl mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        {{ $tournament->name }}
                    </h4>
                    <span class="badge bg-light text-primary text-uppercase">{{ str_replace('_', ' ', $registration->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="border rounded-2xl p-3 bg-light h-100">
                                <div class="text-muted small text-uppercase mb-1">Fee Type</div>
                                <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $registration->joining_type) }}</div>
                                <div>${{ number_format((float) $registration->joining_fee, 2) }} {{ $registration->joining_type === 'per_team' ? 'per team' : 'flat' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-2xl p-3 bg-light h-100">
                                <div class="text-muted small text-uppercase mb-1">Amount Paid</div>
                                <div class="fw-semibold fs-5">${{ number_format((float) $registration->amount_paid, 2) }}</div>
                                <div class="text-muted small">Recorded {{ $registration->paid_at ? $registration->paid_at->format('M d, Y') : 'today' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-2xl p-3 bg-light h-100">
                                <div class="text-muted small text-uppercase mb-1">Teams Added</div>
                                @php
                                    $attachedCount = count($attachedTeamIds);
                                    $remainingSlots = $registration->joining_type === 'per_team'
                                        ? max(($registration->team_quantity ?? 0) - $attachedCount, 0)
                                        : null;
                                @endphp
                                <div class="fw-semibold fs-5">{{ $attachedCount }}</div>
                                @if($registration->joining_type === 'per_team')
                                    <div class="text-muted small">Slots remaining: {{ $remainingSlots }}</div>
                                @else
                                    <div class="text-muted small">Add as many teams as needed.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($teams->isEmpty())
                        <div class="alert alert-warning d-flex align-items-start">
                            <i class="fas fa-users me-3 mt-1"></i>
                            <div>
                                <strong>You haven't created any teams yet.</strong>
                                <div class="mb-2">Use the team wizard to create your first team, then return here to add it to the tournament.</div>
                                <a href="{{ route('club.teams.wizard.step1') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Create a Team
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="border rounded-2xl p-3 mb-4">
                            <h5 class="fw-semibold mb-3">Add Your Teams</h5>
                            <form method="POST" action="{{ route('club.tournament-registrations.attach-teams', $registration) }}">
                                @csrf
                                <div class="row g-3">
                                    @foreach($teams as $team)
                                        @php $checked = in_array($team->id, $attachedTeamIds, true); @endphp
                                        <div class="col-md-6">
                                            <div class="form-check border rounded-2xl p-3 {{ $checked ? 'bg-success-subtle' : 'bg-white' }}">
                                                <input class="form-check-input" type="checkbox" value="{{ $team->id }}" id="team_{{ $team->id }}" name="team_ids[]" {{ $checked ? 'checked disabled' : '' }}>
                                                <label class="form-check-label" for="team_{{ $team->id }}">
                                                    <span class="fw-semibold">{{ $team->name }}</span>
                                                    <br>
                                                    <span class="text-muted small">{{ $team->division?->name ?? 'No division set' }}</span>
                                                    @if($checked)
                                                        <span class="badge bg-success ms-2">Already added</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-1"></i> Save Selection
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="alert alert-info small">
                        <i class="fas fa-lightbulb me-2"></i>
                        Need to adjust your registration or payment? Reach out to {{ $tournament->hostClub?->name ?? 'the tournament host' }} so they can update your registration details.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

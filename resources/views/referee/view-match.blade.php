@extends('layouts.referee-dashboard')

@section('title', 'Match Details')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="header">
                <h2>Match Details</h2>
            </div>
            <div class="body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Home Team</h4>
                        <div class="text-center">
                            <img src="{{ asset('assets/images/club-logo.png') }}" alt="{{ $match->homeClub->name }}" class="img-fluid mb-2" style="max-width: 100px;">
                            <h5>{{ $match->homeClub->name }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Away Team</h4>
                        <div class="text-center">
                            <img src="{{ asset('assets/images/club-logo.png') }}" alt="{{ $match->awayClub->name }}" class="img-fluid mb-2" style="max-width: 100px;">
                            <h5>{{ $match->awayClub->name }}</h5>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5><i class="fas fa-calendar"></i> Date & Time</h5>
                        <p>
                            <strong>Date:</strong> {{ \Carbon\Carbon::parse($match->match_date)->format('l, F d, Y') }}<br>
                            <strong>Time:</strong>
                            @try
                                {{ \Carbon\Carbon::parse($match->match_time)->format('g:i A') }}
                            @catch(\Exception $e)
                                {{ $match->match_time }}
                            @endtry
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="fas fa-map-marker-alt"></i> Venue</h5>
                        <p>{{ $match->venue ?? 'To be determined' }}</p>
                    </div>
                </div>

                @if($match->tournament)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h5><i class="fas fa-trophy"></i> Tournament</h5>
                        <p><strong>{{ $match->tournament->name }}</strong></p>
                        @if($match->tournament->description)
                            <p class="text-muted">{{ $match->tournament->description }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @if($match->required_referee_level)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h5><i class="fas fa-star"></i> Referee Requirements</h5>
                        <p><strong>Minimum Level:</strong> {{ $match->required_referee_level }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="header">
                <h3>Match Status</h3>
            </div>
            <div class="body">
                @if($match->referee_id)
                    @if($match->referee_id == auth()->user()->referee->id)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>You are assigned to this match!</strong>
                        </div>
                        <form method="POST" action="{{ route('referee.matches.cancel', $match) }}" class="mt-3">
                            @csrf
                            <button class="btn btn-warning btn-block" type="submit" onclick="return confirm('Are you sure you want to cancel this match?')">
                                <i class="fas fa-times"></i> Cancel Assignment
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Assigned to another referee</strong>
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        <strong>No referee assigned yet</strong>
                    </div>

                    @php
                        $hasApplied = \App\Models\Application::where('match_id', $match->id)
                            ->where('referee_id', auth()->user()->referee->id)
                            ->exists();
                    @endphp

                    @if(!$hasApplied)
                        <form method="POST" action="{{ route('referee.matches.apply', $match) }}" class="mt-3">
                            @csrf
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fas fa-hand-paper"></i> Apply for Match
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            You have already applied for this match
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="header">
                <h3>Quick Actions</h3>
            </div>
            <div class="body">
                <a href="{{ route('referee.matches.available') }}" class="btn btn-outline-primary btn-block mb-2">
                    <i class="fas fa-arrow-left"></i> Back to Available Matches
                </a>
                <a href="{{ route('referee.dashboard') }}" class="btn btn-outline-secondary btn-block">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

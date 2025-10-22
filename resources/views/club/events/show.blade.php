@extends('layouts.club-dashboard')
@section('title', 'Event Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Event Details</h2>
        <div>
            <a href="{{ route('club.events.edit', $event->id) }}" class="btn btn-warning theme-btn">Edit Event</a>
            <a href="{{ route('club.events.index') }}" class="btn btn-secondary">Back to Events</a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h3>{{ $event->title }}</h3>
                    <p class="text-muted">{{ $event->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Event Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>Date:</strong> {{ $event->event_date }}</li>
                                <li><strong>Time:</strong> {{ $event->event_time }}</li>
                                <li><strong>Location:</strong> {{ $event->location }}</li>
                                <li><strong>Privacy:</strong> {{ ucfirst($event->privacy) }}</li>
                                <li><strong>Created by:</strong> {{ $event->user->name ?? 'Unknown' }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Event Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Start:</strong> {{ $event->start }}</li>
                                <li><strong>End:</strong> {{ $event->end }}</li>
                                <li><strong>Created:</strong> {{ $event->created_at->format('M d, Y H:i') }}</li>
                                <li><strong>Last Updated:</strong> {{ $event->updated_at->format('M d, Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <button type="button" class="btn btn-info btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#inviteModal">
                                <i class="fas fa-envelope me-2"></i>Invite People
                            </button>
                            <form action="{{ route('club.events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash me-2"></i>Delete Event
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invite Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite People to Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('club.events.invite', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
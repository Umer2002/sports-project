@extends('layouts.admin')
@section('title', 'Events')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Events</h2>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Create New Event</a>
        </div>


        @include('partials.alerts')
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->event_date }}</td>
                        <td>{{ $event->event_time }}</td>
                        <td>{{ $event->location }}</td>
                        <td>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                                class="d-inline-block" onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                            <!-- Invite Button -->
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#inviteModal-{{ $event->id }}">
                                Invite
                            </button>
                        </td>
                    </tr>
                    <div class="modal fade" id="inviteModal-{{ $event->id }}" tabindex="-1"
                        aria-labelledby="inviteModalLabel{{ $event->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content rounded-3 shadow">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="inviteModalLabel{{ $event->id }}">Invite User to Event</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.events.invite', $event->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        @if (session('success_message'))
                                            <div class="alert alert-success">{{ session('success_message') }}</div>
                                        @endif
                                        <div class="mb-3">
                                            <label>Email Address</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>
                                        <input type="hidden" name="type" value="event">
                                        <input type="hidden" name="reference_id" value="{{ $event->id }}">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-info">Send Invitation</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
        <!-- Invite Modal -->


    </div>
@endsection

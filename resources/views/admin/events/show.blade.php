@extends('layouts.admin')
@section('title', 'Event Details')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $event->eventname }}</h4>
            <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Events
            </a>
        </div>
        <div class="card-body">
            @if ($event->coverphoto)
                <div class="mb-3 text-center">
                    <img src="{{ asset('storage/' . $event->coverphoto) }}" class="img-fluid rounded" style="max-height: 300px;" alt="Cover Photo">
                </div>
            @endif

            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th width="30%">Event Name</th>
                        <td>{{ $event->eventname }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $event->description }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ \Carbon\Carbon::parse($event->eventdate)->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Time</th>
                        <td>{{ \Carbon\Carbon::parse($event->eventtime)->format('h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td>{{ $event->eventlocation }}</td>
                    </tr>
                    <tr>
                        <th>Privacy</th>
                        <td>
                            <span class="badge bg-{{ $event->privacy === 'public' ? 'success' : 'secondary' }}">
                                {{ ucfirst($event->privacy) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At</th>
                        <td>{{ $event->created_at->format('F d, Y h:i A') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

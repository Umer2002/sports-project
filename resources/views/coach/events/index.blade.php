@extends('layouts.coach-dashboard')
@section('title', 'Events')
@section('page_title', 'Events')
<style>
    .composer-primary {
            background: linear-gradient(130deg, #38bdf8, #6366f1);
            color: #fff;
            box-shadow: 0 18px 35px -22px rgba(99, 102, 241, 0.6);
        }
</style>

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Events</h2>
            <a href="{{ route('coach.events.create') }}" class="btn btn-primary composer-primary">
                <i class="fas fa-plus me-2"></i>Create New Event
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Team</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td><strong>{{ $event->title }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
                                    <td>{{ $event->event_time }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>
                                        @if($event->team)
                                            <span class="badge bg-info">{{ $event->team->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">General</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('coach.events.edit', $event->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        @if($event->created_by === auth()->id())
                                            <form action="{{ route('coach.events.destroy', $event->id) }}" method="POST"
                                                class="d-inline-block" onsubmit="return confirm('Delete this event?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>No events found. Create your first event to get started!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $events->links() }}
        </div>
    </div>
@endsection


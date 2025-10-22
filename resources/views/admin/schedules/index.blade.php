@extends('layouts.admin')

@section('title', 'Schedules')

@section('content')
<section class="content-header">
    <h1>Schedules</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Schedules</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h4 class="card-title my-2">
                        <i class="livicon" data-name="calendar" data-size="18"></i> All Schedules
                    </h4>
                    <a href="{{ route('admin.schedules.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-plus"></i> Add Schedule
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-white">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Title</th>
                                    <th>Event Type</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Location</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td>{{ $schedule->title }}</td>
                                        <td>{{ $schedule->event_type }}</td>
                                        <td>{{ $schedule->start_time }}</td>
                                        <td>{{ $schedule->end_time }}</td>
                                        <td>{{ $schedule->location ?? '-' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-warning me-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete schedule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $schedules->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@extends('layouts.admin')

@section('title', 'Tournaments')

@section('content')
<section class="content-header">
    <h1>Tournaments</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Tournaments</li>
    </ol>
</section>

    <div class="row">
        <div class="col-12">
            <div class="card text-white">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                    <h4 class="card-title my-2">
                        <i class="livicon" data-name="trophy" data-size="18"></i> All Tournaments
                    </h4>
                    <a href="{{ route('admin.tournaments.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-plus"></i> Add Tournament
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
                                    <th>Name</th>
                                    <th>Host Club</th>
                                    <th>Format</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Location</th>
                                    <th>Schedule</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tournaments as $tournament)
                                    <tr>
                                        <td>{{ $tournament->name }}</td>
                                        <td>{{ $tournament->hostClub->name ?? '-' }}</td>
                                        <td>{{ $tournament->format->name ?? '-' }}</td>
                                        <td>{{ $tournament->start_date ? $tournament->start_date->format('M d, Y') : '-' }}</td>
                                        <td>{{ $tournament->end_date ? $tournament->end_date->format('M d, Y') : '-' }}</td>
                                        <td>{{ $tournament->location }}</td>
                                        <td>
                                            <a href="{{ route('admin.scheduler.generate', $tournament) }}" class="btn btn-sm btn-primary">
                                                Generate Schedule
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-sm btn-info me-1" title="View Tournament">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn btn-sm btn-warning me-1" title="Edit Tournament">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.tournaments.destroy', $tournament) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete tournament?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Delete Tournament"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $tournaments->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

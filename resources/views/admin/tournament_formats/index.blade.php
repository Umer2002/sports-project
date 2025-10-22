@extends('layouts.admin')

@section('title', 'Tournament Formats')

@section('content')
    <section class="content-header">
        <h1>Tournament Formats</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a>
            </li>
            <li class="active">Tournament Formats</li>
        </ol>
    </section>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title my-2">
                        <i class="livicon" data-name="trophy" data-size="18" data-c="#fff" data-hc="#fff"
                            data-loop="true"></i>
                        All Tournament Formats
                    </h4>
                    <a href="{{ route('admin.tournamentformats.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-plus"></i> Add Format
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Games/Team</th>
                                    <th>Groups</th>
                                    <th>Elimination</th>
                                    <th>Description</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formats as $format)
                                    <tr>
                                        <td>{{ $format->name }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $format->type)) }}</td>
                                        <td>{{ $format->games_per_team ?? '-' }}</td>
                                        <td>{{ $format->group_count ?? '-' }}</td>
                                        <td>{{ $format->elimination_type ?? '-' }}</td>
                                        <td>{{ Str::limit($format->description, 50) }}</td>
                                        <td class="text-end">
                                            <!-- correct -->
                                            <a href="{{ route('admin.tournamentformats.edit', $format->id) }}"
                                                class="btn btn-sm btn-warning me-1">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.tournamentformats.destroy', $format) }}"
                                                method="POST" class="d-inline-block"
                                                onsubmit="return confirm('Delete format?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'All Sports')

@section('content')
<section class="content-header">
    <h1>All Sports</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">All Sports</li>
    </ol>
</section>

    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title my-2">
                        <i class="livicon" data-name="basketball" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                        Sports List
                    </h4>
                    <a href="{{ route('admin.sports.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-plus"></i> Add Sport
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-white" id="sports_table">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Name</th>
                                    <th>Top Sport</th>
                                    <th>Icon</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sports as $sport)
                                    <tr>
                                        <td>{{ $sport->name }}</td>
                                        <td>
                                            @if($sport->is_top_sport)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sport->icon_path)
                                                <img src="{{ asset('storage/' . $sport->icon_path) }}" width="40" class="rounded">
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.sports.edit', $sport) }}" class="btn btn-sm btn-warning me-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.sports.destroy', $sport) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this sport?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- <div class="mt-3">
                        {{ $sports->links() }}
                    </div> --}}
                </div>

            </div>
        </div>
    </div>
@endsection

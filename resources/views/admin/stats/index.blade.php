@extends('layouts.admin')

@section('title', 'Stats')

@section('content')
<section class="content-header">
    <h1>All Teams (Stats)</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Stats</li>
    </ol>
</section>

    <div class="row">
        <div class="col-12">
            <div class="card bg-dark text-white">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title my-2">
                        <i class="livicon" data-name="linechart" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                        Stats List
                    </h4>
                    <a href="{{ route('admin.stats.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fa fa-plus"></i> Add Stat
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-white" id="stats_table">
                            <thead class="bg-secondary">
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats as $stat)
                                    <tr>
                                        <td>{{ $stat->name }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.stats.edit', $stat) }}" class="btn btn-sm btn-warning me-1">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.stats.destroy', $stat) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this stat?');">
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

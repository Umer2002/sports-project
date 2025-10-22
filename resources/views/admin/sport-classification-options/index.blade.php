@extends('layouts.admin')

@section('title', 'Sport Classification Options')

@section('content')
<section class="content-header">
    <h1>Classification Options</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Classification Options</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title my-2">
                    <i class="livicon" data-name="list" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                    Sport Classification Options
                </h4>
                <a href="{{ route('admin.sport_classification_options.create') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-plus"></i> Add Option
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
                                <th>Sport</th>
                                <th>Group</th>
                                <th>Code</th>
                                <th>Label</th>
                                <th>Numeric Rank</th>
                                <th>Sort</th>
                                <th>Meta</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($options as $option)
                                <tr>
                                    <td>{{ data_get($option, 'group.sport.name', '—') }}</td>
                                    <td>{{ data_get($option, 'group.name', '—') }}</td>
                                    <td>{{ $option->code }}</td>
                                    <td>{{ $option->label }}</td>
                                    <td>{{ $option->numeric_rank ?? '—' }}</td>
                                    <td>{{ $option->sort_order }}</td>
                                    <td>
                                        @if($option->meta)
                                            <code>{{ \Illuminate\Support\Str::limit(json_encode($option->meta), 80) }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.sport_classification_options.edit', $option) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sport_classification_options.destroy', $option) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this option?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No classification options configured yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

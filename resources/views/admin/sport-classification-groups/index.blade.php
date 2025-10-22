@extends('layouts.admin')

@section('title', 'Sport Classification Groups')

@section('content')
<section class="content-header">
    <h1>Classification Groups</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Classification Groups</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title my-2">
                    <i class="livicon" data-name="layers" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                    Sport Classification Groups
                </h4>
                <a href="{{ route('admin.sport_classification_groups.create') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-plus"></i> Add Group
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
                                <th>Code</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Options</th>
                                <th>Sort</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td>{{ $group->sport->name ?? '—' }}</td>
                                    <td>{{ $group->code }}</td>
                                    <td>{{ $group->name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($group->description, 80) }}</td>
                                    <td>{{ $group->options_count }}</td>
                                    <td>{{ $group->sort_order }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.sport_classification_groups.edit', $group) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sport_classification_groups.destroy', $group) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this group and all of its options?');">
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
                                    <td colspan="7" class="text-center text-muted">No classification groups configured yet.</td>
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

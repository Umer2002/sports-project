@extends('layouts.admin')

@section('title', 'Genders')

@section('content')
<section class="content-header">
    <h1>Gender Management</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Genders</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title my-2">
                    <i class="livicon" data-name="users" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                    Configured Genders
                </h4>
                <a href="{{ route('admin.genders.create') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-plus"></i> Add Gender
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
                                <th>Label</th>
                                <th>Sort</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($genders as $gender)
                                <tr>
                                    <td>{{ $gender->sport->name ?? '—' }}</td>
                                    <td>{{ $gender->code }}</td>
                                    <td>{{ $gender->label }}</td>
                                    <td>{{ $gender->sort_order }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.genders.edit', $gender) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.genders.destroy', $gender) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this gender?');">
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
                                    <td colspan="5" class="text-center text-muted">No genders configured yet.</td>
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

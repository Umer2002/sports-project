@extends('layouts.admin')

@section('title', 'Age Groups')

@section('content')
<section class="content-header">
    <h1>Age Groups</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Age Groups</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="card-title my-2">
                    <i class="livicon" data-name="users" data-size="18" data-c="#fff" data-hc="#fff" data-loop="true"></i>
                    Configured Age Groups
                </h4>
                <a href="{{ route('admin.age_groups.create') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-plus"></i> Add Age Group
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
                                <th>Age Range</th>
                                <th>Open Ended</th>
                                <th>Sort</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ageGroups as $ageGroup)
                                <tr>
                                    <td>{{ $ageGroup->sport->name ?? '—' }}</td>
                                    <td>{{ $ageGroup->code }}</td>
                                    <td>{{ $ageGroup->label }}</td>
                                    <td>
                                        @php
                                            $min = $ageGroup->min_age_years;
                                            $max = $ageGroup->max_age_years;
                                        @endphp
                                        @if(!is_null($min) && !is_null($max))
                                            {{ $min }} – {{ $max }}
                                        @elseif(!is_null($min))
                                            {{ $min }}+
                                        @elseif(!is_null($max))
                                            ≤ {{ $max }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($ageGroup->is_open_ended)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $ageGroup->sort_order }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.age_groups.edit', $ageGroup) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.age_groups.destroy', $ageGroup) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this age group?');">
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
                                    <td colspan="7" class="text-center text-muted">No age groups configured yet.</td>
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

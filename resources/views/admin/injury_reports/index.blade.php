@extends('layouts.admin')
@section('title', 'Injury Reports')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">Injury Reports</h5>
        <a href="{{ route('admin.injury_reports.create') }}" class="btn btn-primary">+ New Report</a>
    </div>
    <div class="card-body table-responsive">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Player</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Injury</th>
                    <th>Team</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $report->player->name ?? '-' }}</td>
                        <td>{{ $report->injury_datetime }}</td>
                        <td>{{ $report->location }}</td>
                        <td>{{ $report->injury_type }}</td>
                        <td>{{ $report->team_name }}</td>
                        <td>
                            <a href="{{ route('admin.injury_reports.edit', $report) }}" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $report->id }})">Delete</button>
                            <form id="delete-form-{{ $report->id }}" action="{{ route('admin.injury_reports.destroy', $report) }}" method="POST" style="display: none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Delete this report?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush

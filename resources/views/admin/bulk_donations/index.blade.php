@extends('layouts.admin')
@section('title', 'Bulk Donations')

@section('content')
<section class="content-header">
    <h1>Bulk Donations</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li class="active">Bulk Donations</li>
    </ol>
</section>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h4 class="mb-0">All Bulk Donations</h4>
        <a href="{{ route('admin.bulk_donations.create') }}" class="btn btn-light btn-sm">
            <i class="fa fa-plus"></i> Donate
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle" id="bulk_donations_table">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Donor Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donations as $donation)
                        <tr>
                            <td>{{ $donation->id }}</td>
                            <td>{{ $donation->donor_name }}</td>
                            <td>${{ number_format($donation->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('d M, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $donation->status === 'Completed' ? 'success' : 'warning' }}">
                                    {{ $donation->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.donations.edit', $donation->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/datatables/js/dataTables.js') }}"></script>
<script src="{{ asset('vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#bulk_donations_table').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            columnDefs: [{ targets: 5, orderable: false }]
        });
    });
</script>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Import Clubs CSV</h2>
        <a href="{{ route('volunteer.clubs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Upload a CSV with headers: <code>name,email</code>. Optionally preselect a sport for all rows.</p>
            <form method="POST" action="{{ route('volunteer.clubs.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">CSV File</label>
                    <input type="file" name="csv" class="form-control" accept=".csv,.txt" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="send_invites" value="1" id="sendInvites">
                    <label class="form-check-label" for="sendInvites">Send invites after import (optional)</label>
                </div>
                <button class="btn btn-primary"><i class="fas fa-file-import"></i> Import</button>
            </form>
        </div>
    </div>
</div>
@endsection

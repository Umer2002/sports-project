@extends('layouts.admin')

@section('title', 'Create Reward')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Create New Reward</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.rewards.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    <option value="badge">Badge</option>
                    <option value="certificate">Certificate</option>
                    <option value="bonus">Bonus</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Achievement</label>
                <input type="text" name="achievement" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Create Reward</button>
            </div>
        </form>
    </div>
</div>
@endsection

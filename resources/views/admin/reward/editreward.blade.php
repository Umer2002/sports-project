@extends('layouts.admin')

@section('title', 'Edit Reward')

@section('content')
<div class="card">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Edit Reward</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.rewards.update', $reward) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $reward->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="badge" {{ $reward->type === 'badge' ? 'selected' : '' }}>Badge</option>
                    <option value="certificate" {{ $reward->type === 'certificate' ? 'selected' : '' }}>Certificate</option>
                    <option value="bonus" {{ $reward->type === 'bonus' ? 'selected' : '' }}>Bonus</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Achievement</label>
                <input type="text" name="achievement" class="form-control" value="{{ old('achievement', $reward->achievement) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Image</label>
                @if($reward->image)
                    <div class="mb-2">
                        <img src="{{ asset('images/' . $reward->image) }}" alt="Reward Image" width="120">
                    </div>
                @endif
                <input type="file" name="image" class="form-control">
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Update Reward</button>
            </div>
        </form>
    </div>
</div>
@endsection

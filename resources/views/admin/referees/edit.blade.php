@extends('layouts.admin')
@section('title', isset($referee) ? 'Edit Referee' : 'Add Referee')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ isset($referee) ? 'Edit Referee' : 'Add Referee' }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($referee) ? route('admin.referees.update', $referee) : route('admin.referees.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($referee)) @method('PUT') @endif

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $referee->full_name ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $referee->email ?? '') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $referee->phone ?? '') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" name="profile_picture" class="form-control">
                @if(isset($referee) && $referee->profile_picture)
                    <img src="{{ asset('storage/' . $referee->profile_picture) }}" class="mt-2 rounded" width="80">
                @endif
            </div>

            <button type="submit" class="btn btn-success">
                {{ isset($referee) ? 'Update' : 'Create' }}
            </button>
        </form>
    </div>
</div>
@endsection

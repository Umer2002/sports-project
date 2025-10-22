@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Add New Payout Plan</h2>
    <form action="{{ route('admin.payout_plans.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="player_count" class="form-label">Player Count</label>
            <input type="number" name="player_count" id="player_count" class="form-control" value="{{ old('player_count') }}" required>
            @error('player_count')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="payout_amount" class="form-label">Payout Amount ($)</label>
            <input type="number" name="payout_amount" id="payout_amount" class="form-control" step="0.01" value="{{ old('payout_amount') }}" required>
            @error('payout_amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('admin.payout_plans.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 
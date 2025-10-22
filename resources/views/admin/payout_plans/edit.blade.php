@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Payout Plan</h2>
    <form action="{{ route('admin.payout_plans.update', $payout_plan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="player_count" class="form-label">Player Count</label>
            <input type="number" name="player_count" id="player_count" class="form-control" value="{{ old('player_count', $payout_plan->player_count) }}" required>
            @error('player_count')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="payout_amount" class="form-label">Payout Amount ($)</label>
            <input type="number" name="payout_amount" id="payout_amount" class="form-control" step="0.01" value="{{ old('payout_amount', $payout_plan->payout_amount) }}" required>
            @error('payout_amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('admin.payout_plans.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 
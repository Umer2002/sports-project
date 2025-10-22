@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Payout Plans</h2>
        <a href="{{ route('admin.payout_plans.create') }}" class="btn btn-primary">Add New Plan</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Player Count</th>
                <th>Payout Amount ($)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
                <tr>
                    <td>{{ $plan->player_count }}</td>
                    <td>{{ $plan->payout_amount }}</td>
                    <td>
                        <a href="{{ route('admin.payout_plans.edit', $plan) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.payout_plans.destroy', $plan) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

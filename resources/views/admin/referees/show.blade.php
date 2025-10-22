@extends('layouts.admin')
@section('title', 'Referee Profile')

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Referee: {{ $referee->full_name }}</h5>
    </div>
    <div class="card-body">
        <p><strong>Email:</strong> {{ $referee->email }}</p>
        <p><strong>Phone:</strong> {{ $referee->phone }}</p>
        @if($referee->profile_picture)
            <p><strong>Picture:</strong><br>
                <img src="{{ asset('storage/' . $referee->profile_picture) }}" width="100" class="rounded">
            </p>
        @endif
    </div>
</div>
@endsection

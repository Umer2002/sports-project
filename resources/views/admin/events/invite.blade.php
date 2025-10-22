@extends('layouts.admin')
@section('title', 'Invite User to Event')

@section('content')
<div class="container">
    <h2>Invite User</h2>

    @if(session('success_message'))
        <div class="alert alert-success">{{ session('success_message') }}</div>
    @endif

    <form action="{{ route('admin.events.invite', $event->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <button class="btn btn-info">Send Invitation</button>
    </form>
</div>
@endsection

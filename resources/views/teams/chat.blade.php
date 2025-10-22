@extends('layouts.default')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Team Chat - {{ $team->name }}</h2>
    <div class="card mb-3">
        <div class="card-body" style="max-height:400px; overflow-y:auto;">
            @forelse($messages as $msg)
                <div class="mb-2">
                    <strong>{{ $msg->user->first_name ?? 'Unknown' }}:</strong>
                    <span>{{ $msg->content }}</span>
                </div>
            @empty
                <p class="text-muted">No messages yet.</p>
            @endforelse
        </div>
    </div>
    <form method="POST" action="{{ route('player.teams.chat.send', $team) }}" class="d-flex">
        @csrf
        <input type="text" name="message" class="form-control me-2" placeholder="Type a message">
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</div>
@endsection


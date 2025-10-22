@extends('layouts.default')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Team Chat (React) - {{ $team->name }}</h2>
    <div id="team-chat-root" data-team-id="{{ $team->id }}"></div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/react/team-chat.jsx'])
@endpush


@extends('layouts.club-dashboard')

@section('title', 'Tournament Invites')

@section('content')
    <h1 class="mb-3">Tournament Invites</h1>
    <p class="text-muted">List of all tournaments for accepted invites</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Tournament Name</th>
                <th>Type</th>
                <th>Start Date</th>
                <th>Location</th>
                <th>Invite Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invites as $invite)
                @php
                    $t = $invite->tournament;
                    $registration = $t?->registrations?->first(); // latest for this club
                @endphp
                <tr>
                    <td>{{ $t->name }}</td>
                    <td>{{ \Illuminate\Support\Str::of($t->start_date)->substr(0, 10) }} —
                        {{ \Illuminate\Support\Str::of($t->end_date)->substr(0, 10) }}</td>
                    <td>{{ $t->location ?? ($t->venue->name ?? '—') }}</td>
                    <td>{{ $invite->status ?? '—' }}</td>
                    <td>
                        @if ($registration)
                            {{-- If your route param is {tournamentRegistration} (implicit binding): --}}
                            <a href="{{ route('club.tournament-registrations.setup', $registration) }}">Setup</a>

                            {{-- If your route param is {tournament}: use this instead:
                <a href="{{ route('club.tournament-registrations.setup', ['tournament' => $registration->id]) }}">Setup</a>
                --}}
                    </td>
                    <td>
                        {{-- add team link --}}
                        <a href="{{ route('club.teams.wizard.step1') }}" class="nav-link">
                                <span class="nav-text">Create Team</span>
                            </a>
                    </td>
                        @else
                            <span class="text-muted">No registration yet</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No registered invites found.</td>
                </tr>
            @endforelse

        </tbody>
    </table>

    {{-- If you paginate in the controller: --}}
    @if (method_exists($invites, 'links'))
        <div class="mt-3">
            {{ $invites->links() }}
        </div>
    @endif
@endsection

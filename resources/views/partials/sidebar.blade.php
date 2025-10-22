<div>
    @php($user = auth()->user())

    @if($user && $user->hasRole('admin'))
        @include('admin.partials.sidebar')
    @elseif($user && $user->hasRole('club'))
        @include('club.partials.sidebar')
    @elseif($user && $user->hasRole('player'))
        @include('players.partials.sidebar')
    @elseif($user && $user->hasRole('referee'))
        @include('referee.partials.sidebar')
    @elseif($user && $user->hasRole('college'))
        @include('college.partials.sidebar')
    @elseif($user && ($user->hasRole('volunteer') || $user->hasRole('ambassador')))
        @include('volunteer.partials.sidebar')
    @else
        @include('admin.partials.sidebar')
    @endif
</div>

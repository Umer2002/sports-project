@extends('layouts.player-new')

@section('title', $pickup_game->title ?? 'Game Details')



@section('content')
<div class="container">
    <!-- Hidden input for copy link functionality -->
    <input type="hidden" id="gameLink" value="{{ route('player.pickup-games.show', ['pickup_game' => $pickup_game]) }}">

    <script>
        function copyGameLink() {
            const gameLink = document.getElementById('gameLink').value;
            navigator.clipboard.writeText(gameLink).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                button.classList.remove('btn-info');
                button.classList.add('btn-success');

                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-info');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy link. Please copy manually: ' + gameLink);
            });
        }
    </script>


    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ $pickup_game->title }}</h4>
                        <div>
                            @if($pickup_game->isHostedBy(auth()->id()))
                                <a href="{{ route('player.pickup-games.edit', ['pickup_game' => $pickup_game]) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            @endif
                            <a href="{{ route('player.pickup-games.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Game Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Sport:</strong></td>
                                    <td>{{ $pickup_game->sport ? $pickup_game->sport->name : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>{{ $pickup_game->game_datetime ? $pickup_game->game_datetime->format('M d, Y \a\t H:i') : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $pickup_game->location }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Skill Level:</strong></td>
                                    <td>{{ ucfirst($pickup_game->skill_level) }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Players:</strong></td>
                                    <td>{{ $pickup_game->participants->count() }}/{{ $pickup_game->max_players }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Host:</strong></td>
                                    <td>{{ $pickup_game->host ? $pickup_game->host->name : 'Unknown' }}</td>
                                </tr>
                                @if($pickup_game->equipment_needed)
                                    <tr>
                                        <td><strong>Equipment:</strong></td>
                                        <td>{{ $pickup_game->equipment_needed }}</td>
                                    </tr>
                                @endif

                                @if($pickup_game->description)
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td>{{ $pickup_game->description }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    @if($pickup_game->isHostedBy(auth()->id()))
                                        <div class="mb-3">
                                            <h6 class="card-title mb-2">Share Game</h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                <!-- WhatsApp -->
                                                <a href="https://wa.me/?text={{ urlencode('Join my fun game: ' . $pickup_game->title . ' at ' . $pickup_game->location . ' on ' . ($pickup_game->game_datetime ? $pickup_game->game_datetime->format('M d, Y H:i') : 'TBD') . ' - ' . route('player.pickup-games.show', ['pickup_game' => $pickup_game])) }}"
                                                   target="_blank" class="btn btn-success btn-sm">
                                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                                </a>

                                                <!-- Facebook -->
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('player.pickup-games.show', ['pickup_game' => $pickup_game])) }}&quote={{ urlencode('Join my fun game: ' . $pickup_game->title) }}"
                                                   target="_blank" class="btn btn-primary btn-sm">
                                                    <i class="fab fa-facebook me-1"></i>Facebook
                                                </a>

                                                <!-- Twitter/X -->
                                                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Join my fun game: ' . $pickup_game->title . ' at ' . $pickup_game->location . ' on ' . ($pickup_game->game_datetime ? $pickup_game->game_datetime->format('M d, Y H:i') : 'TBD')) }}&url={{ urlencode(route('player.pickup-games.show', ['pickup_game' => $pickup_game])) }}"
                                                   target="_blank" class="btn btn-dark btn-sm">
                                                    <i class="fab fa-twitter me-1"></i>Twitter
                                                </a>

                                                <!-- Email -->
                                                <a href="mailto:?subject={{ urlencode('Join my fun game: ' . $pickup_game->title) }}&body={{ urlencode('Hey! I\'m hosting a fun game and would love for you to join!%0D%0A%0D%0AGame: ' . $pickup_game->title . '%0D%0ALocation: ' . $pickup_game->location . '%0D%0ADate: ' . ($pickup_game->game_datetime ? $pickup_game->game_datetime->format('M d, Y H:i') : 'TBD') . '%0D%0ASport: ' . ($pickup_game->sport ? $pickup_game->sport->name : 'Not specified') . '%0D%0A%0D%0AJoin here: ' . route('player.pickup-games.show', ['pickup_game' => $pickup_game])) }}"
                                                   class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-envelope me-1"></i>Email
                                                </a>

                                                <!-- Copy Link -->
                                                <button type="button" class="btn btn-info btn-sm" onclick="copyGameLink()">
                                                    <i class="fas fa-copy me-1"></i>Copy Link
                                                </button>
                                            </div>
                                        </div>
                                        <form action="{{ route('player.pickup-games.destroy', ['pickup_game' => $pickup_game]) }}" method="POST" class="mb-2" onsubmit="return confirm('Are you sure you want to cancel this game?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-times me-1"></i>Cancel Game
                                            </button>
                                        </form>
                                    @else
                                        @if($pickup_game->canJoin(auth()->id()))
                                            <form action="{{ route('player.pickup-games.join', ['pickup_game' => $pickup_game]) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fas fa-plus me-1"></i>Join Game
                                                </button>
                                            </form>
                                        @elseif($pickup_game->participants->contains(auth()->id()))
                                            <form action="{{ route('player.pickup-games.leave', ['pickup_game' => $pickup_game]) }}" method="POST" class="mb-2">
                                                @csrf
                                                <button type="submit" class="btn btn-warning w-100">
                                                    <i class="fas fa-minus me-1"></i>Leave Game
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="fas fa-ban me-1"></i>Game Full
                                            </button>
                                        @endif
                                    @endif

                                    <!-- Share options for all users -->

                                </div>
                            </div>

                            @if($pickup_game->participants->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Participants ({{ $pickup_game->participants->count() }})</h6>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-unstyled mb-0">
                                            @foreach($pickup_game->participants as $participant)
                                                <li class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user me-2 text-muted"></i>
                                                    <span>{{ $participant ? $participant->name : 'Unknown User' }}</span>
                                                    @if($participant && $participant->id === $pickup_game->host_id)
                                                        <span class="badge bg-primary ms-2">Host</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.player-new')

@section('title', 'Create Pickup Game')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Create Pickup Game</h4>
                        <a href="{{ route('player.pickup-games.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Games
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('player.pickup-games.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Game Title</label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g., Basketball at Central Park" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sport_id" class="form-label">Sport</label>
                            <select name="sport_id" id="sport_id" class="form-control @error('sport_id') is-invalid @enderror" required>
                                <option value="">Select a sport</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ old('sport_id') == $sport->id ? 'selected' : '' }}>
                                        {{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sport_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Tell people about your game...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="game_datetime" class="form-label">Date & Time</label>
                                    <input type="datetime-local" name="game_datetime" id="game_datetime" class="form-control @error('game_datetime') is-invalid @enderror" value="{{ old('game_datetime') }}" required>
                                    @error('game_datetime')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_players" class="form-label">Max Players</label>
                                    <input type="number" name="max_players" id="max_players" class="form-control @error('max_players') is-invalid @enderror" value="{{ old('max_players', 10) }}" min="2" max="50" required>
                                    @error('max_players')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="e.g., Central Park Basketball Court" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude (Optional)</label>
                                    <input type="number" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" step="any" placeholder="40.7589">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude (Optional)</label>
                                    <input type="number" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" step="any" placeholder="-73.9851">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="skill_level" class="form-label">Skill Level</label>
                                    <select name="skill_level" id="skill_level" class="form-control @error('skill_level') is-invalid @enderror" required>
                                        <option value="all_levels" {{ old('skill_level') == 'all_levels' ? 'selected' : '' }}>All Levels Welcome</option>
                                        <option value="beginner" {{ old('skill_level') == 'beginner' ? 'selected' : '' }}>Beginner Friendly</option>
                                        <option value="intermediate" {{ old('skill_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('skill_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @error('skill_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="privacy" class="form-label">Privacy</label>
                                    <select name="privacy" id="privacy" class="form-control @error('privacy') is-invalid @enderror" required>
                                        <option value="public" {{ old('privacy') == 'public' ? 'selected' : '' }}>Public - Anyone can join</option>
                                        <option value="private" {{ old('privacy') == 'private' ? 'selected' : '' }}>Private - Invite only</option>
                                    </select>
                                    @error('privacy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="equipment_needed" class="form-label">Equipment Needed (Optional)</label>
                            <textarea name="equipment_needed" id="equipment_needed" class="form-control @error('equipment_needed') is-invalid @enderror" rows="2" placeholder="e.g., Bring your own basketball, water bottle">{{ old('equipment_needed') }}</textarea>
                            @error('equipment_needed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="join_fee" class="form-label">Join Fee (Optional)</label>
                            <input type="number" name="join_fee" id="join_fee" class="form-control @error('join_fee') is-invalid @enderror" value="{{ old('join_fee') }}" step="0.01" min="0" placeholder="0.00">
                            @error('join_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Pickup Game Tips:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Be specific about the location and meeting point</li>
                                <li>Include any equipment players should bring</li>
                                <li>Set realistic player limits</li>
                                <li>Share the game link with friends to get more players</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-gamepad me-2"></i>Create Pickup Game
                            </button>
                            <a href="{{ route('player.pickup-games.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

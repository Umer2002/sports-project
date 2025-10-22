@extends('layouts.club-dashboard')
@section('title', 'Select Players - Step 3')
@section('page_title', 'Select Players - Step 3')

@section('content')
    <div class="row clearfix">
        @include('club.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title">Step 3 of 4: Select Players for <strong>{{ $team->name }}</strong></h4>
                    <p class="card-subtitle">Only players that match your age group and gender settings are listed.</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @php($preselected = collect(old('player_ids', $selectedPlayerIds))->map(fn($id) => (int) $id)->all())

                    <div class="mb-4 p-3 bg-light rounded border">
                        <h5 class="mb-1">Eligibility Applied</h5>
                        <p class="mb-1 text-muted">
                            Age Group: <strong>{{ $team->ageGroup->label ?? 'Open' }}</strong>
                            <span class="ms-3">Gender: <strong>{{ $team->genderCategory->label ?? 'Open / Co-ed' }}</strong></span>
                        </p>
                        <small class="text-muted">Need to make changes? Return to Step 2 and update the filters.</small>
                    </div>

                    @if ($availablePlayers->isEmpty())
                        <div class="alert alert-warning">
                            No club players match the selected eligibility filters. Adjust them in Step 2 to widen the pool.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('club.teams.wizard.storePlayers', $team) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="player_ids" class="form-label">Choose Club Players</label>
                            <select
                                name="player_ids[]"
                                id="player_ids"
                                class="form-select select2"
                                data-placeholder="Search and select club players"
                                multiple
                            >
                                @foreach ($availablePlayers as $player)
                                    <option value="{{ $player->id }}" {{ in_array($player->id, $preselected, true) ? 'selected' : '' }}>
                                        {{ $player->name }}
                                        @if ($player->email)
                                            <small class="text-muted">({{ $player->email }})</small>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Only players registered to your club who meet the age group and gender filters appear here.</small>
                            @error('player_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('player_ids.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($team->players->isNotEmpty())
                            <hr>
                            <h5>Current Selection</h5>
                            <ul class="list-group mb-3">
                                @foreach ($team->players as $player)
                                    <li class="list-group-item">
                                        {{ $player->name }}
                                        @if ($player->email)
                                            <small class="text-muted">({{ $player->email }})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('club.teams.wizard.step2', $team) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Step 2
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-users"></i> Save Players & Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        if (window.$) {
            window.$('#player_ids').select2({
                width: '100%',
                placeholder: $('#player_ids').data('placeholder'),
                allowClear: true
            });
        }
    </script>
@endsection

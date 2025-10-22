@extends('layouts.admin')
@section('title', 'Select Players')

@section('content')
    <div class="row clearfix">
        @include('admin.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="header bg-primary text-white">
                    <h2>Step 3: Select Players for <strong>{{ $team->name }}</strong></h2>
                </div>
                <div class="body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-2xl">
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
                        <small class="text-muted">Only players matching these criteria are listed below. Adjust them in Step 2 if needed.</small>
                    </div>

                    @if ($availablePlayers->isEmpty())
                        <div class="alert alert-warning">
                            No players match the current eligibility criteria. Update the age group or gender filters in Step 2 to widen the search.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.teams.wizard.storePlayers', $team) }}">
                        @csrf

                        <div class="form-group">
                            <label for="player_ids" class="form-label">Choose Players</label>
                            <select
                                name="player_ids[]"
                                id="player_ids"
                                class="form-control select2"
                                data-placeholder="Search and select players"
                                multiple
                            >
                                @foreach ($availablePlayers as $player)
                                    <option value="{{ $player->id }}" {{ in_array($player->id, $preselected, true) ? 'selected' : '' }}>
                                        {{ $player->name }} @if($player->email) <small class="text-muted">({{ $player->email }})</small> @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Listing players registered for {{ $team->sport->name ?? 'this sport' }} who meet the selected age group and gender filters.
                                Select multiple players to build your roster.
                            </small>
                            @error('player_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @error('player_ids.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($team->players->isNotEmpty())
                            <hr>
                            <h5>Currently Selected Players</h5>
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
                            <a href="{{ route('admin.teams.wizard.step2', $team) }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Back to Eligibility
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-users"></i> Save Players & Continue
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
            $('#player_ids').select2({
                width: '100%',
                placeholder: $('#player_ids').data('placeholder'),
                allowClear: true
            });
        }
    </script>
@endsection

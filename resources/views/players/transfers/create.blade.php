@extends('layouts.player-new')

@section('title', 'Request Transfer')
@section('page-title', 'Request Transfer')

<link rel="stylesheet" href="{{ asset('assets/player-dashboard/css/transfer-form.css') }}">


@section('content')
<div class="transfer-shell">
    <form action="{{ route('player.transfers.store') }}" method="POST" novalidate>
        @csrf

        <div class="transfer-card" data-player-theme>
            <div class="transfer-card__header">
                <div class="transfer-card__icon">
                    <span class="fa-solid fa-arrow-right-arrow-left"></span>
                </div>
                <h2 class="transfer-card__title">Request Transfer</h2>
            </div>

            <div class="transfer-card__body">
                <div class="transfer-field">
                    <label for="to_sport_id">Select Sport</label>
                    <div class="transfer-select">
                        <span class="transfer-select__icon fa-solid fa-futbol"></span>
                        <select name="to_sport_id" id="to_sport_id"
                            class="@error('to_sport_id') is-invalid @enderror" required>
                            <option value="">Choose sport</option>
                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}" {{ old('to_sport_id') == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('to_sport_id')
                        <div class="transfer-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="transfer-field">
                    <label for="to_club_id">Select Club to Transfer To</label>
                    <div class="transfer-select">
                        <span class="transfer-select__icon fa-solid fa-shield"></span>
                        <select name="to_club_id" id="to_club_id"
                            class="@error('to_club_id') is-invalid @enderror" required>
                            <option value="">Choose club</option>
                            @foreach ($clubs as $club)
                                <option value="{{ $club->id }}" data-sport-id="{{ $club->sport_id ?? '' }}"
                                    {{ old('to_club_id') == $club->id ? 'selected' : '' }}>
                                    {{ $club->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('to_club_id')
                        <div class="transfer-error">{{ $message }}</div>
                    @enderror
                </div>

                @if ($player)
                    <div class="transfer-player-card">
                        <div>
                            <p class="transfer-player-card__name">{{ $player->name }}</p>
                            <p class="transfer-player-card__meta">
                                {{ $player->position->position_name ?? 'Player' }}
                                @if ($player->club)
                                    <span class="transfer-player-card__divider">|</span>
                                    {{ $player->club->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="transfer-submit-wrap">
            <button type="submit" class="transfer-submit">
                Submit Transfer
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clubSelect = document.getElementById('to_club_id');
        const sportSelect = document.getElementById('to_sport_id');

        function autoSelectSport() {
            const option = clubSelect.options[clubSelect.selectedIndex];
            const sportId = option ? (option.dataset ? option.dataset.sportId : option.getAttribute('data-sport-id')) : null;

            if (sportId) {
                for (const sportOption of sportSelect.options) {
                    if (String(sportOption.value) === String(sportId)) {
                        sportSelect.value = sportOption.value;
                        return;
                    }
                }
            }

            const clubId = clubSelect.value;
            if (clubId) {
                fetch(`/clubs/${clubId}/sport`, { headers: { 'Accept': 'application/json' } })
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (!data || !data.sport_id) return;
                        for (const sportOption of sportSelect.options) {
                            if (String(sportOption.value) === String(data.sport_id)) {
                                sportSelect.value = sportOption.value;
                                return;
                            }
                        }
                    })
                    .catch(() => {});
            }
        }

        if (clubSelect && sportSelect) {
            clubSelect.addEventListener('change', autoSelectSport);
            autoSelectSport();
        }
    });
</script>
@endpush

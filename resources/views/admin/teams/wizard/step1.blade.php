@extends('layouts.admin')
@section('title', isset($team) ? 'Edit Team' : 'Add Team')

@section('header_styles')
    <!-- Include if you have custom styles for select2/summernote -->
@endsection

@section('content')
    <div class="row clearfix">
        @include('admin.teams.wizard._progress')

        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>{{ isset($team) ? 'Edit Team' : 'Add Team' }}</h2>
                </div>

                <div class="body">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-2xl">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <?php
                        $actingAsClub = auth()->check() && auth()->user()->hasRole('club');
                        $currentUser = auth()->user();
                        $currentClub = null;
                        $clubSports = $clubSports ?? [];
                        if ($actingAsClub && $currentUser) {
                            // Resolve club either via users.club_id or clubs.user_id
                            $currentClub = $currentUser->club ?: \App\Models\Club::where('user_id', $currentUser->id)->first();
                        }
                    ?>
                    <form action="{{ $actingAsClub ? route('club.teams.wizard.storeStep1') : route('admin.teams.wizard.storeStep1') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($team))
                            @method('PUT')
                        @endif

                        {{-- Team Name --}}
                        <h2 class="card-inside-title">Team Name</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $team->name ?? '') }}" placeholder="Enter team name" required>
                            </div>
                        </div>

                        {{-- Description --}}
                        <h2 class="card-inside-title">Description</h2>
                        <div class="form-group">
                            <textarea name="description" class="form-control summernote" rows="5">{{ old('description', $team->description ?? '') }}</textarea>
                        </div>

                        {{-- Club --}}
                        @if(!$actingAsClub)
                            <h2 class="card-inside-title">Club</h2>
                            <div class="form-group">
                                <select name="club_id" id="club_id" class="form-control select2" data-placeholder="Select Club" required>
                                    <option value="">Select Club</option>
                                    @foreach ($clubs as $id => $club)
                                        @php $sportForClub = $clubSports[$id] ?? ''; @endphp
                                        <option value="{{ $id }}" data-sport-id="{{ $sportForClub }}"
                                            {{ old('club_id', $team->club_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $club }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Sport --}}
                            <h2 class="card-inside-title">Sport</h2>
                            <div class="form-group">
                                <select id="sport_select" class="form-control" disabled>
                                    <option value="">-- Sport will be set from club --</option>
                                    @foreach ($sports as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="sport_id" id="sport_id" value="{{ old('sport_id') }}">
                                <small class="text-muted">Sport is set based on the selected club and cannot be changed.</small>
                            </div>
                        @else
                            <input type="hidden" name="club_id" value="{{ $currentClub->id ?? '' }}">
                            <input type="hidden" name="sport_id" value="{{ $currentClub->sport_id ?? '' }}">
                            <div class="form-group">
                                <div class="alert alert-info mb-0">Club: {{ $currentClub->name ?? 'N/A' }} | Sport: {{ optional($currentClub->sport)->name ?? 'N/A' }}</div>
                            </div>
                        @endif

                        {{-- Logo --}}
                        <h2 class="card-inside-title">Team Logo</h2>
                        <div class="form-group">
                            <input type="file" name="logo" class="form-control">
                            @if (isset($team) && $team->logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $team->logo) }}" width="60"
                                        class="rounded shadow-sm" alt="Team Logo">
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="form-group d-flex justify-content-between mt-4">
                            <a href="{{ $actingAsClub ? route('club.teams.index') : route('admin.teams.index') }}" class="btn btn-secondary btn-label">
                                <i class="ti ti-trash label-icon"></i> Discard
                            </a>
                            <button type="submit" class="btn btn-success btn-label">
                                <i class="ti ti-send label-icon"></i> {{ isset($team) ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <!-- Bootstrap + Summernote + Select2 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.$) {
                window.$('.summernote').summernote({
                    height: 200
                });

                window.$('.select2').each(function () {
                    const placeholder = this.dataset.placeholder || 'Select an option';
                    window.$(this).select2({
                        width: '100%',
                        placeholder,
                        allowClear: true
                    });
                });
            }

            const clubSelect = document.getElementById('club_id');
            const sportSelect = document.getElementById('sport_select');
            const sportIdInput = document.getElementById('sport_id');
            const rawClubSports = @json($clubSports) || {};
            const clubSports = Object.keys(rawClubSports).reduce((carry, key) => {
                carry[String(key)] = rawClubSports[key];
                return carry;
            }, {});

            function updateSportSelection(sportId) {
                const value = (sportId === null || sportId === undefined || sportId === '')
                    ? ''
                    : String(sportId);
                if (sportSelect) {
                    sportSelect.value = value;
                    if (window.$ && window.$(sportSelect).data('select2')) {
                        window.$(sportSelect).val(value).trigger('change.select2');
                    }
                }
                if (sportIdInput) {
                    sportIdInput.value = value;
                }
            }

            function setSportFromClub(clubId) {
                if (!clubId) {
                    updateSportSelection('');
                    return;
                }

                const key = String(clubId);
                const mappedSport = Object.prototype.hasOwnProperty.call(clubSports, key)
                    ? clubSports[key]
                    : null;

                updateSportSelection(mappedSport ?? '');
            }

            function syncSportWithClub(clubId) {
                setSportFromClub(clubId);
            }

            if (clubSelect) {
                const handleChange = () => {
                    syncSportWithClub(clubSelect.value);
                };

                clubSelect.addEventListener('change', handleChange);

                if (window.$) {
                    const $clubSelect = window.$(clubSelect);
                    $clubSelect.on('select2:select select2:clear', handleChange);
                }

                if (clubSelect.value) {
                    syncSportWithClub(clubSelect.value);
                }
            }

            @if($actingAsClub && $currentClub)
                syncSportWithClub({{ $currentClub->id }});
            @endif
        });
    </script>
@endsection

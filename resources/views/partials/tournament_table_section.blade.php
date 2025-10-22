<div class="left-column">

<style>
    .action-btn {
        border: none !important;
        color: white !important;
    }

    .action-btn i {
        color: white !important;
    }

    .action-btn:hover {
        color: white !important;
    }

    .action-btn:hover i {
        color: white !important;
    }
    #th-text {
        text-align: center;
        color: #fff !important;
    }
</style>

<!-- Tournament Search Section -->
<div class="tournament-chart-container rounded-4" data-tournament-search>
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="main-header" style="color: #fff;">Tournament Search</h1>
        <div class="d-flex align-items-center gap-2">
            <button class="chat-src-btn" type="button" data-filter-apply>Search</button>
            <button class="chat-clr-btn" type="button" data-filter-reset>Clear filters</button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-section">
        <!-- First Row -->
        <div class="row filter-row">
            <div class="col-md-3">
                <label class="filter-label">Province</label>
                <select class="form-select" data-filter="state" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Provinces</option>
                    @foreach($tournamentFilterOptions['states'] as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">City</label>
                <select class="form-select" data-filter="city" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Cities</option>
                    @foreach($tournamentFilterOptions['cities'] as $city)
                        <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Sport</label>
                <select class="form-select" data-filter="sport" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Sports</option>
                    @foreach($tournamentFilterOptions['sports'] as $sport)
                        <option value="{{ $sport }}">{{ $sport }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Month</label>
                <select class="form-select" data-filter="month" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Dates</option>
                    @foreach($tournamentFilterOptions['months'] as $month)
                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row g-3">
            <div class="col">
                <label class="filter-label">Division</label>
                <select class="form-select" data-filter="division" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Divisions</option>
                    @foreach($tournamentFilterOptions['divisions'] as $division)
                        <option value="{{ $division }}">{{ $division }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col">
                <label class="filter-label">Status</label>
                <select class="form-select" data-filter="status" style="color: #fff !important;background-color: #232327 !important;">
                    <option value="">All Statuses</option>
                    @foreach($tournamentFilterOptions['statuses'] as $status)
                        <option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="results-header" data-result-count>Results • {{ $tournamentDirectoryEntries->count() ?? 0 }} Tournaments</div>

    <!-- Tournament Table -->
    <div class="tournament-table">
        <div class="table-responsive">
            <table class="table align-middle custom-table">
                <thead>
                    <tr>
                        <th id="th-text">Tournament</th>
                        <th id="th-text">City / Province</th>
                        <th id="th-text">Dates</th>
                        <th id="th-text">Divisions</th>
                        <th id="th-text">Teams</th>
                        <th id="th-text">Fee</th>
                        <th id="th-text">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tournamentDirectoryEntries ?? [] as $tournament)
                        @php
                            $stateSlug = \Illuminate\Support\Str::slug($tournament['state'] ?? '', '_');
                            $citySlug = \Illuminate\Support\Str::slug($tournament['city'] ?? '', '_');
                            $sportSlug = \Illuminate\Support\Str::slug($tournament['sport'] ?? '', '_');
                            $divisionSlug = \Illuminate\Support\Str::slug($tournament['division'] ?? '', '_');
                            $monthKey = $tournament['month_key'] ?? '';
                            $statusType = $tournament['status_type'] ?? '';
                            $feeValue = is_null($tournament['fee']) ? '' : (float) $tournament['fee'];
                        @endphp
                        <tr data-tournament-row
                            data-state="{{ $stateSlug }}"
                            data-state-label="{{ $tournament['state'] }}"
                            data-city="{{ $citySlug }}"
                            data-city-label="{{ $tournament['city'] }}"
                            data-sport="{{ $sportSlug }}"
                            data-division="{{ $divisionSlug }}"
                            data-status="{{ $statusType }}"
                            data-month="{{ $monthKey }}"
                            data-fee="{{ $feeValue }}">
                            <td>{{ $tournament['name'] }}</td>
                            <td>{{ $tournament['city'] }}, {{ $tournament['state'] }}</td>
                            <td>{{ $tournament['dates'] }}</td>
                            <td>{{ $tournament['division'] }}</td>
                            <td>{{ $tournament['teams'] }}</td>
                            <td>
                                @if (!is_null($tournament['fee']))
                                    ${{ number_format((float) $tournament['fee'], 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <span class="status-badge {{ $tournament['status_class'] }}">
                                    {{ $tournament['status_label'] }}
                                </span>
                                @if (empty($tournament['action_disabled']))
                                    <a href="{{ route('player.tournaments.directory') }}"
                                        class="status-badge status-register" data-player-tournament-link
                                        data-url="{{ route('player.tournaments.directory') }}"
                                        data-tournament-id="{{ $tournament['id'] }}">
                                        {{ $tournament['action_label'] }}
                                    </a>
                                @else
                                    <span class="status-badge status-register disabled" aria-disabled="true">
                                        {{ $tournament['action_label'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No tournaments available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tip Section -->
    <div class="tip-section">
        <p class="tip-text">
            Tip: Use filters to narrow by province, city, date range, division, age, surface, status, and fee.
        </p>
    </div>
    </div>
</div>

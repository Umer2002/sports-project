@extends('layouts.default')

@section('title', 'Tournament Finder')

@section('styles')
    <style>
        .tournament-hero {
            background: linear-gradient(135deg, rgba(5, 10, 30, 0.88), rgba(0, 45, 75, 0.78));
            border-radius: 32px;
            margin: 48px auto;
            position: relative;
            overflow: hidden;
        }

        .floating-square {
            position: absolute;
            border: 1px solid rgba(0, 174, 239, 0.35);
            background: rgba(255, 247, 0, 0.08);
            border-radius: 24px;
            z-index: 1;
        }

        .floating-square::after {
            content: '';
            position: absolute;
            inset: 10%;
            border: 1px dashed rgba(0, 174, 239, 0.25);
            border-radius: inherit;
        }

        .square-1 {
            width: 180px;
            height: 180px;
            top: -60px;
            right: -40px;
        }

        .square-2 {
            width: 140px;
            height: 140px;
            bottom: -50px;
            left: 5%;
        }

        .square-3 {
            width: 220px;
            height: 220px;
            top: 35%;
            left: -80px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-kicker {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 999px;
            background: rgba(255, 247, 0, 0.12);
            color: #fff700;
            letter-spacing: 0.12em;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .hero-heading {
            color: #fff;
            line-height: 1.1;
        }

        .hero-copy {
            color: rgba(255, 255, 255, 0.65);
            max-width: 520px;
        }

        .hero-highlights {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-top: 28px;
        }

        .hero-highlight {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 18px 20px;
            color: rgba(255, 255, 255, 0.75);
            min-width: 200px;
        }

        .tournament-search-card {
            background: rgba(10, 10, 25, 0.92);
            border-radius: 26px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 18, 40, 0.55);
        }

        .search-label {
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.14em;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 6px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon-badge {
            position: absolute;
            top: 50%;
            left: 16px;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(0, 174, 239, 0.18);
            color: #00aeef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            padding: 12px 18px 12px 74px;
            border-radius: 18px;
            min-height: 54px;
        }

        .search-input:focus {
            background: rgba(0, 174, 239, 0.12);
            border-color: rgba(0, 174, 239, 0.85);
            box-shadow: 0 0 0 0.2rem rgba(0, 174, 239, 0.2);
            color: #fff;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .hero-submit {
            background: linear-gradient(145deg, #00aeef, #005a8f);
            border: none;
            border-radius: 16px;
            padding: 14px 20px;
            color: #fff;
            font-weight: 600;
        }

        .tournament-layout {
            margin-bottom: 80px;
        }

        .tournament-card {
            width: 100%;
            text-align: left;
            background: rgba(10, 12, 32, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 22px;
            padding: 24px;
            margin-bottom: 18px;
            color: #fff;
            transition: transform 0.25s ease, border-color 0.25s ease, background 0.25s ease;
        }

        .tournament-list {
            display: flex;
            flex-direction: column;
        }

        .tournament-card:hover,
        .tournament-card.active {
            border-color: rgba(0, 174, 239, 0.75);
            background: rgba(0, 174, 239, 0.15);
            transform: translateY(-3px);
            box-shadow: 0 18px 45px rgba(0, 30, 55, 0.45);
        }

        .card-topline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .status-upcoming {
            background: rgba(255, 247, 0, 0.15);
            color: #fff700;
        }

        .status-in_progress {
            background: rgba(0, 174, 239, 0.18);
            color: #00aeef;
        }

        .status-completed {
            background: rgba(0, 169, 113, 0.18);
            color: #00d987;
        }

        .status-scheduled {
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.75);
        }

        .card-date {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.55);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .card-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .card-tag {
            background: rgba(0, 174, 239, 0.12);
            color: #00aeef;
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.75rem;
            letter-spacing: 0.06em;
        }

        .tournament-detail {
            background: rgba(10, 12, 32, 0.92);
            border-radius: 28px;
            padding: 36px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
            min-height: 520px;
            box-shadow: 0 28px 60px rgba(0, 18, 40, 0.6);
        }

        .detail-empty {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            padding: 80px 20px;
        }

        .detail-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .detail-title {
            font-weight: 700;
            margin-top: 16px;
        }

        .detail-summary {
            color: rgba(255, 255, 255, 0.65);
            max-width: 620px;
        }

        .detail-metrics {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .metric-card {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 14px 18px;
            min-width: 110px;
        }

        .metric-label {
            font-size: 0.7rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
        }

        .metric-value {
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
        }

        .detail-meta {
            display: grid;
            gap: 12px;
            margin: 28px 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .meta-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: rgba(0, 174, 239, 0.18);
            color: #00aeef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .upcoming-card {
            background: rgba(0, 174, 239, 0.12);
            border: 1px solid rgba(0, 174, 239, 0.25);
            border-radius: 22px;
            padding: 20px 24px;
            margin-bottom: 30px;
        }

        .upcoming-label {
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.75rem;
            color: #00aeef;
            margin-bottom: 12px;
        }

        .upcoming-teams {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }

        .upcoming-meta {
            margin-top: 12px;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.85rem;
        }

        .detail-section {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 22px;
            padding: 22px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 24px;
        }

        .detail-section h5 {
            font-size: 0.95rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 18px;
        }

        .fixture-card {
            background: rgba(0, 174, 239, 0.08);
            border-radius: 18px;
            padding: 18px;
            margin-bottom: 16px;
        }

        .fixture-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
        }

        .fixture-teams {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .fixture-team {
            flex: 1;
            font-weight: 600;
        }

        .fixture-score {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }

        .fixture-venue {
            margin-top: 12px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.55);
        }

        .fixture-card.status-completed {
            background: rgba(0, 169, 113, 0.12);
        }

        .fixture-card.status-upcoming {
            background: rgba(255, 247, 0, 0.12);
        }

        .fixture-tag {
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            background: rgba(255, 255, 255, 0.08);
        }

        .scoreboard-wrapper {
            overflow-x: auto;
        }

        .scoreboard-table {
            width: 100%;
            border-collapse: collapse;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.85rem;
        }

        .scoreboard-table th {
            font-size: 0.68rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
            padding: 10px 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
        }

        .scoreboard-table th:first-child,
        .scoreboard-table td:first-child {
            text-align: left;
        }

        .scoreboard-table td {
            padding: 10px 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            text-align: center;
        }

        .team-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(0, 174, 239, 0.12);
            border: 1px solid rgba(0, 174, 239, 0.25);
            border-radius: 18px;
            padding: 8px 16px;
            margin: 0 12px 12px 0;
            color: #00aeef;
            font-weight: 500;
        }

        .empty-state {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.65);
        }

        @media (max-width: 991.98px) {
            .tournament-hero {
                margin: 32px auto;
            }

            .tournament-detail {
                padding: 26px;
            }

            .tournament-card {
                padding: 20px;
            }
        }

        @media (max-width: 767.98px) {
            .hero-highlights {
                flex-direction: column;
                gap: 12px;
            }

            .tournament-search-card {
                padding: 24px;
            }

            .detail-section {
                padding: 18px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="tournament-hero py-5 w-100">
        <span class="floating-square square-1"></span>
        <span class="floating-square square-2"></span>
        <span class="floating-square square-3"></span>
        <div class="container hero-content">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <span class="hero-kicker">Discover tournaments</span>
                    <h1 class="display-5 fw-bold hero-heading mt-3 mb-3">Find your next championship moment</h1>
                    <p class="hero-copy mb-4">Browse upcoming and in-progress competitions, explore fixtures in detail, and follow live standings — all wrapped in the same energetic palette as our sign-up experience.</p>
                    <div class="hero-highlights">
                        <div class="hero-highlight">
                            <strong class="d-block text-white">Smart Filters</strong>
                            Narrow tournaments by sport, club name, keywords, or dates in seconds.
                        </div>
                        <div class="hero-highlight">
                            <strong class="d-block text-white">Live Standings</strong>
                            Instant points tables update as results roll in.
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="tournament-search-card">
                        <form method="GET" action="{{ route('tournaments.search') }}" class="row g-3 tournament-search-form" data-states-endpoint="{{ route('locations.states') }}" data-cities-endpoint="{{ route('locations.cities') }}">
                            <div class="col-12">
                                <label class="search-label">Search</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    </span>
                                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control search-input" placeholder="Tournament or club name, city, keywords">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">Sport</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 16V8"></path>
                                            <path d="M4 8V16"></path>
                                            <path d="M4 12L12 16L20 12"></path>
                                            <path d="M4 12L12 8L20 12"></path>
                                        </svg>
                                    </span>
                                    <select name="sport" class="form-select search-input">
                                        <option value="">All sports</option>
                                        @foreach ($sports as $sport)
                                            <option value="{{ $sport->id }}" @selected(($filters['sport'] ?? null) == $sport->id)>{{ $sport->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">Country</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 5.25-9 12-9 12s-9-6.75-9-12a9 9 0 1 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                    </span>
                                    <select name="country" class="form-select search-input" data-location-filter="country">
                                        <option value="">All countries</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}" @selected(($filters['country'] ?? null) == (string) $country->id)>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">State / Region</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 9l9-7 9 7"></path>
                                            <path d="M9 22V12h6v10"></path>
                                        </svg>
                                    </span>
                                    <select name="state" class="form-select search-input" data-location-filter="state" @disabled(empty($filters['country']))>
                                        <option value="">{{ ($filters['country'] ?? null) ? 'All states' : 'Select a country first' }}</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}" @selected(($filters['state'] ?? null) == (string) $state->id)>{{ $state->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">City</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M2 12h20"></path>
                                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                        </svg>
                                    </span>
                                    <select name="city" class="form-select search-input" data-location-filter="city" @disabled(empty($filters['state']))>
                                        <option value="">{{ ($filters['state'] ?? null) ? 'All cities' : 'Select a state first' }}</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}" @selected(($filters['city'] ?? null) == (string) $city->id)>{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">Starts after</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </span>
                                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-control search-input">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="search-label">Ends before</label>
                                <div class="input-icon">
                                    <span class="input-icon-badge">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    </span>
                                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-control search-input">
                                </div>
                            </div>
                            <div class="col-md-6 align-self-end">
                                <button type="submit" class="btn hero-submit w-100" data-search-submit>Search tournaments</button>
                            </div>
                            <div class="col-12">
                                <div id="searchFeedback" class="small text-warning d-none" role="alert"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container tournament-layout">
        <div class="row g-4">
            <div class="col-lg-4">
                <div id="tournamentEmptyState" class="empty-state{{ $tournaments->isEmpty() ? '' : ' d-none' }}">
                    No tournaments match your filters just yet. Try widening the search to discover more competitions.
                </div>
                <div id="tournamentList" class="tournament-list{{ $tournaments->isEmpty() ? ' d-none' : '' }}">
                    @foreach ($tournaments as $tournament)
                        @php $payload = $tournamentLookup->get($tournament->id); @endphp
                        <button type="button" class="tournament-card{{ $loop->first ? ' active' : '' }}" data-tournament-id="{{ $tournament->id }}">
                            <div class="card-topline">
                                <span class="status-badge status-{{ $payload['status'] ?? 'scheduled' }}">{{ $payload['status_label'] ?? 'Scheduled' }}</span>
                                <span class="card-date">{{ $payload['date_range'] ?? 'Dates to be confirmed' }}</span>
                            </div>
                            <div class="card-title">{{ $payload['name'] ?? $tournament->name }}</div>
                            <p class="card-meta mb-2">{{ $payload['summary'] ?? '' }}</p>
                            <div class="card-tags">
                                @if (!empty($payload['sport']))
                                    <span class="card-tag">{{ $payload['sport'] }}</span>
                                @endif
                                @if (!empty($payload['location']))
                                    <span class="card-tag">{{ $payload['location'] }}</span>
                                @endif
                                @if (!empty($payload['host_club']))
                                    <span class="card-tag">Hosted by {{ $payload['host_club'] }}</span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-8">
                <div class="tournament-detail">
                    <div class="detail-empty{{ $tournaments->isNotEmpty() ? ' d-none' : '' }}" id="detailPlaceholder">
                        Select a tournament on the left to see fixtures, team information, and live standings.
                    </div>
                    <div class="detail-content{{ $tournaments->isEmpty() ? ' d-none' : '' }}" id="detailContent">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                            <div>
                                <span class="detail-status status-scheduled" id="detailStatus">Scheduled</span>
                                <h2 class="h3 detail-title" id="detailName"></h2>
                                <p class="detail-summary" id="detailSummary"></p>
                            </div>
                            <div class="detail-metrics" id="detailMetrics"></div>
                        </div>

                        <div class="detail-meta">
                            <div class="meta-item" id="detailDateRange">
                                <span class="meta-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </span>
                                <span></span>
                            </div>
                            <div class="meta-item" id="detailLocation">
                                <span class="meta-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0Z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </span>
                                <span></span>
                            </div>
                            <div class="meta-item" id="detailSport">
                                <span class="meta-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                        <path d="M2 12h20"></path>
                                    </svg>
                                </span>
                                <span></span>
                            </div>
                            <div class="meta-item" id="detailHost">
                                <span class="meta-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 20V4l12-2v16"></path>
                                        <path d="M2 6h4"></path>
                                        <path d="M2 12h4"></path>
                                        <path d="M2 18h4"></path>
                                    </svg>
                                </span>
                                <span></span>
                            </div>
                        </div>

                        <div class="upcoming-card d-none" id="upcomingMatchCard">
                            <div class="upcoming-label">Next fixture</div>
                            <div class="upcoming-teams" id="upcomingMatchTeams"></div>
                            <div class="upcoming-meta" id="upcomingMatchMeta"></div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="detail-section">
                                    <h5>Fixtures</h5>
                                    <div id="fixtureList"></div>
                                </div>
                                <div class="detail-section">
                                    <h5>Registered teams</h5>
                                    <div id="teamList"></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="detail-section">
                                    <h5>Points table</h5>
                                    <div class="scoreboard-wrapper" id="scoreboardWrapper"></div>
                                </div>
                                <div class="detail-section">
                                    <h5>Quick facts</h5>
                                    <ul class="list-unstyled mb-0" id="detailHighlights"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchForm = document.querySelector('.tournament-search-form');
            const tournamentList = document.getElementById('tournamentList');
            const tournamentEmptyState = document.getElementById('tournamentEmptyState');
            const detailPlaceholder = document.getElementById('detailPlaceholder');
            const detailContent = document.getElementById('detailContent');
            const detailStatus = document.getElementById('detailStatus');
            const detailName = document.getElementById('detailName');
            const detailSummary = document.getElementById('detailSummary');
            const detailMetrics = document.getElementById('detailMetrics');
            const detailDateRange = document.querySelector('#detailDateRange span:last-child');
            const detailLocation = document.querySelector('#detailLocation span:last-child');
            const detailSport = document.querySelector('#detailSport span:last-child');
            const detailHost = document.querySelector('#detailHost span:last-child');
            const upcomingCard = document.getElementById('upcomingMatchCard');
            const upcomingTeams = document.getElementById('upcomingMatchTeams');
            const upcomingMeta = document.getElementById('upcomingMatchMeta');
            const fixtureList = document.getElementById('fixtureList');
            const teamList = document.getElementById('teamList');
            const scoreboardWrapper = document.getElementById('scoreboardWrapper');
            const detailHighlights = document.getElementById('detailHighlights');
            const searchFeedback = document.getElementById('searchFeedback');
            const submitButton = searchForm?.querySelector('[data-search-submit]');
            const submitButtonLabel = submitButton?.textContent?.trim() || '';
            const statesEndpoint = searchForm?.dataset.statesEndpoint;
            const citiesEndpoint = searchForm?.dataset.citiesEndpoint;
            const countrySelect = searchForm?.querySelector('[data-location-filter="country"]');
            const stateSelect = searchForm?.querySelector('[data-location-filter="state"]');
            const citySelect = searchForm?.querySelector('[data-location-filter="city"]');
            const initialTournaments = @json($tournamentPayload);

            let lookup = new Map();
            let activeId = null;
            let listButtons = [];
            let stateRequestController = null;
            let cityRequestController = null;
            let searchRequestController = null;

            const setStatus = (status, label) => {
                if (!detailStatus) {
                    return;
                }
                detailStatus.textContent = label;
                detailStatus.className = `detail-status status-${status}`;
            };

            const renderMetrics = (metrics) => {
                if (!detailMetrics) {
                    return;
                }
                detailMetrics.innerHTML = '';
                metrics.forEach((metric) => {
                    const card = document.createElement('div');
                    card.className = 'metric-card';
                    const label = document.createElement('div');
                    label.className = 'metric-label';
                    label.textContent = metric.label;
                    const value = document.createElement('div');
                    value.className = 'metric-value';
                    value.textContent = metric.value;
                    card.appendChild(label);
                    card.appendChild(value);
                    detailMetrics.appendChild(card);
                });
            };

            const renderUpcoming = (match) => {
                if (!upcomingCard || !upcomingTeams || !upcomingMeta) {
                    return;
                }

                if (!match) {
                    upcomingCard.classList.add('d-none');
                    upcomingTeams.textContent = '';
                    upcomingMeta.textContent = '';
                    return;
                }

                upcomingCard.classList.remove('d-none');
                const homeName = match.home?.name ?? 'TBD';
                const awayName = match.away?.name ?? 'TBD';
                upcomingTeams.textContent = `${homeName} vs ${awayName}`;
                upcomingMeta.textContent = `${match.date} • ${match.time} • ${match.venue}`;
            };

            const renderFixtures = (matches) => {
                if (!fixtureList) {
                    return;
                }

                fixtureList.innerHTML = '';

                if (!matches.length) {
                    const empty = document.createElement('div');
                    empty.className = 'empty-state';
                    empty.textContent = 'Fixtures will appear here once the schedule is published.';
                    fixtureList.appendChild(empty);
                    return;
                }

                matches.forEach((match) => {
                    const card = document.createElement('div');
                    card.className = `fixture-card status-${match.status}`;

                    const header = document.createElement('div');
                    header.className = 'fixture-header';
                    const date = document.createElement('span');
                    date.className = 'fixture-date';
                    date.textContent = `${match.date} • ${match.time}`;
                    const tag = document.createElement('span');
                    tag.className = 'fixture-tag';
                    tag.textContent = match.status_label;
                    header.appendChild(date);
                    header.appendChild(tag);

                    const teams = document.createElement('div');
                    teams.className = 'fixture-teams';
                    const home = document.createElement('div');
                    home.className = 'fixture-team text-start';
                    home.textContent = match.home?.name ?? 'TBD';
                    const score = document.createElement('div');
                    score.className = 'fixture-score';
                    score.textContent = match.score ? `${match.score.home} – ${match.score.away}` : 'vs';
                    const away = document.createElement('div');
                    away.className = 'fixture-team text-end';
                    away.textContent = match.away?.name ?? 'TBD';
                    teams.appendChild(home);
                    teams.appendChild(score);
                    teams.appendChild(away);

                    const venue = document.createElement('div');
                    venue.className = 'fixture-venue';
                    venue.textContent = match.venue;

                    card.appendChild(header);
                    card.appendChild(teams);
                    card.appendChild(venue);
                    fixtureList.appendChild(card);
                });
            };

            const renderTeams = (teams) => {
                if (!teamList) {
                    return;
                }

                teamList.innerHTML = '';

                if (!teams.length) {
                    const empty = document.createElement('div');
                    empty.className = 'empty-state';
                    empty.textContent = 'Teams will display here once registrations are confirmed.';
                    teamList.appendChild(empty);
                    return;
                }

                teams.forEach((team) => {
                    const chip = document.createElement('span');
                    chip.className = 'team-chip';
                    chip.textContent = team.club ? `${team.name} • ${team.club}` : team.name;
                    teamList.appendChild(chip);
                });
            };

            const renderScoreboard = (rows) => {
                if (!scoreboardWrapper) {
                    return;
                }

                scoreboardWrapper.innerHTML = '';

                if (!rows.length) {
                    const empty = document.createElement('div');
                    empty.className = 'empty-state';
                    empty.textContent = 'Standings will update as soon as results are reported.';
                    scoreboardWrapper.appendChild(empty);
                    return;
                }

                const table = document.createElement('table');
                table.className = 'scoreboard-table';
                const thead = document.createElement('thead');
                thead.innerHTML = '<tr><th>Club</th><th>P</th><th>W</th><th>D</th><th>L</th><th>GF</th><th>GA</th><th>GD</th><th>Pts</th></tr>';
                const tbody = document.createElement('tbody');

                rows.forEach((row) => {
                    const tr = document.createElement('tr');
                    const makeCell = (value, alignStart = false) => {
                        const td = document.createElement('td');
                        if (alignStart) {
                            td.classList.add('text-start');
                        }
                        td.textContent = value;
                        return td;
                    };

                    tr.appendChild(makeCell(row.club_name, true));
                    tr.appendChild(makeCell(row.played));
                    tr.appendChild(makeCell(row.wins));
                    tr.appendChild(makeCell(row.draws));
                    tr.appendChild(makeCell(row.losses));
                    tr.appendChild(makeCell(row.goals_for));
                    tr.appendChild(makeCell(row.goals_against));
                    tr.appendChild(makeCell(row.goal_diff));
                    tr.appendChild(makeCell(row.points));
                    tbody.appendChild(tr);
                });

                table.appendChild(thead);
                table.appendChild(tbody);
                scoreboardWrapper.appendChild(table);
            };

            const renderHighlights = (tournament) => {
                if (!detailHighlights) {
                    return;
                }

                detailHighlights.innerHTML = '';
                const highlights = [];

                if (tournament.location) {
                    highlights.push(`Hosted at ${tournament.location}`);
                }
                if (tournament.host_club) {
                    highlights.push(`Organised by ${tournament.host_club}`);
                }
                if (tournament.sport) {
                    highlights.push(`${tournament.sport} showcase`);
                }
                if (Array.isArray(tournament.metrics)) {
                    const completed = tournament.metrics.find((item) => item.label === 'Completed');
                    if (completed) {
                        highlights.push(`${completed.value} fixtures completed`);
                    }
                }

                if (!highlights.length) {
                    const item = document.createElement('li');
                    item.textContent = 'More details coming soon.';
                    detailHighlights.appendChild(item);
                    return;
                }

                highlights.forEach((text) => {
                    const item = document.createElement('li');
                    item.textContent = text;
                    detailHighlights.appendChild(item);
                });
            };

            const clearDetail = () => {
                setStatus('scheduled', 'Scheduled');

                if (detailName) {
                    detailName.textContent = '';
                }
                if (detailSummary) {
                    detailSummary.textContent = '';
                }

                renderMetrics([]);

                if (detailDateRange) {
                    detailDateRange.textContent = 'Dates to be confirmed';
                }
                if (detailLocation) {
                    detailLocation.textContent = 'Venue to be announced';
                }
                if (detailSport) {
                    detailSport.textContent = 'Sport to be confirmed';
                }
                if (detailHost) {
                    detailHost.textContent = 'Host club to be announced';
                }

                renderUpcoming(null);

                if (fixtureList) {
                    fixtureList.innerHTML = '';
                }
                if (teamList) {
                    teamList.innerHTML = '';
                }
                if (scoreboardWrapper) {
                    scoreboardWrapper.innerHTML = '';
                }
                if (detailHighlights) {
                    detailHighlights.innerHTML = '';
                }
            };

            const showPlaceholder = () => {
                if (!detailPlaceholder || !detailContent) {
                    return;
                }

                clearDetail();
                detailPlaceholder.classList.remove('d-none');
                detailContent.classList.add('d-none');
            };

            const renderTournament = (id) => {
                const data = lookup.get(id);
                if (!data) {
                    showPlaceholder();
                    return;
                }

                if (detailPlaceholder && detailContent) {
                    detailPlaceholder.classList.add('d-none');
                    detailContent.classList.remove('d-none');
                }

                setStatus(data.status, data.status_label);

                if (detailName) {
                    detailName.textContent = data.name;
                }
                if (detailSummary) {
                    detailSummary.textContent = data.summary || '';
                }

                renderMetrics(Array.isArray(data.metrics) ? data.metrics : []);

                if (detailDateRange) {
                    detailDateRange.textContent = data.date_range || 'Dates to be confirmed';
                }
                if (detailLocation) {
                    detailLocation.textContent = data.location || 'Venue to be announced';
                }
                if (detailSport) {
                    detailSport.textContent = data.sport || 'Sport to be confirmed';
                }
                if (detailHost) {
                    detailHost.textContent = data.host_club ? `Hosted by ${data.host_club}` : 'Host club to be announced';
                }

                renderUpcoming(data.upcoming_match || null);
                renderFixtures(Array.isArray(data.matches) ? data.matches : []);
                renderTeams(Array.isArray(data.teams) ? data.teams : []);
                renderScoreboard(Array.isArray(data.scoreboard) ? data.scoreboard : []);
                renderHighlights(data);
            };

            const buildTag = (text) => {
                const span = document.createElement('span');
                span.className = 'card-tag';
                span.textContent = text;
                return span;
            };

            const buildCard = (item, isActive) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `tournament-card${isActive ? ' active' : ''}`;
                button.dataset.tournamentId = item.id;

                const topLine = document.createElement('div');
                topLine.className = 'card-topline';

                const status = document.createElement('span');
                status.className = `status-badge status-${item.status || 'scheduled'}`;
                status.textContent = item.status_label || 'Scheduled';

                const date = document.createElement('span');
                date.className = 'card-date';
                date.textContent = item.date_range || 'Dates to be confirmed';

                topLine.appendChild(status);
                topLine.appendChild(date);

                const title = document.createElement('div');
                title.className = 'card-title';
                title.textContent = item.name;

                const meta = document.createElement('p');
                meta.className = 'card-meta mb-2';
                meta.textContent = item.summary || '';

                const tags = document.createElement('div');
                tags.className = 'card-tags';

                if (item.sport) {
                    tags.appendChild(buildTag(item.sport));
                }
                if (item.location) {
                    tags.appendChild(buildTag(item.location));
                }
                if (item.host_club) {
                    tags.appendChild(buildTag(`Hosted by ${item.host_club}`));
                }

                button.appendChild(topLine);
                button.appendChild(title);
                button.appendChild(meta);
                if (tags.childElementCount) {
                    button.appendChild(tags);
                }

                return button;
            };

            const onCardClick = (event) => {
                const id = Number(event.currentTarget.dataset.tournamentId);
                if (!id || !lookup.has(id)) {
                    return;
                }

                if (activeId === id) {
                    return;
                }

                activeId = id;

                listButtons.forEach((button) => {
                    const matches = Number(button.dataset.tournamentId) === id;
                    button.classList.toggle('active', matches);
                });

                renderTournament(id);
            };

            const bindListButtons = () => {
                if (!tournamentList) {
                    listButtons = [];
                    return;
                }

                listButtons = Array.from(tournamentList.querySelectorAll('[data-tournament-id]'));
                listButtons.forEach((button) => {
                    button.addEventListener('click', onCardClick);
                });
            };

            const renderList = (items, options = {}) => {
                const collection = Array.isArray(items) ? items : [];
                const preserveActive = options.preserveActive ?? true;
                const explicitActiveId = options.activeId;

                lookup = new Map(collection.map((item) => [item.id, item]));

                let nextActive = null;
                if (preserveActive && activeId && lookup.has(activeId)) {
                    nextActive = activeId;
                } else if (explicitActiveId && lookup.has(explicitActiveId)) {
                    nextActive = explicitActiveId;
                } else if (collection.length) {
                    nextActive = collection[0].id;
                }

                activeId = nextActive;

                if (!tournamentList || !tournamentEmptyState) {
                    return;
                }

                if (!collection.length) {
                    tournamentList.innerHTML = '';
                    tournamentList.classList.add('d-none');
                    tournamentEmptyState.classList.remove('d-none');
                    showPlaceholder();
                    listButtons = [];
                    return;
                }

                tournamentEmptyState.classList.add('d-none');
                tournamentList.classList.remove('d-none');
                tournamentList.innerHTML = '';

                collection.forEach((item) => {
                    const button = buildCard(item, item.id === activeId);
                    tournamentList.appendChild(button);
                });

                bindListButtons();

                if (activeId && lookup.has(activeId)) {
                    renderTournament(activeId);
                } else {
                    showPlaceholder();
                }
            };

            const setFeedback = (message) => {
                if (!searchFeedback) {
                    return;
                }

                if (!message) {
                    searchFeedback.textContent = '';
                    searchFeedback.classList.add('d-none');
                    return;
                }

                searchFeedback.textContent = message;
                searchFeedback.classList.remove('d-none');
            };

            const setLoading = (state) => {
                if (submitButton) {
                    submitButton.disabled = state;
                    submitButton.textContent = state ? 'Searching…' : submitButtonLabel;
                }

                if (searchForm) {
                    searchForm.classList.toggle('is-loading', state);
                }
            };

            const buildQueryString = () => {
                if (!searchForm) {
                    return '';
                }

                const formData = new FormData(searchForm);
                const params = new URLSearchParams();

                formData.forEach((value, key) => {
                    if (typeof value === 'string') {
                        const trimmed = value.trim();
                        if (trimmed === '') {
                            return;
                        }
                        params.append(key, trimmed);
                        return;
                    }

                    if (value != null) {
                        params.append(key, value);
                    }
                });

                return params.toString();
            };

            const updateHistory = (queryString) => {
                if (!searchForm || typeof window === 'undefined') {
                    return;
                }

                const base = searchForm.action || window.location.pathname;
                const url = new URL(base, window.location.origin);
                url.search = queryString;
                window.history.replaceState({}, '', `${url.pathname}${url.search}`);
            };

            const triggerSearch = () => {
                if (!searchForm) {
                    return;
                }

                if (typeof searchForm.requestSubmit === 'function') {
                    searchForm.requestSubmit();
                } else {
                    const event = new Event('submit', { bubbles: true, cancelable: true });
                    searchForm.dispatchEvent(event);
                }
            };

            const performSearch = async () => {
                if (!searchForm) {
                    return;
                }

                const queryString = buildQueryString();
                const base = searchForm.action || window.location.pathname;
                const urlObject = new URL(base, window.location.origin);
                urlObject.search = queryString;
                const requestUrl = urlObject.toString();

                if (searchRequestController && typeof searchRequestController.abort === 'function') {
                    searchRequestController.abort();
                }

                searchRequestController = typeof AbortController !== 'undefined' ? new AbortController() : null;

                setFeedback('');
                setLoading(true);

                try {
                    const options = {
                        headers: { Accept: 'application/json' },
                    };

                    if (searchRequestController) {
                        options.signal = searchRequestController.signal;
                    }

                    const response = await fetch(requestUrl, options);

                    if (!response.ok) {
                        throw new Error('Unable to fetch tournaments');
                    }

                    const payload = await response.json();
                    const items = Array.isArray(payload?.data?.tournaments) ? payload.data.tournaments : [];

                    renderList(items);
                    updateHistory(queryString);
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error(error);
                    setFeedback('We couldn’t load tournaments right now. Please try again.');
                } finally {
                    setLoading(false);
                    searchRequestController = null;
                }
            };

            const buildPlaceholderOption = (text) => {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = text;
                return option;
            };

            const resetSelect = (select, placeholder, disable = true) => {
                if (!select) {
                    return;
                }

                select.innerHTML = '';
                select.appendChild(buildPlaceholderOption(placeholder));
                select.disabled = disable;
            };

            const populateSelect = (select, items, placeholder) => {
                if (!select) {
                    return;
                }

                const previousValue = select.value;
                select.innerHTML = '';
                select.appendChild(buildPlaceholderOption(placeholder));

                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });

                const hasPrevious = items.some((item) => item.id === previousValue);
                select.value = hasPrevious ? previousValue : '';
                select.disabled = items.length === 0;
            };

            const loadStates = async (countryId) => {
                if (!statesEndpoint || !stateSelect) {
                    return;
                }

                if (stateRequestController && typeof stateRequestController.abort === 'function') {
                    stateRequestController.abort();
                }

                stateRequestController = typeof AbortController !== 'undefined' ? new AbortController() : null;

                resetSelect(stateSelect, countryId ? 'Loading states...' : 'Select a country first', true);
                resetSelect(citySelect, 'Select a state first', true);

                if (!countryId) {
                    stateRequestController = null;
                    return;
                }

                try {
                    const options = {
                        headers: { Accept: 'application/json' },
                    };

                    if (stateRequestController) {
                        options.signal = stateRequestController.signal;
                    }

                    const response = await fetch(`${statesEndpoint}?country=${encodeURIComponent(countryId)}`, options);

                    if (!response.ok) {
                        throw new Error('Unable to load states');
                    }

                    const payload = await response.json();
                    populateSelect(stateSelect, payload.data || [], 'All states');
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    resetSelect(stateSelect, 'Unable to load states', true);
                } finally {
                    stateRequestController = null;
                }
            };

            const loadCities = async (stateId) => {
                if (!citiesEndpoint || !citySelect) {
                    return;
                }

                if (cityRequestController && typeof cityRequestController.abort === 'function') {
                    cityRequestController.abort();
                }

                cityRequestController = typeof AbortController !== 'undefined' ? new AbortController() : null;

                resetSelect(citySelect, stateId ? 'Loading cities...' : 'Select a state first', true);

                if (!stateId) {
                    cityRequestController = null;
                    return;
                }

                try {
                    const options = {
                        headers: { Accept: 'application/json' },
                    };

                    if (cityRequestController) {
                        options.signal = cityRequestController.signal;
                    }

                    const response = await fetch(`${citiesEndpoint}?state=${encodeURIComponent(stateId)}`, options);

                    if (!response.ok) {
                        throw new Error('Unable to load cities');
                    }

                    const payload = await response.json();
                    populateSelect(citySelect, payload.data || [], 'All cities');
                } catch (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    resetSelect(citySelect, 'Unable to load cities', true);
                } finally {
                    cityRequestController = null;
                }
            };

            renderList(initialTournaments, { preserveActive: false });

            if (countrySelect) {
                countrySelect.addEventListener('change', () => {
                    const selectedCountry = countrySelect.value;
                    loadStates(selectedCountry);
                    triggerSearch();
                });
            }

            if (stateSelect) {
                stateSelect.addEventListener('change', () => {
                    const selectedState = stateSelect.value;
                    loadCities(selectedState);
                    triggerSearch();
                });
            }

            if (citySelect) {
                citySelect.addEventListener('change', () => {
                    triggerSearch();
                });
            }

            if (searchForm) {
                searchForm.addEventListener('submit', (event) => {
                    event.preventDefault();
                    performSearch();
                });

                const searchInput = searchForm.querySelector('input[name="q"]');
                let searchDebounce;

                if (searchInput) {
                    searchInput.addEventListener('input', () => {
                        clearTimeout(searchDebounce);
                        searchDebounce = setTimeout(() => {
                            triggerSearch();
                        }, 450);
                    });
                }

                ['select[name="sport"]', 'input[name="start_date"]', 'input[name="end_date"]'].forEach((selector) => {
                    const field = searchForm.querySelector(selector);
                    if (field) {
                        field.addEventListener('change', () => {
                            triggerSearch();
                        });
                    }
                });
            }
        });
    </script>
@endsection

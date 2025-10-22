@extends('layouts.default')

@section('title', 'Club Finder')

@section('styles')
    <style>
        .club-hero {
            background: linear-gradient(135deg, rgba(8, 28, 60, 0.95), rgba(0, 96, 128, 0.85));
            border-radius: 32px;
            margin: 48px auto;
            overflow: hidden;
            position: relative;
        }

        .club-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(0, 174, 239, 0.25), transparent 60%);
        }

        .club-hero-content {
            position: relative;
            z-index: 2;
        }

        .club-kicker {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.12em;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .club-search-card {
            background: rgba(10, 24, 48, 0.95);
            border-radius: 26px;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 60px rgba(4, 12, 30, 0.5);
        }

        .club-search-label {
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.55);
            margin-bottom: 6px;
        }

        .club-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            border-radius: 18px;
            min-height: 54px;
            padding: 12px 18px;
        }

        .club-input:focus {
            border-color: rgba(0, 174, 239, 0.85);
            box-shadow: 0 0 0 0.2rem rgba(0, 174, 239, 0.2);
            background: rgba(0, 174, 239, 0.12);
            color: #fff;
        }

        .club-card {
            display: block;
            height: 100%;
            border-radius: 22px;
            background: rgba(9, 16, 35, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: inherit;
            text-decoration: none;
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .club-card:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 174, 239, 0.75);
            box-shadow: 0 24px 50px rgba(0, 24, 60, 0.4);
        }

        .club-card-body {
            padding: 26px;
        }

        .club-logo {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(0, 174, 239, 0.18);
            border: 1px solid rgba(0, 174, 239, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-weight: 700;
            color: #00aeef;
            text-transform: uppercase;
        }

        .club-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .club-meta {
            color: rgba(255, 255, 255, 0.6);
        }

        .club-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 18px 0;
        }

        .club-stat-pill {
            background: rgba(255, 255, 255, 0.06);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.75);
        }

        .club-players {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .club-player-chip {
            background: rgba(0, 174, 239, 0.16);
            border: 1px solid rgba(0, 174, 239, 0.35);
            border-radius: 14px;
            padding: 6px 12px;
            font-size: 0.8rem;
            color: #00aeef;
        }

        .club-empty {
            padding: 40px;
            text-align: center;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.04);
            color: rgba(255, 255, 255, 0.65);
        }

        @media (max-width: 991.98px) {
            .club-hero {
                margin: 32px auto;
            }

            .club-card-body {
                padding: 22px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="club-hero py-5 w-100">
        <div class="container club-hero-content">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <span class="club-kicker">Discover clubs</span>
                    <h1 class="display-5 fw-bold text-white mt-3 mb-3">Explore community clubs and their rosters</h1>
                    <p class="text-white-50 mb-4">Search by club name or sport, preview highlighted players, and click through to rich profiles with schedules, rosters, and community updates.</p>
                </div>
                <div class="col-lg-6">
                    <div class="club-search-card">
                        <form method="GET" action="{{ route('clubs.search') }}" class="row g-3">
                            <div class="col-12">
                                <label class="club-search-label">Search</label>
                                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control club-input" placeholder="Club name or keywords">
                            </div>
                            <div class="col-md-6">
                                <label class="club-search-label">Sport</label>
                                <select name="sport" class="form-select club-input">
                                    <option value="">All sports</option>
                                    @foreach ($sports as $sport)
                                        <option value="{{ $sport->id }}" @selected(($filters['sport'] ?? null) == $sport->id)>{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 align-self-end">
                                <button type="submit" class="btn btn-primary w-100">Search clubs</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        @if ($clubs->count())
            <div class="row g-4">
                @foreach ($clubs as $club)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('public.club.profile', $club->slug) }}" class="club-card">
                            <div class="club-card-body">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="club-logo">
                                        @if ($club->logo)
                                            <img src="{{ $club->logo }}" alt="{{ $club->name }} logo">
                                        @else
                                            <span>{{ mb_substr($club->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="h5 text-white mb-1">{{ $club->name }}</h3>
                                        <div class="club-meta">
                                            {{ optional($club->sport)->name ?? 'Multi-sport Club' }}
                                            @if ($club->address)
                                                · {{ $club->address }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($club->bio)
                                    <p class="text-white-50 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($club->bio), 140) }}</p>
                                @endif

                                <div class="club-stats">
                                    <span class="club-stat-pill">Players · {{ $club->players_count }}</span>
                                    <span class="club-stat-pill">Teams · {{ $club->teams_count }}</span>
                                </div>

                                <div class="club-players">
                                    @forelse ($club->players->take(6) as $player)
                                        <span class="club-player-chip">{{ $player->name }}</span>
                                    @empty
                                        <span class="text-white-50">No players listed yet</span>
                                    @endforelse
                                    @if ($club->players_count > 6)
                                        <span class="club-player-chip">+{{ $club->players_count - 6 }} more</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $clubs->links() }}
            </div>
        @else
            <div class="club-empty">
                <h3 class="h5 text-white">No clubs match your filters yet</h3>
                <p class="mb-0">Try adjusting the name or sport filters to find the communities you are looking for.</p>
            </div>
        @endif
    </div>
@endsection

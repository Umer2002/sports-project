@php
    use Illuminate\Support\Str;

    $context = $blogContext ?? request()->query('context');
    $user = auth()->user();
    $hasRole = static function ($user, $role) {
        return $user && method_exists($user, 'hasRole') && $user->hasRole($role);
    };

    if (!empty($blogLayout)) {
        $resolvedLayout = $blogLayout;
    } elseif ($context === 'club' && $hasRole($user, 'club')) {
        $resolvedLayout = 'layouts.club-dashboard';
    } elseif ($hasRole($user, 'referee')) {
        $resolvedLayout = 'layouts.referee-dashboard';
    }elseif ($hasRole($user, 'coach')) {
        $resolvedLayout = 'layouts.coach-dashboard';
    }
    else {
        $resolvedLayout = 'layouts.player-new';
    }

    $isClubLayout = $resolvedLayout === 'layouts.club-dashboard';
    $isRefereeLayout = $resolvedLayout === 'layouts.referee-dashboard';
    $blogRouteQuery = $context ? ['context' => $context] : [];
@endphp

@extends($resolvedLayout)

@section('title', 'Tournament Directory')


    @if ($isRefereeLayout)
        <style>
            .tournament-directory-wrapper {
                padding: 32px 24px 48px;
                color: #f8fafc;
            }

            .tournament-directory-wrapper header.header {
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.18), rgba(239, 68, 68, 0.22));
                border-radius: 18px;
                padding: 24px 28px;
                margin-bottom: 24px;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .tournament-directory-wrapper header.header h1 {
                font-size: 28px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 8px;
            }

            .tournament-directory-wrapper header.header p {
                color: rgba(255, 255, 255, 0.75);
                margin: 0;
            }

            .tournament-directory-wrapper form {
                background: rgba(15, 23, 42, 0.82);
                border: 1px solid rgba(148, 163, 184, 0.15);
                border-radius: 20px;
                padding: 20px;
                margin-bottom: 24px;
            }

            .tournament-directory-wrapper .filters {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }

            .tournament-directory-wrapper .filters select,
            .tournament-directory-wrapper .filters input {
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(148, 163, 184, 0.25);
                color: #f1f5f9;
                border-radius: 10px;
                padding: 10px 12px;
            }

            .tournament-directory-wrapper .filters select:focus,
            .tournament-directory-wrapper .filters input:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.4);
                outline: none;
            }

            .tournament-directory-wrapper .btn-gradient {
                background: linear-gradient(135deg, #22d3ee, #6366f1);
                border: none;
                color: #0f172a;
                font-weight: 600;
                border-radius: 10px;
                padding: 10px 18px;
            }

            .tournament-directory-wrapper .btn-clear {
                color: #9ca3af;
                text-decoration: none;
                align-self: center;
            }

            .tournament-directory-wrapper .table {
                width: 100%;
                display: table;
                background: rgba(15, 23, 42, 0.85);
                border-radius: 18px;
                border: 1px solid rgba(148, 163, 184, 0.12);
                border-collapse: separate;
                border-spacing: 0;
                overflow: hidden;
            }

            .tournament-directory-wrapper .table-row {
                display: table-row;
                background: transparent;
            }

            .tournament-directory-wrapper .table-row.header-row {
                display: table-row;
                background: rgba(30, 41, 59, 0.8);
                color: #e2e8f0;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .tournament-directory-wrapper .table-row div {
                display: table-cell;
                padding: 16px 18px;
                color: #e2e8f0;
                vertical-align: middle;
                border-bottom: 1px solid rgba(148, 163, 184, 0.08);
            }

            .tournament-directory-wrapper .table-row .bold {
                font-weight: 600;
                color: #f8fafc;
            }

            .tournament-directory-wrapper .status-row {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .tournament-directory-wrapper .status-row .status {
                background: rgba(34, 197, 94, 0.18);
                color: #34d399;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 0.78rem;
                font-weight: 600;
            }

            .tournament-directory-wrapper .status-row .btn-gradient {
                background: linear-gradient(135deg, #f97316, #fb7185);
                color: #fff;
                padding: 6px 14px;
                font-size: 0.78rem;
            }

            .tournament-directory-wrapper .table-row:last-child div {
                border-bottom: none;
            }

            .tournament-directory-wrapper .pagination .page-link {
                background: rgba(15, 23, 42, 0.85);
                border: 1px solid rgba(148, 163, 184, 0.2);
                color: #cbd5f5;
            }

            .tournament-directory-wrapper .pagination .page-item.active .page-link {
                background: linear-gradient(135deg, #22d3ee, #6366f1);
                border-color: transparent;
                color: #0f172a;
            }
        </style>
    @endif


@section('content')
    <div class="{{ $isRefereeLayout ? 'tournament-directory-wrapper' : '' }}">

    <!-- Header -->
    <header class="header">
        <h1>🏆 <span>Tournament Directory</span></h1>
        <p>Search and register for tournaments by location, date, sport, division, age, and more.</p>
    </header>

    <!-- Filters -->
    <form method="GET" class="border-0 shadow-sm mb-3">
        @csrf
        <div class="filters">

            <select name="sport_id" onchange="this.form.submit()">
                @foreach ($sports as $s)
                    <option value="{{ $s->id }}" @selected($sportId == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>

            <select name="division_id">
                <option value="">All</option>
                @foreach ($divisions as $d)
                    <option value="{{ $d->id }}" @selected($division == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>

            <select name="gender_id">
                <option value="">All</option>
                @foreach ($genders as $g)
                    <option value="{{ $g->id }}" @selected($gender == $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>

            <select name="age_group_id">
                <option value="">All</option>
                @foreach ($ageGroups as $ag)
                    <option value="{{ $ag->id }}" @selected($ageGroup == $ag->id)>{{ $ag->name }}</option>
                @endforeach
            </select>

            <label class="form-label">Province / State</label>
            <select name="state_id" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($states as $s)
                    <option value="{{ $s->id }}" @selected($stateId == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>

            <select name="city_id">
                <option value="">All</option>
                @foreach ($cities as $c)
                    <option value="{{ $c->id }}" @selected($cityId == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ optional($dateFrom)->format('Y-m-d') }}" class="form-control">

            <input type="date" name="date_to" value="{{ optional($dateTo)->format('Y-m-d') }}" class="form-control">

            <input type="number" step="0.01" name="max_fee" value="{{ $maxFee }}" class="form-control"
                placeholder="e.g. 300">

            <select name="status">
                <option value="">Any</option>
                <option value="open" @selected($status === 'open')>Registration Open</option>
                <option value="waitlist" @selected($status === 'waitlist')>Waitlist</option>
                <option value="closed" @selected($status === 'closed')>Closed</option>
            </select>
            <button class=" btn-gradient ">Search</button>
            <a href="{{ route('player.tournaments.directory') }}" class=" btn-clear">Clear Filters</a>
        </div>
    </form>

    <!-- Results header -->
    <p class="small text-muted mb-2">
        Results · {{ $tournaments->total() }} tournaments
    </p>

    <!-- Results table-like list -->
    <div class="table">
        <!-- Table Header -->
        <div class="table-row header-row">
            <div>Tournament</div>
            <div>City / Province</div>
            <div>Dates</div>
            <div>Divisions</div>
            <div>Teams</div>
            <div>Fee</div>
            <div>Status</div>
        </div>

        @forelse($tournaments as $t)
            @php
                $isOpen = optional($t->registration_cutoff_date)->isFuture() ?? false;
                $isWaitlist = $t->joining_type === 'waitlist';
                $statusLabel = $isWaitlist ? 'Waitlist' : ($isOpen ? 'Open' : 'Closed');
                $btnText = $isWaitlist ? 'Join Waitlist' : ($isOpen ? 'Register' : 'Details');
                $btnDisabled = !$isOpen && !$isWaitlist;
            @endphp
            <div class="table-row">
                <div class="bold">{{ $t->name }}</div>
                <div> {{ optional($t->city)->name ?? '—' }},
                    {{ optional($t->state)->name ?? '—' }}</div>
                <div> {{ optional($t->start_date)->format('M j') }} – {{ optional($t->end_date)->format('M j, Y') }}
                </div>
                <div>{{ optional($t->division)->name ?? '—' }}</div>
                <div>{{ $t->teams_count ?? 0 }}</div>
                <div>${{ number_format((float) $t->joining_fee, 2) }}</div>
                <div class="status-row">
                    <span class="status open">Open</span>
                    <button class="btn-gradient small">Register</button>
                </div>
            </div>


        @empty
            <div class="table-row">
                <div colspan="8" class="text-center text-muted py-4">No tournaments match your filters.</div>
            </div>
        @endforelse

    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end">
        {{ $tournaments->links() }}
    </div>

    <p class="text-muted small mt-2">
        Tip: Filter by province, city, date range, division, age, status, and max fee.
    </p>
    </div>
@endsection

@extends('layouts.club-dashboard')

@section('title', 'Club Financial Dashboard')
@section('page_title', 'Financials')

@section('header_styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cedarville+Cursive&display=swap');

        .financial-container {
            color: #e5e7eb;
        }

        .financial-card {
            background: linear-gradient(
                180deg,
                color-mix(in srgb, var(--card-bg) 95%, rgba(255, 255, 255, 0.92)) 0%,
                color-mix(in srgb, var(--card-bg) 90%, rgba(15, 23, 42, 0.1)) 100%
            );
            border: 1px solid color-mix(in srgb, var(--border) 60%, transparent);
            border-radius: 18px;
            padding: 24px;
            height: 100%;
            color: var(--text-primary);
            box-shadow: 0 16px 30px -20px rgba(15, 23, 42, 0.15);
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        [data-bs-theme='dark'] body .financial-card {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            border-color: rgba(148, 163, 184, 0.1);
            box-shadow: 0 20px 36px -24px rgba(8, 15, 35, 0.4);
        }

        .payout-calculator .form-control,
        .payout-calculator .form-select {
            background: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: #f9fafb;
            border-radius: 12px;
            height: 48px;
        }

        .payout-calculator .btn-quick {
            border-radius: 999px;
            padding: 8px 24px;
            background: rgba(148, 163, 184, 0.12);
            color: #f9fafb;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        .payout-calculator .btn-quick.active,
        .payout-calculator .btn-quick:hover {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-color: transparent;
        }

        .countdown-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }

        .countdown-card {
            border-radius: 16px;
            padding: 18px 12px;
            text-align: center;
            color: #0f172a;
            font-weight: 600;
        }

        .countdown-card .value {
            font-size: 28px;
            display: block;
            margin-bottom: 4px;
        }

        .countdown-card .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.9;
        }

        .countdown-card.purple {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .countdown-card.orange {
            background: linear-gradient(135deg, #f97316, #f59e0b);
        }

        .countdown-card.blue {
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
        }

        .countdown-card.pink {
            background: linear-gradient(135deg, #ec4899, #f472b6);
        }

        .countdown-card.green {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .summary-card {
            background: #0f172a;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(148, 163, 184, 0.15);
        }

        .summary-label {
            font-size: 13px;
            color: rgba(226, 232, 240, 0.7);
        }

        .summary-value {
            font-size: 28px;
            font-weight: 700;
            color: #22c55e;
        }

        .summary-subtext {
            font-size: 13px;
            color: rgba(226, 232, 240, 0.65);
        }

        .financial-actions .btn {
            border-radius: 10px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .financial-actions .btn-success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border: none;
        }

        .financial-actions .btn-outline-light {
            border-color: rgba(148, 163, 184, 0.3);
            color: #e5e7eb;
        }

        .sponsorship-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(59, 130, 246, 0.15));
            border-radius: 18px;
            padding: 24px;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .timeline-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .timeline-item:last-child {
            border-bottom: 0;
        }

        .trend-up {
            color: #22c55e;
        }

        .trend-down {
            color: #f87171;
        }

        .chart-card {
            background: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 18px;
            padding: 24px;
        }

        .search-wrapper {
            position: relative;
            min-width: 240px;
        }

        .search-wrapper input {
            background: #0f172a;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            padding: 12px 40px 12px 16px;
            color: #f9fafb;
        }

        .search-wrapper .icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(148, 163, 184, 0.7);
        }

        @media (max-width: 992px) {
            .countdown-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 576px) {
            .countdown-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .cheque-card {
            background: linear-gradient(
                180deg,
                color-mix(in srgb, var(--card-bg) 96%, rgba(248, 250, 252, 0.94)) 0%,
                color-mix(in srgb, var(--card-bg) 90%, rgba(56, 189, 248, 0.08)) 100%
            );
            border: 1px solid color-mix(in srgb, var(--border) 55%, transparent);
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 20px 36px -24px rgba(15, 23, 42, 0.18);
            color: var(--text-primary);
            position: relative;
            overflow: hidden;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        [data-bs-theme='dark'] body .cheque-card {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.92) 0%, rgba(15, 23, 42, 0.78) 100%);
            border: 1px solid rgba(99, 102, 241, 0.25);
            box-shadow: 0 20px 40px rgba(8, 15, 35, 0.45);
            color: #f8fafc;
        }

        .cheque-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.22), transparent 45%);
            pointer-events: none;
        }

        .cheque-card .cheque-title {
            font-size: 20px;
            font-weight: 600;
        }

        .cheque-card .cheque-meta {
            font-size: 13px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.75);
        }

        .cheque-card input[type="text"] {
            background: var(--input-bg);
            border: 1px solid color-mix(in srgb, var(--border) 60%, transparent);
            border-radius: 12px;
            padding: 10px 14px;
            color: var(--text-primary);
        }

        .cheque-card .amount-box {
            min-width: 220px;
            text-align: right;
            font-size: 26px;
            font-weight: 700;
            color: color-mix(in srgb, #0694f9 70%, var(--text-primary) 30%);
            background: color-mix(in srgb, rgba(34, 211, 238, 0.12) 70%, var(--card-bg) 30%);
            border-radius: 14px;
            padding: 12px 18px;
            border: 1px solid color-mix(in srgb, rgba(34, 211, 238, 0.35) 60%, transparent);
        }

        .cheque-card .signature-box {
            min-width: 260px;
            text-align: center;
            border-left: 1px dashed color-mix(in srgb, var(--border) 55%, transparent);
            padding-left: 16px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .cheque-card .signature-line {
            display: block;
            margin-top: 10px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.45);
            padding-bottom: 6px;
            font-family: 'Cedarville Cursive', cursive;
            font-size: 18px;
            color: rgba(248, 250, 252, 0.8);
        }

        .cheque-card .cheque-footer {
            font-size: 12px;
            color: rgba(203, 213, 225, 0.65);
            margin-bottom: 0;
        }

        .cheque-card .date-input {
            width: 160px;
        }

        .cheque-card .cheque-row {
            margin-top: 18px;
            gap: 16px;
        }

        .cheque-card label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: color-mix(in srgb, var(--text-secondary) 70%, transparent);
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    @php
        $formatCurrency = static fn ($value) => '$' . number_format((float) $value, 2);

        $spellOutAmount = static function (float $amount, string $currency = 'USD'): string {
            $whole = (int) floor($amount);
            $fraction = (int) round(($amount - $whole) * 100);

            $formatter = null;
            if (class_exists('\NumberFormatter')) {
                try {
                    $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                } catch (\Throwable $e) {
                    $formatter = null;
                }
            }

            $wholeWords = $formatter ? ucfirst($formatter->format($whole)) : number_format($whole);
            $fractionWords = $formatter ? $formatter->format($fraction) : number_format($fraction);

            $currencyName = match (strtoupper($currency)) {
                'CAD' => 'Canadian dollars',
                'EUR' => 'euros',
                default => 'dollars',
            };

            $centName = match (strtoupper($currency)) {
                'CAD' => 'cents',
                'EUR' => 'cents',
                default => 'cents',
            };

            if ($fraction === 0) {
                return "{$wholeWords} {$currencyName} only";
            }

            return "{$wholeWords} {$currencyName} and {$fractionWords} {$centName}";
        };

        $defaultCurrency = $currencyOptions->first() ?? 'USD';
        $defaultNetPayout = (float) $defaultCalculation['net_payout'];
        $chequeNumber = str_pad((string) ($latestClubPayout->id ?? $club->id ?? 1), 6, '0', STR_PAD_LEFT);
        $chequeDate = now()->format('m/d/Y');
        $chequeWords = $spellOutAmount($defaultNetPayout, $defaultCurrency);
    @endphp
    <div class="financial-container container-fluid py-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1 fw-semibold">Club Financial Dashboard</h2>
                <p class="text-muted small mb-0">Track payouts, sponsorships, and enrollment revenue in real time.</p>
            </div>
            <div class="search-wrapper">
                <input type="search" placeholder="Search transactions..." aria-label="Search financial records">
                <i class="fas fa-search icon"></i>
            </div>
        </div>

        <div class="cheque-card mb-4" data-cheque-card data-cheque-currency="{{ strtoupper($defaultCurrency) }}">
            <div class="cheque-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <h5 class="cheque-title mb-0">
                    <i class="fas fa-trophy me-2 text-warning"></i>Play2Earn Sports
                </h5>
                <div class="cheque-meta d-flex flex-wrap align-items-center gap-2">
                    <span>Cheque No:</span>
                    <span class="text-light" data-cheque-number>{{ $chequeNumber }}</span>
                    <span class="mx-2 d-none d-lg-inline">|</span>
                    <span>Date:</span>
                    <input type="text" class="date-input form-control form-control-sm bg-transparent text-light"
                        value="{{ $chequeDate }}" data-cheque-date>
                </div>
            </div>

            <div class="cheque-row d-flex flex-column flex-lg-row align-items-lg-center">
                <label class="me-lg-2">Pay to the order of</label>
                <input type="text" class="form-control flex-grow-1" value="{{ $club->name }}" data-cheque-payee>
                <div class="amount-box mt-3 mt-lg-0" data-cheque-amount-value>
                    {{ $formatCurrency($defaultNetPayout) }}
                </div>
            </div>

            <div class="cheque-row d-flex flex-column flex-lg-row align-items-lg-center">
                <label class="me-lg-2">Amount in words</label>
                <input type="text" class="form-control flex-grow-1" value="{{ $chequeWords }}"
                    data-cheque-amount-words readonly>
            </div>

            <div class="cheque-row d-flex flex-column flex-lg-row align-items-lg-center">
                <label class="me-lg-2">Memo</label>
                <input type="text" class="form-control flex-grow-1" placeholder="[ Bonus / Tournament Payout ]"
                    data-cheque-memo>
                <div class="signature-box mt-3 mt-lg-0">
                    <span class="text-uppercase small d-block">Authorized Signature</span>
                    <span class="signature-line">
                        {{ auth()->user()->name ?? 'Club Admin' }}
                    </span>
                </div>
            </div>

            <p class="cheque-footer mt-4">
                Bank of Play2Earn • Routing: 123456789 • Account: 987654321 &nbsp;|&nbsp;
                This cheque auto-updates as players enroll.
            </p>
        </div>

        <div class="financial-card mb-4" data-countdown-root data-target="{{ $payoutCountdown['target_iso'] }}">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between mb-3 gap-3">
                <div>
                    <div class="text-uppercase text-xs fw-semibold text-secondary">Payout Countdown</div>
                    <h4 class="mb-1">{{ $payoutCountdown['label'] }}</h4>
                    <p class="text-muted mb-0 small">
                        @if ($payoutCountdown['is_complete'])
                            Latest cycle complete. Review final payouts below.
                        @elseif ($club->isInOnboardingPeriod())
                            Tip: Turn this into a component and expose the five numbers as properties for a live countdown.
                        @else
                            Your next payout is approaching. Confirm banking details to avoid delays.
                        @endif
                    </p>
                </div>
                <div class="text-muted small text-lg-end">
                    Players active: <strong>{{ $playerCount }}</strong>
                    @if($acceptedInviteCount > $registeredPlayerCount)
                        <span class="badge bg-secondary bg-opacity-50 text-uppercase ms-2">
                            Accepted invites: {{ $acceptedInviteCount }}
                        </span>
                    @endif
                    · Estimated gross:
                    <strong>{{ $formatCurrency($defaultCalculation['gross_payout']) }}</strong>
                </div>
            </div>

            <div class="countdown-grid" data-countdown-values>
                <div class="countdown-card purple">
                    <span class="value" data-unit="weeks">{{ str_pad($payoutCountdown['weeks'], 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="label">Weeks</span>
                </div>
                <div class="countdown-card orange">
                    <span class="value" data-unit="days">{{ str_pad($payoutCountdown['days'], 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="label">Days</span>
                </div>
                <div class="countdown-card blue">
                    <span class="value" data-unit="hours">{{ str_pad($payoutCountdown['hours'], 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="label">Hours</span>
                </div>
                <div class="countdown-card pink">
                    <span class="value" data-unit="minutes">{{ str_pad($payoutCountdown['minutes'], 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="label">Minutes</span>
                </div>
                <div class="countdown-card green">
                    <span class="value" data-unit="seconds">{{ str_pad($payoutCountdown['seconds'], 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="label">Seconds</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="financial-card payout-calculator" data-calculation='@json($defaultCalculation)' data-platform-fee="{{ $platformFeePercent }}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success bg-opacity-25 text-success fw-semibold">Payout Calculator</span>
                            <span class="text-muted text-xs">Club admin · single payout per player</span>
                        </div>
                        <div class="text-muted small">
                            Current plan: {{ $defaultCalculation['plan_player_count'] }} players
                            @if ($defaultCalculation['is_projected'])
                                <span class="badge bg-warning text-dark ms-2">Projected</span>
                            @endif
                        </div>
                    </div>

                    <form id="financialCalculator" class="row g-3 align-items-end" autocomplete="off">
                        @csrf
                        <div class="col-md-6">
                            <label for="playerCountInput" class="form-label text-uppercase text-xs">Number of players</label>
                            <input type="number" min="0" step="1" class="form-control" name="player_count"
                                id="playerCountInput" value="{{ $defaultCalculation['player_count'] }}">
                        </div>
                        <div class="col-md-6">
                            <label for="currencySelect" class="form-label text-uppercase text-xs">Currency</label>
                            <select class="form-select" id="currencySelect" name="currency">
                                @foreach ($currencyOptions as $currency)
                                    <option value="{{ $currency }}">{{ strtoupper($currency) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($payoutPlans->take(6) as $plan)
                                    <button type="button" class="btn btn-quick {{ $plan->player_count === $defaultCalculation['player_count'] ? 'active' : '' }}"
                                        data-player-count="{{ $plan->player_count }}">
                                        {{ $plan->player_count }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-lg-8 financial-actions">
                            <button type="submit" class="btn btn-success w-100">Calculate</button>
                        </div>
                        <div class="col-lg-4 financial-actions d-flex gap-2">
                            <button type="button" class="btn btn-outline-light flex-fill" id="copyResultBtn">Copy result</button>
                            <button type="button" class="btn btn-outline-light flex-fill" id="shareResultBtn">Share to chat</button>
                        </div>
                    </form>

                    <div class="summary-card mt-4" id="calculationSummary">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div>
                                <div class="summary-label">Net payout to club</div>
                                <div class="summary-value" data-summary="net">
                                    {{ $formatCurrency($defaultCalculation['net_payout']) }}
                                </div>
                                <div class="summary-subtext" data-summary="gross">
                                    Gross payout ({{ $defaultCalculation['player_count'] }}
                                    × {{ $formatCurrency($defaultCalculation['per_player_rate']) }})
                                    = {{ $formatCurrency($defaultCalculation['gross_payout']) }}
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <a id="exportCsvButton" class="btn btn-outline-light"
                                    href="{{ route('club.financial.dashboard.export', ['player_count' => $defaultCalculation['player_count'], 'currency' => $currencyOptions->first()]) }}">
                                    Export CSV
                                </a>
                                <div class="text-muted small" data-summary="plan">
                                    Based on plan for {{ $defaultCalculation['plan_player_count'] }} players.
                                    Platform fee: {{ $platformFeePercent }}%.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="financial-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Financial Snapshot</h5>
                        <span class="badge bg-secondary bg-opacity-25 text-secondary">Live</span>
                    </div>

                    <div class="timeline-item">
                        <span class="summary-label">Estimated payout (gross)</span>
                        <span class="fw-semibold">{{ $formatCurrency($defaultCalculation['gross_payout']) }}</span>
                    </div>
                    <div class="timeline-item">
                        <span class="summary-label">Estimated payout (net)</span>
                        <span class="fw-semibold text-success">{{ $formatCurrency($defaultCalculation['net_payout']) }}</span>
                    </div>
                    <div class="timeline-item">
                        <span class="summary-label">Latest club payout</span>
                        <span class="fw-semibold">
                            @if ($latestClubPayout)
                                {{ $formatCurrency($latestClubPayoutAmount) }}
                                <span class="text-muted small d-block">
                                    {{ $latestClubPayout->created_at?->format('M j, Y') }}
                                </span>
                            @else
                                <span class="text-muted">No payouts yet</span>
                            @endif
                        </span>
                    </div>
                    <div class="timeline-item">
                        <span class="summary-label">Total club payouts</span>
                        <span class="fw-semibold">{{ $formatCurrency($totalClubPayouts) }}</span>
                    </div>
                    <div class="timeline-item">
                        <span class="summary-label">Player payments processed</span>
                        <span class="fw-semibold">{{ $formatCurrency($playerPaymentsTotal) }}</span>
                    </div>
                    <div class="timeline-item border-0">
                        <span class="summary-label">Donations ({{ $donationCount }})</span>
                        <span class="fw-semibold">{{ $formatCurrency($donationTotal) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-xl-7">
                <div class="sponsorship-card" data-countdown-root data-target="{{ $sponsorshipCountdown['target_iso'] }}">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                        <div>
                            <div class="text-uppercase text-xs fw-semibold text-secondary">Sponsorship payout</div>
                            <h4 class="mb-1">{{ $sponsorshipCountdown['label'] }}</h4>
                            <p class="text-muted small mb-0">Next release in
                                <span data-unit="weeks">{{ str_pad($sponsorshipCountdown['weeks'], 2, '0', STR_PAD_LEFT) }}</span>w
                                <span data-unit="days">{{ str_pad($sponsorshipCountdown['days'], 2, '0', STR_PAD_LEFT) }}</span>d.
                            </p>
                        </div>
                        <button class="btn btn-outline-light border-0 px-3 text-uppercase small align-self-start">
                            End in 14 Days
                        </button>
                    </div>

                    <div class="countdown-grid" data-countdown-values>
                        <div class="countdown-card purple">
                            <span class="value" data-unit="months">00</span>
                            <span class="label">Months</span>
                        </div>
                        <div class="countdown-card orange">
                            <span class="value" data-unit="weeks">{{ str_pad($sponsorshipCountdown['weeks'], 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="label">Weeks</span>
                        </div>
                        <div class="countdown-card blue">
                            <span class="value" data-unit="days">{{ str_pad($sponsorshipCountdown['days'], 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="label">Days</span>
                        </div>
                        <div class="countdown-card pink">
                            <span class="value" data-unit="hours">{{ str_pad($sponsorshipCountdown['hours'], 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="label">Hours</span>
                        </div>
                        <div class="countdown-card green">
                            <span class="value" data-unit="minutes">{{ str_pad($sponsorshipCountdown['minutes'], 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="label">Minutes</span>
                        </div>
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        Tip: Turn this into a component and expose the five numbers as properties for a live countdown.
                    </p>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="financial-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent activity</h5>
                        <span class="text-muted text-xs">Past 30 days</span>
                    </div>
                    <div class="mb-3">
                        <div class="summary-label mb-1">Latest donations</div>
                        @if ($recentDonations->isEmpty())
                            <div class="text-muted small">No donations recorded yet.</div>
                        @else
                            <ul class="list-unstyled mb-0">
                                @foreach ($recentDonations as $donation)
                                    <li class="d-flex justify-content-between small py-1 border-bottom border-secondary border-opacity-10">
                                        <span>{{ $donation->donor_name ?? 'Anonymous' }}</span>
                                        <span>{{ $donation->formatted_amount }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div>
                        <div class="summary-label mb-1">Next payout steps</div>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Confirm enrollment roster before deadline.</li>
                            <li>Upload sponsor agreements for validation.</li>
                            <li>Ensure payouts profile is up to date.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1">Enrollment & payout trend</h5>
                    <p class="text-muted small mb-0">Comparing player payments against club payouts for the last six months.</p>
                </div>
                <div class="badge bg-secondary bg-opacity-25 text-secondary text-uppercase">Updated {{ now()->format('M j, Y') }}</div>
            </div>
            <canvas id="financialTrendChart" height="140"></canvas>
        </div>
    </div>
@endsection

@section('footer_scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const calculateUrl = @json(route('club.financial.dashboard.calculate'));
        const exportBaseUrl = @json(route('club.financial.dashboard.export'));

        const numberFormatter = (currency) => new Intl.NumberFormat(undefined, {
            style: 'currency',
            currency: currency || 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        function updateCountdown(root) {
            const targetIso = root.dataset.target;
            if (!targetIso) {
                return;
            }

            const valueNodes = root.querySelectorAll('[data-unit]');

            const tick = () => {
                const targetDate = new Date(targetIso);
                const now = new Date();
                let diff = targetDate.getTime() - now.getTime();

                if (diff <= 0) {
                    valueNodes.forEach((node) => node.textContent = '00');
                    return;
                }

                const seconds = Math.floor(diff / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);

                const weeks = Math.floor(days / 7);
                const remainingDays = days % 7;
                const remainingHours = hours % 24;
                const remainingMinutes = minutes % 60;
                const remainingSeconds = seconds % 60;
                const months = Math.floor(days / 30);

                valueNodes.forEach((node) => {
                    const unit = node.dataset.unit;
                    let value = '00';

                    switch (unit) {
                        case 'months':
                            value = months.toString().padStart(2, '0');
                            break;
                        case 'weeks':
                            value = weeks.toString().padStart(2, '0');
                            break;
                        case 'days':
                            value = remainingDays.toString().padStart(2, '0');
                            break;
                        case 'hours':
                            value = remainingHours.toString().padStart(2, '0');
                            break;
                        case 'minutes':
                            value = remainingMinutes.toString().padStart(2, '0');
                            break;
                        case 'seconds':
                            value = remainingSeconds.toString().padStart(2, '0');
                            break;
                    }

                    node.textContent = value;
                });
            };

            tick();
            setInterval(tick, 1000);
        }

        document.querySelectorAll('[data-countdown-root]').forEach((root) => {
            const valuesContainer = root.querySelector('[data-countdown-values]');
            const countdownRoot = valuesContainer || root;
            countdownRoot.dataset.target = root.dataset.target;
            updateCountdown(countdownRoot);
        });

        const BELOW_TWENTY = [
            'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
            'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        ];
        const TENS_WORDS = [
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        ];
        const SCALE_WORDS = ['', 'thousand', 'million', 'billion'];

        const capitalizeWords = (phrase) => phrase.replace(/\b\w/g, (char) => char.toUpperCase());

        const threeDigitToWords = (num) => {
            let words = [];
            const hundred = Math.floor(num / 100);
            const remainder = num % 100;

            if (hundred > 0) {
                words.push(BELOW_TWENTY[hundred], 'hundred');
            }

            if (remainder > 0) {
                if (remainder < 20) {
                    words.push(BELOW_TWENTY[remainder]);
                } else {
                    const tens = Math.floor(remainder / 10);
                    const ones = remainder % 10;
                    words.push(TENS_WORDS[tens]);
                    if (ones > 0) {
                        words.push(BELOW_TWENTY[ones]);
                    }
                }
            }

            return words.join(' ');
        };

        const integerToWords = (num) => {
            if (num === 0) {
                return 'zero';
            }

            let words = [];
            let scaleIndex = 0;

            while (num > 0) {
                const chunk = num % 1000;
                if (chunk > 0) {
                    const chunkWords = threeDigitToWords(chunk);
                    const scaleWord = SCALE_WORDS[scaleIndex] || '';
                    words.unshift(scaleWord ? `${chunkWords} ${scaleWord}` : chunkWords);
                }
                num = Math.floor(num / 1000);
                scaleIndex += 1;
            }

            return words.join(' ').trim();
        };

        const spellCurrencyWords = (amount, currencyCode) => {
            const whole = Math.floor(amount);
            const cents = Math.round((amount - whole) * 100);
            const currencyName = ({
                'USD': 'dollars',
                'CAD': 'Canadian dollars',
                'EUR': 'euros'
            }[currencyCode.toUpperCase()] || 'dollars');
            const centName = currencyCode.toUpperCase() === 'EUR' ? 'cents' : 'cents';

            const wholeWords = capitalizeWords(integerToWords(whole));
            const centWords = capitalizeWords(integerToWords(cents));

            if (cents === 0) {
                return `${wholeWords} ${currencyName} only`;
            }

            return `${wholeWords} ${currencyName} and ${centWords} ${centName}`;
        };

        const chequeCard = document.querySelector('[data-cheque-card]');
        const chequeAmountValue = chequeCard?.querySelector('[data-cheque-amount-value]');
        const chequeAmountWordsInput = chequeCard?.querySelector('[data-cheque-amount-words]');
        let chequeCurrency = chequeCard?.dataset.chequeCurrency || 'USD';

        const updateCheque = (data, currency) => {
            if (!chequeCard) {
                return;
            }

            chequeCurrency = (currency || chequeCurrency || 'USD').toUpperCase();
            const formatter = numberFormatter(chequeCurrency);

            if (chequeAmountValue) {
                chequeAmountValue.textContent = formatter.format(data.net_payout ?? 0);
            }
            if (chequeAmountWordsInput) {
                chequeAmountWordsInput.value = spellCurrencyWords(data.net_payout ?? 0, chequeCurrency);
            }
        };

        const calculatorForm = document.getElementById('financialCalculator');
        const summaryRoot = document.getElementById('calculationSummary');
        const quickButtons = document.querySelectorAll('.btn-quick');
        const playerInput = document.getElementById('playerCountInput');
        const currencySelect = document.getElementById('currencySelect');
        const copyBtn = document.getElementById('copyResultBtn');
        const shareBtn = document.getElementById('shareResultBtn');
        const exportBtn = document.getElementById('exportCsvButton');
        const summaryData = document.querySelector('.payout-calculator')?.dataset?.calculation;

        const setActiveButton = (count) => {
            quickButtons.forEach((btn) => {
                btn.classList.toggle('active', parseInt(btn.dataset.playerCount, 10) === count);
            });
        };

        quickButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const count = parseInt(btn.dataset.playerCount, 10);
                playerInput.value = count;
                setActiveButton(count);
                if (calculatorForm) {
                    calculatorForm.requestSubmit();
                }
            });
        });

        if (currencySelect) {
            currencySelect.addEventListener('change', () => {
                const stored = summaryRoot?.dataset?.lastResult
                    ? JSON.parse(summaryRoot.dataset.lastResult)
                    : (summaryData ? JSON.parse(summaryData) : null);
                if (!stored) {
                    return;
                }
                stored.currency = currencySelect.value || stored.currency || "USD";
                updateSummary(stored, stored.currency);
            });
        }

        const updateSummary = (data, currency) => {
            if (!summaryRoot) {
                return;
            }

            const enriched = {...data, currency};
            const formatter = numberFormatter(enriched.currency);
            summaryRoot.querySelector('[data-summary="net"]').textContent = formatter.format(enriched.net_payout);
            summaryRoot.querySelector('[data-summary="gross"]').textContent =
                `Gross payout (${enriched.player_count} × ${formatter.format(enriched.per_player_rate)}) = ${formatter.format(enriched.gross_payout)}`;

            summaryRoot.querySelector('[data-summary="plan"]').textContent =
                `Based on plan for ${enriched.plan_player_count} players. Platform fee: ${enriched.platform_fee_percent}%.`;

            const query = new URLSearchParams({
                player_count: enriched.player_count,
                currency: enriched.currency
            });
            if (exportBtn) {
                exportBtn.setAttribute('href', `${exportBaseUrl}?${query.toString()}`);
            }
            summaryRoot.dataset.lastResult = JSON.stringify(enriched);
            updateCheque(enriched, enriched.currency);
        };

        if (calculatorForm) {
            calculatorForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const playerCount = parseInt(playerInput.value, 10) || 0;
                const currency = currencySelect.value || 'USD';

                fetch(calculateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        player_count: playerCount,
                        currency: currency
                    })
                })
                    .then((response) => response.json())
                    .then(({data}) => {
                        updateSummary(data, currency);
                        setActiveButton(playerCount);
                    })
                    .catch(() => {
                        alert('Unable to calculate payout right now. Please try again.');
                    });
            });
        }

        const baseCalculation = document.querySelector('.payout-calculator')?.dataset?.calculation;
        if (baseCalculation) {
            const initial = JSON.parse(baseCalculation);
            initial.currency = currencySelect.value || 'USD';
            updateSummary(initial, initial.currency);
        }

        const buildResultText = (data, currency) => {
            const formatter = numberFormatter(currency);
            return [
                `Players: ${data.player_count}`,
                `Plan: ${data.plan_player_count}`,
                `Gross: ${formatter.format(data.gross_payout)}`,
                `Fee (${data.platform_fee_percent}%): ${formatter.format(data.platform_fee_amount)}`,
                `Net: ${formatter.format(data.net_payout)}`
            ].join('\n');
        };

        if (copyBtn) {
            copyBtn.addEventListener('click', () => {
                const calc = summaryRoot.dataset.lastResult ? JSON.parse(summaryRoot.dataset.lastResult) : JSON.parse(summaryData || '{}');
                navigator.clipboard.writeText(buildResultText(calc, calc.currency || currencySelect.value))
                    .then(() => copyBtn.textContent = 'Copied!')
                    .catch(() => copyBtn.textContent = 'Copy failed');
                setTimeout(() => copyBtn.textContent = 'Copy result', 2000);
            });
        }

        if (shareBtn) {
            shareBtn.addEventListener('click', () => {
                const calc = summaryRoot.dataset.lastResult ? JSON.parse(summaryRoot.dataset.lastResult) : JSON.parse(summaryData || '{}');
                const text = encodeURIComponent(buildResultText(calc, calc.currency || currencySelect.value));
                const url = `mailto:?subject=Club Payout Update&body=${text}`;
                window.location.href = url;
            });
        }

        const chartContext = document.getElementById('financialTrendChart');
        if (chartContext) {
            const chartData = @json($monthlyRevenue);
            const formatter = numberFormatter(currencySelect.value);

            new Chart(chartContext, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Player payments',
                            data: chartData.playerPayments,
                            borderColor: '#38bdf8',
                            backgroundColor: 'rgba(56, 189, 248, 0.15)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Club payouts',
                            data: chartData.clubPayouts,
                            borderColor: '#a855f7',
                            backgroundColor: 'rgba(168, 85, 247, 0.15)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#cbd5f5'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const value = context.parsed.y || 0;
                                    return `${context.dataset.label}: ${formatter.format(value)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {color: '#9ca3af'}
                        },
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                callback(value) {
                                    return formatter.format(value);
                                }
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endsection

@extends('layouts.player-new')

@section('title', 'Referral Earnings')
@section('page-title', 'Referral Earnings')

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Allura&display=swap">

<style>
    /* Cards follow Bootstrap tokens */
.card {
  background: var(--bs-card-bg);
  color: var(--bs-card-color);
  border: 1px solid var(--bs-border-color-translucent);
}

/* Table header + borders */
.referral-summary .table thead {
  background: var(--bs-primary-bg-subtle);
}
.table thead th {
  color: var(--bs-emphasis-color);
  border-bottom-color: var(--bs-border-color);
}

/* Headings inside cards */
.card h5 {
  color: var(--bs-emphasis-color);
  border-bottom: 1px solid var(--bs-border-color);
  padding-bottom: .25rem;
  margin-bottom: .5rem;
}

/* Table body + hover that adapts to theme */
.table {
  --bs-table-color: var(--bs-body-color);
  --bs-table-bg: transparent;
  --bs-table-hover-bg: color-mix(in oklab, var(--bs-emphasis-color) 6%, transparent);
  --bs-table-border-color: var(--bs-border-color);
}

/* Muted text should always use the theme token */
.text-muted { color: var(--bs-secondary-color) !important; }

/* Subtle per-theme shadows for cards */
[data-bs-theme="dark"] .card { box-shadow: 0 4px 20px rgba(0,0,0,.25); }
/* Fallback when the data-bs-theme attribute is missing (assume dark) */
:root:not([data-bs-theme]) .card,
body:not([data-bs-theme]) .card { box-shadow: 0 4px 20px rgba(0,0,0,.25); }

[data-bs-theme="light"] .card { box-shadow: 0 4px 16px rgba(0,0,0,.05); }

/* Wrapper inherits theme body color */
.referral-wrapper {
  margin: 0 auto;
  padding-bottom: 80px;
  color: var(--bs-body-color);
}

/* ================= Status Pills ================= */

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: .375rem;
  padding: .375rem .75rem;
  border-radius: 999px;
  font-size: .85rem;
  font-weight: 600;
  border: 1px solid var(--bs-border-color);
  background-color: var(--bs-secondary-bg);
  color: var(--bs-body-color);
}
.status-pill--success {
  background-color: var(--bs-success-bg-subtle);
  color: var(--bs-success-text-emphasis);
  border-color: color-mix(in oklab, var(--bs-success) 35%, var(--bs-border-color));
}
/* Blue "in progress" feel; swap to --bs-warning-* if you want yellow */
.status-pill--pending {
  background-color: var(--bs-info-bg-subtle);
  color: var(--bs-info-text-emphasis);
  border-color: color-mix(in oklab, var(--bs-info) 35%, var(--bs-border-color));
}

/* ================= Cheque / “Check Card” ================= */

/* Dark-friendly default look */
.check-card {
  background: linear-gradient(135deg, rgba(17, 24, 39, 0.95), rgba(30, 64, 175, 0.78));
  border-radius: 32px;
  overflow: hidden;
  box-shadow: 0 30px 80px rgba(15, 23, 42, 0.4);
  color: #f8fafc;
}
.check-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 32px 36px;
  background: rgba(37, 99, 235, 0.85);
}
.check-card__header img { height: 64px; }
.check-card__body { padding: 36px; background: rgba(13, 22, 45, 0.88); }

.check-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 20px;
}
.check-field {
  background: rgba(8, 15, 35, 0.75);
  border-radius: 18px;
  padding: 16px 20px;
  min-height: 70px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  border: 1px solid rgba(148, 163, 184, 0.2);
}
.check-field--minimal {
  border: none;
  min-width: 200px;
  background: color-mix(in srgb, var(--bs-body-bg) 35%, rgba(15, 23, 42, 0.45));
}
[data-bs-theme="light"] .check-field--minimal {
  background: color-mix(in srgb, var(--bs-secondary-bg) 85%, #ffffff);
}
.check-field__value--script {
  font-family: 'Allura', cursive;
  font-size: 1.25rem;
  opacity: 0.85;
}
.check-field__label {
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-size: 0.75rem;
  color: rgba(148, 163, 184, 0.8);
  margin-bottom: 6px;
}
.check-field__value { font-size: 1.1rem; font-weight: 600; }
.check-signature {
  margin-top: 36px;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 18px;
  color: rgba(148, 163, 184, 0.8);
}
.check-signature__line { width: 240px; height: 2px; background: rgba(148, 163, 184, 0.45); }

/* ================= Light overrides ================= */

[data-bs-theme="light"] .check-card {
  background: linear-gradient(135deg, #f8fafc, #e8eefc);
  color: var(--bs-emphasis-color);
  box-shadow: 0 24px 60px rgba(0,0,0,.06);
}
[data-bs-theme="light"] .check-card__header {
  background: var(--bs-primary);
  color: var(--bs-primary-contrast, #fff);
}
[data-bs-theme="light"] .check-card__body { background: #ffffff; }
[data-bs-theme="light"] .check-field {
  background: var(--bs-secondary-bg);
  border: 1px solid var(--bs-border-color);
}
[data-bs-theme="light"] .check-field--minimal {
  border: none;
}
[data-bs-theme="light"] .check-field__label { color: var(--bs-secondary-color); }

/* ================= Referral Summary ================= */

.referral-summary { margin-top: 48px; }
.referral-summary .card {
  border-radius: 24px;
  border: 1px solid rgba(148, 163, 184, 0.2);
  box-shadow: 0 22px 60px rgba(15, 23, 42, 0.12);
}
.referral-summary .table { margin-bottom: 0; }
.referral-summary .table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  border-top: none;
}

/* ================= Responsive ================= */
@media (max-width: 767.98px) {
  .check-card__body { padding: 24px; }
  .check-row { grid-template-columns: 1fr; }
}
/* Replace these global rules with scoped ones */

/* was: thead th { ... } */
.payout-container thead th { background-color: var(--bg-tertiary); }

/* was: table { width:95%; margin:0 auto } */
.payout-container table { width:95%; margin:0 auto; }

/* was: .table { color: var(--text-primary) !important; } */
.payout-container .table { color: var(--text-primary); }

/* Borders disabled everywhere -> scope it */
.payout-container .table> :not(caption)>*>* { border-bottom-width: 0; border-top-width: 0; }

</style>

@section('content')
    <div class="referral-wrapper">
        <div class="check-card mb-5">
            <div class="check-card__header">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('assets/player-dashboard/images/logo.png') }}" alt="Play2Earn Logo">
                    <div>
                        <div class="fw-semibold">Play2Earn Sports Cheque</div>
                        <div class="small text-body-secondary fw-medium">Cheque No: {{ $chequeNumber }}</div>
                    </div>
                </div>
                <div class="check-field check-field--minimal">
                    <div class="check-field__label">Date</div>
                    <div class="check-field__value">{{ $chequeDate }}</div>
                </div>
            </div>

            <div class="check-card__body">
                <div class="check-row">
                    <div class="check-field">
                        <div class="check-field__label">Pay to the order of</div>
                        <div class="check-field__value">{{ $user->name }}</div>
                    </div>
                    <div class="check-field">
                        <div class="check-field__label">Amount</div>
                        <div class="check-field__value">{{ $amountFormatted }}</div>
                    </div>
                    <div class="check-field">
                        <div class="check-field__label">Referral earnings</div>
                        <div class="check-field__value">Players: ${{ number_format($playerEarnings, 2) }} | Clubs:
                            ${{ number_format($clubEarnings, 2) }}</div>
                    </div>
                </div>

                <div class="check-field mb-4">
                    <div class="check-field__label">Amount in words</div>
                    <div class="check-field__value check-field__value--script">
                        {{ $amountWords }}</div>
                </div>

                <div class="check-field">
                    <div class="check-field__label">Memo</div>
                    <div class="check-field__value">Referral &amp; activation rewards</div>
                </div>

                <div class="check-signature">
                    <span>Authorized Signature</span>
                    <div class="check-signature__line"></div>
                    <span>[ Signature ]</span>
                </div>
            </div>
        </div>

        <div class="referral-summary">
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-1">Player referrals</h5>
                        <p class="mb-0 text-muted">{{ $playerInvites->count() }} invites sent • {{ $playerAccepted }}
                            verified signups • ${{ number_format($playerEarnings, 2) }} earned</p>
                    </div>
                    <a href="{{ route('player.invite.create') }}" class="btn btn-primary">Invite more players</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Club activation tracker</h5>
                        <a href="{{ route('player.invite.club.create') }}" class="btn btn-outline-primary btn-sm">Refer a
                            club</a>
                    </div>

                    @if ($clubStats->isEmpty())
                        <p class="text-muted mb-0">You haven't referred any clubs yet. Invite a club and earn $1,000 when
                            they activate 200 players.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Club</th>
                                        <th>League / Location</th>
                                        <th>Contacts</th>
                                        <th class="text-end">Players registered</th>
                                        <th class="text-end">Payout earned</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clubStats as $club)
                                        <tr>
                                            <td>{{ $club['club_name'] }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $club['league'] ?? '—' }}</div>
                                                <div class="text-muted">{{ $club['location'] ?? '—' }}</div>
                                            </td>
                                            <td>{{ $club['contacts_sent'] ?? 0 }}</td>
                                            <td class="text-end">{{ $club['players_registered'] }}</td>
                                            <td class="text-end">${{ number_format($club['payout_earned'], 2) }}</td>
                                            <td>
                                                <span
                                                    class="status-pill {{ $club['status'] === 'Qualified' ? 'status-pill--success' : 'status-pill--pending' }}">
                                                    {{ $club['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')
@section('title', 'Referral Payments')
@php
    $totals          = $totals          ?? (object)['total_invites'=>0,'total_club_payout'=>0,'total_player_payout'=>0];
    $playersReady    = $playersReady    ?? collect();
    $playersUpcoming = $playersUpcoming ?? collect();
    $clubsReady      = $clubsReady      ?? collect();
    $clubsUpcoming   = $clubsUpcoming   ?? collect();
    $paid            = $paid            ?? collect();
@endphp

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Referral Payments</h2>
  </div>

  {{-- ===== Badges / KPIs ===== --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card card-body text-dark kpi-box">
        <h6 class="mb-1">Total Invites (accepted)</h6>
        <div class="h4 mb-0">{{ number_format($totals->total_invites ?? 0) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-body text-dark kpi-box">
        <h6 class="mb-1">Total Club Payout (@ $1,000)</h6>
        <div class="h4 mb-0">${{ number_format($totals->total_club_payout ?? 0, 2) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-body text-dark kpi-box">
        <h6 class="mb-1">Total Player Payout (@ $10)</h6>
        <div class="h4 mb-0">${{ number_format($totals->total_player_payout ?? 0, 2) }}</div>
      </div>
    </div>
  </div>

  {{-- ===== 1) Players — Ready to Pay ===== --}}
  <div class="card mb-4">
    <div class="card-header"><strong>Players — Ready to Pay (≥ 90 days)</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Player</th>
            <th>Club Referrals</th>
            <th>Player Referrals</th>
            <th>Total Amount</th>
            <th>PayPal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($playersReady as $row)
            <tr>
              <td>{{ $row->name }}</td>
              <td>{{ (int)$row->club_referrals }}</td>
              <td>{{ (int)$row->player_referrals }}</td>
              <td class="fw-bold">${{ number_format($row->total_amount, 2) }}</td>
              <td class="text-muted">{{ $row->paypal_link ?: '—' }}</td>
              <td>
                <button class="btn btn-sm btn-primary"
                        data-pay
                        data-type="player"
                        data-action="{{ route('admin.payments.pay.player', $row->id) }}"
                        @if(($row->total_amount ?? 0) <= 0 || empty($row->paypal_link)) disabled @endif>
                  Pay Now
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No player payouts ready.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===== 2) Players — Upcoming (< 90 days) ===== --}}
  <div class="card mb-4">
    <div class="card-header"><strong>Players — Upcoming (< 90 days)</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Player</th>
            <th>Club Referrals (in-progress)</th>
            <th>Player Referrals (in-progress)</th>
            <th>Soonest Payout In</th>
          </tr>
        </thead>
        <tbody>
          @forelse($playersUpcoming as $row)
            <tr>
              <td>{{ $row->name }}</td>
              <td>{{ (int)$row->club_referrals }}</td>
              <td>{{ (int)$row->player_referrals }}</td>
              <td>
                @php $d = (int)$row->min_days_left; @endphp
                @if($d <= 0)
                  today
                @elseif($d === 1)
                  1 day
                @else
                  {{ $d }} days
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No upcoming player payouts.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===== 3) Clubs — Ready to Pay ===== --}}
  <div class="card mb-4">
    <div class="card-header"><strong>Clubs — Ready to Pay (≥ 90 days)</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Club</th>
            <th>Club Referrals</th>
            <th>Player Referrals</th>
            <th>Total Amount</th>
            <th>PayPal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clubsReady as $row)
            <tr>
              <td>{{ $row->name }}</td>
              <td>{{ (int)$row->club_referrals }}</td>
              <td>{{ (int)$row->player_referrals }}</td>
              <td class="fw-bold">${{ number_format($row->total_amount, 2) }}</td>
              <td class="text-muted">{{ $row->paypal_link ?: '—' }}</td>
              <td>
                <button class="btn btn-sm btn-primary"
                        data-pay
                        data-type="club"
                        data-action="{{ route('admin.payments.pay.club', $row->id) }}"
                        @if(($row->total_amount ?? 0) <= 0 || empty($row->paypal_link)) disabled @endif>
                  Pay Now
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No club payouts ready.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===== 4) Clubs — Upcoming (< 90 days) ===== --}}
  <div class="card mb-4">
    <div class="card-header"><strong>Clubs — Upcoming (< 90 days)</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Club</th>
            <th>Club Referrals (in-progress)</th>
            <th>Player Referrals (in-progress)</th>
            <th>Soonest Payout In</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clubsUpcoming as $row)
            <tr>
              <td>{{ $row->name }}</td>
              <td>{{ (int)$row->club_referrals }}</td>
              <td>{{ (int)$row->player_referrals }}</td>
              <td>
                @php $d = (int)$row->min_days_left; @endphp
                @if($d <= 0)
                  today
                @elseif($d === 1)
                  1 day
                @else
                  {{ $d }} days
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No upcoming club payouts.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ===== 5) Payments Made (Players & Clubs) ===== --}}
  <div class="card mb-4">
    <div class="card-header"><strong>Payments Made</strong></div>
    <div class="card-body table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Inviter Type</th>
            <th>Name</th>
            <th>Club Referrals (paid)</th>
            <th>Player Referrals (paid)</th>
            <th>Total Paid</th>
            <th>Last Paid At</th>
          </tr>
        </thead>
        <tbody>
          @forelse($paid as $row)
            <tr>
              <td class="text-capitalize">{{ $row->inviter_type }}</td>
              <td>{{ $row->inviter_name }}</td>
              <td>{{ (int)$row->club_referrals_paid }}</td>
              <td>{{ (int)$row->player_referrals_paid }}</td>
              <td class="fw-bold">${{ number_format($row->total_paid, 2) }}</td>
              <td>{{ $row->last_paid_at ? \Carbon\Carbon::parse($row->last_paid_at)->format('Y-m-d H:i') : '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No payouts recorded yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.kpi-box { background: #f8fafc; border: 1px solid #e5e7eb; }
.table td, .table th { vertical-align: middle; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-pay]');
  if (!btn) return;

  const action = btn.dataset.action;
  const type   = btn.dataset.type;

  btn.disabled = true;
  const original = btn.textContent;
  btn.textContent = 'Paying...';

  try {
    const res = await fetch(action, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: new FormData()
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok || data.ok === false) throw new Error(data.message || 'Payment failed.');

    btn.classList.remove('btn-primary');
    btn.classList.add('btn-success');
    btn.textContent = 'Paid';
  } catch (err) {
    alert(err.message || 'Payment failed');
    btn.disabled = false;
    btn.textContent = original;
  }
});
</script>
@endpush

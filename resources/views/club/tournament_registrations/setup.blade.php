@extends('layouts.club-dashboard')

@section('title', 'Tournament Registration')
@section('page_title', 'Complete Tournament Registration')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-2xl">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-handshake me-2"></i>
                        Join {{ $tournament->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        You're almost there! Confirm how many teams you'll enter and acknowledge the tournament fee to secure your place.
                    </p>

                    <div class="alert alert-secondary d-flex align-items-start">
                        <i class="fas fa-info-circle me-3 mt-1"></i>
                        <div>
                            <strong>Registration Details</strong>
                            <div>Joining type: <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $registration->joining_type) }}</span></div>
                            <div>Fee: <span class="fw-semibold">${{ number_format((float) $registration->joining_fee, 2) }}</span> {{ $registration->joining_type === 'per_team' ? 'per team' : 'per club' }}</div>
                            @if($tournament->registration_cutoff_date)
                                <div>Registration closes: {{ $tournament->registration_cutoff_date->format('M d, Y') }}</div>
                            @endif
                        </div>
                    </div>

                    <form method="POST" action="{{ route('club.tournament-registrations.setup.store', $registration) }}">
                        @csrf

                        @if($registration->joining_type === 'per_team')
                            @php
                                $defaultQuantity = old('team_quantity', $registration->team_quantity ?: 1);
                            @endphp
                            <div class="mb-3">
                                <label for="team_quantity" class="form-label fw-semibold">Number of Teams *</label>
                                <input type="number" min="1" max="50" class="form-control @error('team_quantity') is-invalid @enderror" id="team_quantity" name="team_quantity" value="{{ $defaultQuantity }}" required>
                                @error('team_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Charge will be calculated as teams × fee. You can add your teams after payment.</div>
                            </div>
                        @else
                            <input type="hidden" name="team_quantity" value="1">
                        @endif

                        @php
                            $initialTeams = $registration->joining_type === 'per_team'
                                ? (int) old('team_quantity', $registration->team_quantity ?: 1)
                                : 1;
                            $initialTotal = $initialTeams * (float) $registration->joining_fee;
                        @endphp

                        <div class="border rounded-2xl p-3 bg-light mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Estimated total</span>
                                <span class="fs-4" id="feeTotal">${{ number_format($initialTotal, 2) }}</span>
                            </div>
                            <div class="text-muted small">We'll record this fee as paid so you can continue building teams. Contact the tournament host if adjustments are needed.</div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="1" id="confirm_payment" name="confirm_payment" {{ old('confirm_payment') ? 'checked' : '' }} required>
                            <label class="form-check-label" for="confirm_payment">
                                I understand the registration fee shown above and confirm payment arrangements with the host club.
                            </label>
                            @error('confirm_payment')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('club.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Continue <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const teamInput = document.getElementById('team_quantity');
        const feeTotal = document.getElementById('feeTotal');
        const joiningFee = {{ (float) $registration->joining_fee }};

        if (teamInput) {
            const updateTotal = () => {
                const qty = Math.max(1, parseInt(teamInput.value || '1', 10));
                const total = (qty * joiningFee).toFixed(2);
                feeTotal.textContent = `$${total}`;
            };

            teamInput.addEventListener('input', updateTotal);
            updateTotal();
        }
    });
</script>
@endpush

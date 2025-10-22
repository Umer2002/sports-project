@extends('layouts.club-dashboard')
@section('title','Store Payments Settings')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Payments (Stripe)</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('club.store.settings.payments.update') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Stripe Publishable Key</label>
                        <input type="text" name="stripe_public_key" class="form-control" value="{{ old('stripe_public_key', $club->stripe_public_key) }}" placeholder="pk_live_... or pk_test_...">
                        @error('stripe_public_key')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stripe Secret Key</label>
                        <input type="text" name="stripe_secret_key" class="form-control" value="{{ old('stripe_secret_key', $club->stripe_secret_key) }}" placeholder="sk_live_... or sk_test_...">
                        @error('stripe_secret_key')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Stripe Account ID (optional)</label>
                        <input type="text" name="stripe_account_id" class="form-control" value="{{ old('stripe_account_id', $club->stripe_account_id) }}" placeholder="acct_...">
                        @error('stripe_account_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
    <div class="alert alert-info mt-3">
        <strong>Note:</strong> These keys are used to process orders for your club shop. For marketplaces with payouts to connected accounts, provide your <em>Stripe Account ID</em> and contact support to enable Stripe Connect.
    </div>
</div>
@endsection


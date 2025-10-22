@extends('layouts.default')

@section('content')
<div class="container py-5 text-center">
    <h1 class="text-success mb-4">Payment Successful!</h1>
    <p class="lead">Thank you for your payment. Your account is now complete and you have been registered successfully.</p>
    <a href="{{ route('player.dashboard') }}" class="btn btn-primary mt-4">Go to Dashboard</a>
</div>
@endsection 
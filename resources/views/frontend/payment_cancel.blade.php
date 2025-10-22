@extends('layouts.default')

@section('content')
<div class="container py-5 text-center">
    <h1 class="text-danger mb-4">Payment Cancelled</h1>
    <p class="lead">Your payment was not completed. If this was a mistake, you can try again below.</p>
    <a href="javascript:history.back()" class="btn btn-secondary mt-4">Go Back</a>
    <a href="{{ route('player.checkout') }}" class="btn btn-primary mt-4">Try Payment Again</a>
</div>
@endsection 
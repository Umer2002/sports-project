@extends('public.profiles.swimming')

@section('player_theme', 'Baseball')

@section('donation-button')
<button class="donate-btn mt-1" onclick="openDonationModal()">MAKE A DONATION</button>
@endsection

@section('player-custom-css')
<style>
    /* Baseball-specific styles */
    .hero {
        background:  linear-gradient(to bottom, rgba(41, 128, 185, 0.9) 0%, rgba(31, 36, 41, 0.8) 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    
    
</style>
@endsection

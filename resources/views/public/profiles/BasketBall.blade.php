@extends('public.profiles.swimming')

@section('player_theme', 'BasketBall')

@section('player-custom-css')
<style>
    /* Basketball-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    } */
    .hero {
        background:  linear-gradient(to bottom, #ff6b35 0%, #f7931e 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #1e3a8a;
    }
    
    .stats-value {
        color: #ff6b35;
    }
    
    .donate-btn {
        background: #ff6b35;
        border-color: #ff6b35;
    }
    
    .donate-btn:hover {
        background: #e55a2b;
        border-color: #e55a2b;
    }
    
    .soccer-text {
        color: #ff6b35;
    }
</style>
@endsection

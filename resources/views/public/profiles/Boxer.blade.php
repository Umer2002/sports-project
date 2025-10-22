@extends('public.profiles.swimming')

@section('player_theme', 'Boxer')

@section('player-custom-css')
<style>
    /* Boxing-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #7c2d12 0%, #a16207 100%);
    } */
    .hero {
        background:  linear-gradient(to bottom, #7c2d12 0%, #a16207 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #451a03;
    }
    
    .stats-value {
        color: #7c2d12;
    }
    
    .donate-btn {
        background: #7c2d12;
        border-color: #7c2d12;
    }
    
    .donate-btn:hover {
        background: #5c1d06;
        border-color: #5c1d06;
    }
    
    .soccer-text {
        color: #7c2d12;
    }
    
    .menu-bar a.active {
        background: #7c2d12;
        border-color: #7c2d12;
    }
    
    .menu-bar a:hover {
        background: #a16207;
        border-color: #a16207;
    }
</style>
@endsection

@extends('public.profiles.swimming')

@section('player_theme', 'track-and-field')

@section('player-custom-css')
<style>
    /* Track and Field-specific styles */
    .hero {
        background: linear-gradient(135deg, #be185d 0%, #ec4899 100%);
    }
    .hero {
        background:  linear-gradient(to bottom, #be185d 0%, #ec4899  100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #831843;
    }
    
    .stats-value {
        color: #be185d;
    }
    
    .donate-btn {
        background: #be185d;
        border-color: #be185d;
    }
    
    .donate-btn:hover {
        background: #9d174d;
        border-color: #9d174d;
    }
    
    .soccer-text {
        color: #be185d;
    }
    
    .menu-bar a.active {
        background: #be185d;
        border-color: #be185d;
    }
    
    .menu-bar a:hover {
        background: #ec4899;
        border-color: #ec4899;
    }
</style>
@endsection

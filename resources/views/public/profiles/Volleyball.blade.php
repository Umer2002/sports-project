@extends('public.profiles.swimming')

@section('player_theme', 'Volleyball')

@section('player-custom-css')
<style>
    /* Volleyball-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    } */
    .hero {
        background:  linear-gradient(to bottom, #dc2626 0%, #ef4444 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #991b1b;
    }
    
    .stats-value {
        color: #dc2626;
    }
    
    .donate-btn {
        background: #dc2626;
        border-color: #dc2626;
    }
    
    .donate-btn:hover {
        background: #b91c1c;
        border-color: #b91c1c;
    }
    
    .soccer-text {
        color: #dc2626;
    }
    
    .menu-bar a.active {
        background: #dc2626;
        border-color: #dc2626;
    }
    
    .menu-bar a:hover {
        background: #ef4444;
        border-color: #ef4444;
    }
</style>
@endsection

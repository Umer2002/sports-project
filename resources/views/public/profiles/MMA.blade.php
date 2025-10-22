@extends('public.profiles.swimming')

@section('player_theme', 'MMA')

@section('player-custom-css')
<style>
    /* MMA-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    } */
    .hero {
        background:  linear-gradient(to bottom, #1f2937 0%, #374151 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #111827;
    }
    
    .stats-value {
        color: #1f2937;
    }
    
    .donate-btn {
        background: #1f2937;
        border-color: #1f2937;
    }
    
    .donate-btn:hover {
        background: #111827;
        border-color: #111827;
    }
    
    .soccer-text {
        color: #1f2937;
    }
    
    .menu-bar a.active {
        background: #1f2937;
        border-color: #1f2937;
    }
    
    .menu-bar a:hover {
        background: #374151;
        border-color: #374151;
    }
</style>
@endsection

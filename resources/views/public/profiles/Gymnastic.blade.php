@extends('public.profiles.swimming')

@section('player_theme', 'Gymnastic')

@section('player-custom-css')
<style>
    /* Gymnastics-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
    } */
    .hero {
        background:  linear-gradient(to bottom, #7c3aed 0%, #a855f7 100%) ,url("{{ asset($playerAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    .player-name {
        color: #581c87;
    }
    
    .stats-value {
        color: #7c3aed;
    }
    
    .donate-btn {
        background: #7c3aed;
        border-color: #7c3aed;
    }
    
    .donate-btn:hover {
        background: #6d28d9;
        border-color: #6d28d9;
    }
    
    .soccer-text {
        color: #7c3aed;
    }
    
    .menu-bar a.active {
        background: #7c3aed;
        border-color: #7c3aed;
    }
    
    .menu-bar a:hover {
        background: #a855f7;
        border-color: #a855f7;
    }
</style>
@endsection

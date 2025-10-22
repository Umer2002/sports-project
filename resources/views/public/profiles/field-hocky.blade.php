@extends('public.profiles.swimming')

@section('player_theme', 'field-hocky')

@section('player-custom-css')
<style>
    /* Field Hockey-specific styles */
    /* .hero {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    } */
    @php($themeAssetPath = $playerAssetPath ?? 'assets/players/swimming/assets')
    .hero {
        background:  linear-gradient(to bottom, #f59e0b 0%, #fbbf24 100%) ,url("{{ asset($themeAssetPath . '/image/hero-bg.jpg') }}");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        
    }
    
    .player-name {
        color: #92400e;
    }
    
    .stats-value {
        color: #f59e0b;
    }
    
    .donate-btn {
        background: #f59e0b;
        border-color: #f59e0b;
    }
    
    .donate-btn:hover {
        background: #d97706;
        border-color: #d97706;
    }
    
    .soccer-text {
        color: #f59e0b;
    }
    
    .menu-bar a.active {
        background: #f59e0b;
        border-color: #f59e0b;
    }
    
    .menu-bar a:hover {
        background: #fbbf24;
        border-color: #fbbf24;
    }
</style>
@endsection

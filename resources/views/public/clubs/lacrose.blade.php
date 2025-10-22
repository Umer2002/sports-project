@extends('public.clubs.football')

@section('club_theme', 'lacrose')

@section('custom-theme-css')
<style>
  /* Lacrosse-specific styles */
  .hero {
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  }
  
  .player-name {
    color: #164e63;
  }
  
  .stats-value {
    color: #0891b2;
  }
  
  .donate-btn {
    background: #0891b2;
    border-color: #0891b2;
  }
  
  .donate-btn:hover {
    background: #0e7490;
    border-color: #0e7490;
  }
  
  .soccer-text {
    color: #0891b2;
  }
  
  .menu-bar a.active {
    background: #0891b2;
    border-color: #0891b2;
  }
  
  .menu-bar a:hover {
    background: #06b6d4;
    border-color: #06b6d4;
  }
</style>
@endsection

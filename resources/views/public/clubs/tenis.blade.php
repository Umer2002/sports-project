@extends('public.clubs.football')

@section('club_theme', 'tenis')

@section('custom-theme-css')
<style>
  /* Tennis-specific styles */
  .hero {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
  }

  .player-name {
    color: #064e3b;
  }

  .stats-value {
    color: #059669;
  }

  /* .donate-btn {
    background: #059669;
    border-color: #059669;
  }
  
  .donate-btn:hover {
    background: #047857;
    border-color: #047857;
  } */

  .soccer-text {
    color: #059669;
  }

  .menu-bar a.active {
    background: #059669;
    border-color: #059669;
  }

  .menu-bar a:hover {
    background: #10b981;
    border-color: #10b981;
  }
</style>
@endsection
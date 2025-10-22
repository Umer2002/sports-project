@extends('public.clubs.football')

@section('club_theme', 'wrestling')

@section('custom-theme-css')
<style>
  /* Wrestling-specific styles */
  .hero {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
  }

  .player-name {
    color: #991b1b;
  }

  .vertical-hockey-text {
    left: calc(100% - 530px);
  }

  .stats-value {
    color: #dc2626;
  }

  .donate-btn {
    /* background: #dc2626;
    border-color: #dc2626; */
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
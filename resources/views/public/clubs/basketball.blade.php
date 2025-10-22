@extends('public.clubs.football')

@section('club_theme', 'basketball')

@section('custom-theme-css')
<style>
  /* Basketball-specific overrides */
  :root {
    --basketball-primary: #f97316;
    --basketball-primary-dark: #ea580c;
    --basketball-secondary: #fb923c;
  }

  .hero {
    background: linear-gradient(135deg, var(--basketball-primary) 0%, var(--basketball-secondary) 100%);
  }

  .player-name,
  .vertical-hockey-text,
  .stats-value,
  .soccer-text {
    color: var(--basketball-primary);
  }

  .donate-btn,
  .menu-bar a.active,
  .join-club-btn {
    /* background: var(--basketball-primary); */
    border-color: var(--basketball-primary);
  }

  .donate-btn:hover,
  .menu-bar a:hover,
  .join-club-btn:hover {
    /* background: var(--basketball-primary-dark); */
    border-color: var(--basketball-primary-dark);
  }

  /* .info-card .info-card-title span,
  .club-stat-number,
  .coach-name,
  .team-name,
  .player-name-card {
     color: var(--basketball-primary); 
  } */

  .club-card .icon-circle {
    background: rgba(249, 115, 22, 0.15);
  }

  .club-card .icon-circle svg path {
    fill: var(--basketball-primary);
  }
</style>
@endsection
@extends('public.clubs.football')

@section('club_theme', 'football')

@section('social-links')
@php
  $socialLinks = [];
  if ($club->social_links) {
    if (is_string($club->social_links)) {
      $socialLinks = json_decode($club->social_links, true) ?: [];
    } elseif (is_array($club->social_links)) {
      $socialLinks = $club->social_links;
    }
  }
@endphp

@if(isset($socialLinks['facebook']) && $socialLinks['facebook'])
  <a href="{{ $socialLinks['facebook'] }}" class="social-icon facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
@endif

@if(isset($socialLinks['instagram']) && $socialLinks['instagram'])
  <a href="{{ $socialLinks['instagram'] }}" class="social-icon instagram" target="_blank"><i class="fab fa-instagram"></i></a>
@endif

@if(isset($socialLinks['tiktok']) && $socialLinks['tiktok'])
  <a href="{{ $socialLinks['tiktok'] }}" class="social-icon tiktok" target="_blank"><i class="fab fa-tiktok"></i></a>
@endif

@if(isset($socialLinks['snapchat']) && $socialLinks['snapchat'])
  <a href="{{ $socialLinks['snapchat'] }}" class="social-icon snapchat" target="_blank"><i class="fab fa-snapchat-ghost"></i></a>
@endif

@if(isset($socialLinks['pinterest']) && $socialLinks['pinterest'])
  <a href="{{ $socialLinks['pinterest'] }}" class="social-icon pinterest" target="_blank"><i class="fab fa-pinterest-p"></i></a>
@endif

@if(isset($socialLinks['linkedin']) && $socialLinks['linkedin'])
  <a href="{{ $socialLinks['linkedin'] }}" class="social-icon linkedin" target="_blank"><i class="fab fa-linkedin-in"></i></a>
@endif

@if(isset($socialLinks['reddit']) && $socialLinks['reddit'])
  <a href="{{ $socialLinks['reddit'] }}" class="social-icon reddit" target="_blank"><i class="fab fa-reddit-alien"></i></a>
@endif

@if(isset($socialLinks['twitter']) && $socialLinks['twitter'])
  <a href="{{ $socialLinks['twitter'] }}" class="social-icon twitter" target="_blank"><i class="fab fa-x-twitter"></i></a>
@endif

@if(isset($socialLinks['youtube']) && $socialLinks['youtube'])
  <a href="{{ $socialLinks['youtube'] }}" class="social-icon youtube" target="_blank"><i class="fab fa-youtube"></i></a>
@endif

@if(empty($socialLinks))
  <!-- Default social icons if no social links are set -->
  <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
  <a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
  <a href="#" class="social-icon tiktok"><i class="fab fa-tiktok"></i></a>
  <a href="#" class="social-icon snapchat"><i class="fab fa-snapchat-ghost"></i></a>
  <a href="#" class="social-icon pinterest"><i class="fab fa-pinterest-p"></i></a>
  <a href="#" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
  <a href="#" class="social-icon reddit"><i class="fab fa-reddit-alien"></i></a>
  <a href="#" class="social-icon twitter"><i class="fab fa-x-twitter"></i></a>
  <a href="#" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
@endif
@endsection

@section('custom-theme-css')
<style>
  /* Default club styles */
  .hero {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  }
  
  .player-name {
    color: #1e40af;
  }
  
  .stats-value {
    color: #3b82f6;
  }
  
  .donate-btn {
    background: #3b82f6;
    border-color: #3b82f6;
  }
  
  .donate-btn:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
  }
  
  .soccer-text {
    color: #3b82f6;
  }
  
  .menu-bar a.active {
    background: #3b82f6;
    border-color: #3b82f6;
  }
  
  .menu-bar a:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
  }
</style>
@endsection

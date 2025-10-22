@php
    $playerThemeKey = trim($__env->yieldContent('player_theme', 'swimming'));
    if ($playerThemeKey === '') {
        $playerThemeKey = 'swimming';
    }

    $includePlayerAssets = $playerThemeKey !== 'none';
    $playerAssetPath = null;

    if ($includePlayerAssets) {
        $playerAssetPath = "assets/players/{$playerThemeKey}/assets";

        if (! is_dir(public_path($playerAssetPath))) {
            $playerThemeKey = 'swimming';
            $playerAssetPath = "assets/players/{$playerThemeKey}/assets";
        }
    }

    $rawSocialLinks = $player->social_links ?? [];
    if ($rawSocialLinks instanceof \Illuminate\Support\Collection) {
        $playerSocialLinks = $rawSocialLinks->toArray();
    } elseif (is_array($rawSocialLinks)) {
        $playerSocialLinks = $rawSocialLinks;
    } elseif (is_string($rawSocialLinks) && trim($rawSocialLinks) !== '') {
        $playerSocialLinks = json_decode($rawSocialLinks, true) ?: [];
    } else {
        $playerSocialLinks = [];
    }

    view()->share('playerAssetPath', $playerAssetPath);
    view()->share('includePlayerAssets', $includePlayerAssets);
    view()->share('playerSocialLinks', $playerSocialLinks);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $player->name }} - {{ $player->sport->name ?? 'Player' }}</title>

    <!-- Base CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    @if($includePlayerAssets && $playerAssetPath)
        <link rel="stylesheet" href="{{ asset($playerAssetPath . '/css/bootstrap.min.css') }}" />
        <link rel="stylesheet" href="{{ asset($playerAssetPath . '/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset($playerAssetPath . '/css/font.css') }}" />
    @endif

    @yield('theme-css')
    
    <!-- Theme-specific CSS overrides -->
    @yield('player-custom-css')
    
    <!-- Custom styles -->
    <style>
        .menu-bar{
            padding-bottom: 0px;
            padding-top: 0px;
        }
        .stats-value{
            font-size: 38px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    @yield('header')

    <!-- Hero Section -->
    @yield('hero')

    <!-- Tab Content -->
    @yield('tabs')

    <!-- Newsletter Section -->
    @yield('newsletter')

    <!-- Footer -->
    @yield('footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Theme-specific scripts -->
    @yield('theme-scripts')
</body>
</html>

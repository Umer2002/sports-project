# Public Player Profiles System

This system allows players to have public profile pages with different themes based on their sport.

## Overview

The public profile system automatically applies different themes based on the player's sport. Each sport can have its own unique design while maintaining consistent functionality.

## How It Works

1. **Route**: `/profile/{playerId}` - Public profile page for any player
2. **Theme Selection**: Automatically determined by the player's sport
3. **Dynamic Content**: All player data is dynamically loaded and displayed

## Sport Theme Mapping

The system maps sport names to theme directories:

- `Swimming` → `swimming` theme
- `BASEBALL` → `Baseball` theme  
- `BASKETBALL` → `BasketBall` theme
- `Boxing` → `Boxer` theme
- `Mixed Martial Arts` → `MMA` theme
- `FOOTBALL` → `american` theme
- `Field hockey` → `field-hocky` theme
- `Gymnastics` → `Gymnastic` theme
- `Lacrosse` → `lacrosse` theme
- `Track and Field` → `track-and-field` theme
- `Volleyball` → `Volleyball` theme

If no specific theme exists for a sport, the `default` theme is used.

## File Structure

```
resources/views/public/profiles/
├── base.blade.php          # Base layout template
├── default.blade.php       # Default theme for unknown sports
├── swimming.blade.php      # Swimming-specific theme
├── BasketBall.blade.php    # Basketball-specific theme
└── [other-sport].blade.php # Other sport themes
```

## Creating New Themes

To create a new theme for a sport:

1. Create a new Blade template in `resources/views/public/profiles/`
2. Extend the base template: `@extends('public.profiles.base')`
3. Override sections as needed:
   - `@section('theme-css')` - Add sport-specific CSS
   - `@section('header')` - Custom header
   - `@section('hero')` - Custom hero section
   - `@section('tabs')` - Custom tab content
   - `@section('newsletter')` - Custom newsletter
   - `@section('footer')` - Custom footer

4. Add the sport mapping in `PublicProfileController::getThemeBySport()`

## Example: Creating a Basketball Theme

```php
@extends('public.profiles.swimming')

@section('theme-css')
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/font.css') }}" />
<style>
    /* Basketball-specific styles */
    .hero {
        background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    }
    
    .player-name {
        color: #1e3a8a;
    }
    
    .stats-value {
        color: #ff6b35;
    }
</style>
@endsection
```

## Available Data

The following data is available in all theme templates:

- `$player` - Player model with relationships
- `$weather` - Current weather data
- `$statsWithValues` - Player statistics
- `$playerRewards` - Player's rewards/achievements
- `$posts` - Player's blog posts
- `$pickupGames` - Available pickup games for the sport
- `$allRewards` - All available rewards
- `$teamMembers` - Other players on the same team
- `$clubHistory` - Player's club history
- `$achievements` - Player's achievements

## Testing

Use the test route to verify the system:
```
GET /test/public-profile
```

This will return JSON with player info and theme selection.

## Usage

To link to a player's public profile:
```php
<a href="{{ route('public.player.profile', $player->id) }}">View Profile</a>
```

## Customization

Each theme can be customized by:

1. **CSS Overrides**: Add sport-specific styles in the `@section('theme-css')`
2. **Layout Changes**: Override any section from the base template
3. **Content Modifications**: Customize the content display for specific sports
4. **Asset Integration**: Use sport-specific images, colors, and branding

## Security

- Public profiles are accessible without authentication
- Only published player data is displayed
- Sensitive information is filtered out
- Social links and PayPal links are optional and user-controlled

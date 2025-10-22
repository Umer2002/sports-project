@php
    $photoUrl = null;

    if (!empty($player->photo)) {
        $rawPhotoPath = $player->photo;
        $normalizedPath = ltrim($rawPhotoPath, '/');

        if (filter_var($rawPhotoPath, FILTER_VALIDATE_URL)) {
            $photoUrl = $rawPhotoPath;
        } elseif (\Illuminate\Support\Str::startsWith($normalizedPath, ['storage/', 'uploads/', 'images/'])) {
            $photoUrl = asset($normalizedPath);
        } elseif (\Illuminate\Support\Str::startsWith($normalizedPath, 'public/')) {
            $photoUrl = asset(substr($normalizedPath, strlen('public/')));
        } else {
            $photoUrl = asset('storage/' . $normalizedPath);
        }
    }

    $displayName = trim($player->name ?? '');
    $initial = $displayName !== '' ? strtoupper(substr($displayName, 0, 1)) : '?';
@endphp

<div class="player-card" data-id="{{ $player->id }}">
    <div class="player-avatar">
        @if ($photoUrl)
            <img src="{{ $photoUrl }}" alt="{{ $player->name }}">
        @else
            <span>{{ $initial }}</span>
        @endif
    </div>
    <div class="player-card__info">
        <span class="player-card__name">{{ $player->name }}</span>
        @if ($player->email)
            <span class="player-card__meta">{{ $player->email }}</span>
        @endif
    </div>
</div>

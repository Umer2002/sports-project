@php
    $weather = $weather ?? null;
    $shareLabel = $shareLabel ?? __('Share Profile');
    $socialLinks = $socialLinks ?? [];
    $locationFallback = $locationFallback ?? '--';
    $wrapperClass = $wrapperClass ?? 'weather-widget';
    $socialWrapperClass = $socialWrapperClass ?? 'social-icons justify-content-center';
    $fallbackWeatherIcon = $fallbackWeatherIcon ?? asset('assets/club-dashboard-main/assets/sun-cloud.png');
    $fallbackTemperature = $fallbackTemperature ?? '—°';

    $entity = $entity ?? [];
    $entityImage = $entity['image'] ?? null;
    $entityAlt = $entity['alt'] ?? 'Sport';
    $entityAttributes = $entity['attributes'] ?? '';
    $entityFallbackImage = $entity['fallback_image'] ?? asset('assets/club-dashboard-main/assets/football.png');
    $entityFallbackAlt = $entity['fallback_alt'] ?? $entityAlt;
    $entityText = $entity['text'] ?? null;
    $entityTextClass = $entity['text_class'] ?? 'sport-fallback d-flex align-items-center justify-content-center h-100';
    $entityHtml = $entity['html'] ?? null;
@endphp

<div class="{{ $wrapperClass }}">
    <div class="football">
        @if ($entityHtml)
            {!! $entityHtml !!}
        @elseif ($entityImage)
            <img src="{{ $entityImage }}" alt="{{ $entityAlt }}" {!! $entityAttributes !!}>
        @elseif ($entityText)
            <div class="{{ $entityTextClass }}">{{ $entityText }}</div>
        @else
            <img src="{{ $entityFallbackImage }}" alt="{{ $entityFallbackAlt }}">
        @endif
    </div>
    <div class="weather-info">
        @if ($weather)
            <div class="weather-temp">{{ $weather['temp'] ?? $fallbackTemperature }}</div>
            @if (!empty($weather['icon']))
                <img src="https://openweathermap.org/img/wn/{{ $weather['icon'] }}@2x.png" alt="{{ $weather['condition'] ?? 'Weather icon' }}">
            @else
                <img src="{{ $fallbackWeatherIcon }}" alt="{{ $weather['condition'] ?? 'Weather' }}">
            @endif
            <div class="weather-details">
                <div class="weather-detail">H:{{ $weather['temp_max'] ?? '—' }}° L:{{ $weather['temp_min'] ?? '—' }}°</div>
                <div class="weather-location">
                    @php
                        $city = $weather['city'] ?? null;
                        $country = $weather['country'] ?? null;
                        $locationText = trim(($city ? $city : '') . ($city && $country ? ', ' : '') . ($country ?? ''));
                    @endphp
                    {{ $locationText !== '' ? $locationText : $locationFallback }}
                </div>
            </div>
        @else
            <div class="weather-temp">{{ $fallbackTemperature }}</div>
            <img src="{{ $fallbackWeatherIcon }}" alt="Weather">
            <div class="weather-details">
                <div class="weather-detail">H:—° L:—°</div>
                <div class="weather-location">{{ $locationFallback }}</div>
            </div>
        @endif
    </div>
    <div class="social-share">
        <div class="share-label">{{ $shareLabel }}</div>
        <div class="{{ $socialWrapperClass }}">
            @foreach ($socialLinks as $social)
                @php
                    $type = $social['type'] ?? 'icon';
                    $class = 'social-icon' . (!empty($social['class']) ? ' ' . $social['class'] : '');
                    $url = $social['url'] ?? null;
                    $target = $social['target'] ?? '_blank';
                    $rel = $social['rel'] ?? 'noopener noreferrer';
                    $label = $social['label'] ?? null;
                    $tag = $url ? 'a' : 'span';
                    $attributes = [
                        'class' => 'social-link',
                    ];

                    if ($url) {
                        $attributes['href'] = $url;
                        $attributes['target'] = $target;
                        $attributes['rel'] = $rel;
                    } else {
                        $attributes['role'] = 'button';
                        $attributes['aria-disabled'] = 'true';
                        $attributes['tabindex'] = '-1';
                    }

                    if ($label) {
                        $attributes['title'] = $label;
                    }

                    $attributeString = collect($attributes)
                        ->map(fn ($value, $key) => $key . '="' . e($value) . '"')
                        ->implode(' ');
                @endphp
                <div class="{{ $class }}">
                    <{{ $tag }} {!! $attributeString !!}>

                    @if ($type === 'image')
                        <img src="{{ $social['src'] ?? '' }}" alt="{{ $social['alt'] ?? ($label ?? 'Social icon') }}" width="{{ $social['width'] ?? 28 }}" height="{{ $social['height'] ?? 28 }}">
                    @elseif (!empty($social['icon_class']))
                        <i class="{{ $social['icon_class'] }}"></i>
                    @else
                        @php
                            $icon = $social['icon'] ?? null;
                            $iconPrefix = $social['icon_prefix'] ?? 'fab fa-';
                        @endphp
                        @if ($icon)
                            <i class="{{ trim($iconPrefix . $icon) }}"></i>
                        @endif
                    @endif

                    </{{ $tag }}>
                </div>
            @endforeach
        </div>
    </div>
</div>

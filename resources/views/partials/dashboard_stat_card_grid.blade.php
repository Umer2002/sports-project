@php
    $cards = $cards ?? [];
    $colClasses = $colClasses ?? 'col-sm-6 col-md-6 col-lg-3';
    $rowClass = $rowClass ?? 'mb-4';
    $imageMap = $imageMap ?? [
        'green' => asset('assets/club-dashboard-main/assets/bars.png'),
        'orange' => asset('assets/club-dashboard-main/assets/graph.png'),
        'blue' => asset('assets/club-dashboard-main/assets/bars.png'),
        'purple' => asset('assets/club-dashboard-main/assets/pie.png'),
        'teal' => asset('assets/club-dashboard-main/assets/bars.png'),
        'cyan' => asset('assets/club-dashboard-main/assets/graph.png'),
    ];
    $trendIconMap = $trendIcons ?? [
        'up' => asset('assets/club-dashboard-main/assets/ic_trending_up.png'),
        'down' => asset('assets/club-dashboard-main/assets/ic_trending_down.png'),
    ];
@endphp

@once
    <style>
        .stat-card {
            border-radius: 12px !important;
            width: 100% !important;
            max-width: 280px !important;
            height: auto !important;
            padding: 16px !important;
            box-sizing: border-box !important;
            color: white !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            /* gap: 8px; */
        }

        .stat-card img {
            max-width: 100%;
        }

        .stat-card canvas {
            width: 100% !important;
            height: 60px !important;
            max-height: 60px !important;
        }

        .stat-card .stat-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border-radius: 6px !important;
            font-size: 12px;
            font-weight: 500;
            padding: 2px 10px;
            line-height: 1.1;
            min-height: 22px;
        }

        .stat-card .stat-value {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 4px;
            color: white;
        }

        .stat-card .stat-trend,
        .stat-card .stat-footer,
        .stat-card .stat-description {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
        }

        .stat-card.green {
            background: linear-gradient(92.43deg, #74d876 20.65%, rgba(70, 132, 119, 0.9) 95.8%) !important;
        }

        .stat-card.orange {
            background: linear-gradient(92.43deg, #ea7d4d 20.65%, rgba(226, 169, 68, 0.9) 95.8%) !important;
        }

        .stat-card.blue {
            background: linear-gradient(92.43deg, #0075ff 20.65%, rgba(120, 189, 222, 0.9) 95.8%) !important;
        }

        .stat-card.purple {
            background: linear-gradient(92.43deg, #7b4ed7 20.65%, rgba(102, 125, 242, 0.9) 95.8%) !important;
        }

        .stat-card.teal {
            background: linear-gradient(92.43deg, #20c997 20.65%, rgba(32, 201, 151, 0.9) 95.8%) !important;
        }

        .stat-card.cyan {
            background: linear-gradient(92.43deg, #17a2b8 20.65%, rgba(23, 162, 184, 0.9) 95.8%) !important;
        }
    </style>
@endonce

@if (!empty($cards))
    <div class="stats-card">
        <div class="row{{ $rowClass ? ' ' . $rowClass : '' }}">
            @foreach ($cards as $card)
                @php
                    $color = $card['color'] ?? null;
                    $cardClasses = array_filter(['stat-card', $color, $card['class'] ?? null]);
                    $cardClass = implode(' ', $cardClasses);
                    $columnClass = $card['column_class'] ?? $colClasses;
                    $image = $card['image'] ?? ($color && isset($imageMap[$color]) ? $imageMap[$color] : null);
                    $value = $card['value'] ?? 0;
                    $format = $card['format'] ?? 'number';
                    if ($format === 'number' && is_numeric($value)) {
                        $value = number_format((float) $value);
                    }
                    $trendIcon = $card['trend_icon'] ?? null;
                    $trendDirection = $card['trend_direction'] ?? null;
                    if (!$trendIcon && $trendDirection && isset($trendIconMap[$trendDirection])) {
                        $trendIcon = $trendIconMap[$trendDirection];
                    }
                    $badge = $card['badge'] ?? ($card['label'] ?? null);
                    $badgeAttributes = $card['badge_attributes'] ?? '';
                    $footer = $card['trend_text'] ?? ($card['footer'] ?? ($card['description'] ?? null));
                    $before = $card['before'] ?? null;
                    $after = $card['after'] ?? null;
                @endphp
                <div class="{{ $columnClass }}">
                    <div class="{{ $cardClass }}">
                        @if ($before)
                            {!! $before !!}
                        @endif

                        @if ($image)
                            @if (!empty($card['image_wrapper_class']))
                                <div class="{{ $card['image_wrapper_class'] }}">
                                    <img src="{{ $image }}" alt="{{ $badge ?? 'stat visual' }}" class="{{ $card['image_class'] ?? 'mb-1' }}">
                                </div>
                            @else
                                <img src="{{ $image }}" alt="{{ $badge ?? 'stat visual' }}" class="{{ $card['image_class'] ?? 'mb-1' }}">
                            @endif
                        @endif

                        @if ($badge)
                            <div class="stat-badge {{ $card['badge_class'] ?? '' }}" {!! $badgeAttributes !!}>{{ $badge }}</div>
                        @endif

                        <div class="stat-value {{ $card['value_class'] ?? '' }}">
                            <span>{{ $value }}</span>
                            @if ($trendIcon)
                                <img src="{{ $trendIcon }}" alt="{{ $trendDirection ?? 'trend' }}">
                            @endif
                        </div>

                        @if ($footer)
                            <div class="{{ $card['trend_class'] ?? 'stat-trend' }}">{{ $footer }}</div>
                        @endif

                        @if ($after)
                            {!! $after !!}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

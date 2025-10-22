<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CalendarEventPreference;
use App\Models\Event;
use App\Models\GameMatch;
use App\Models\Tournament;
use App\Models\Venue;
use App\Traits\CalendarEventHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class CalendarEventController extends Controller
{
    use CalendarEventHelpers;

    protected const TYPE_MAP = [
        'event' => Event::class,
        'tournament' => Tournament::class,
        'match' => GameMatch::class,
    ];

    public function show(string $type, int $id)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $resource = $this->resolveResource($type, $id);

        $preference = $this->findOrCreatePreference($user->id, $type, $resource->id);

        $payload = $this->buildPayload($type, $resource, $preference);

        return response()->json($payload);
    }

    public function updatePreference(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $resource = $this->resolveResource($type, $id);
        $preference = $this->findOrCreatePreference($user->id, $type, $resource->id);

        if (! $this->preferencesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Preference storage is not available yet. Please run the latest migrations.',
            ], 503);
        }

        $data = $request->validate([
            'attending_status' => ['nullable', Rule::in(['yes', 'maybe', 'no'])],
            'carpool_status' => ['nullable', Rule::in(['driver', 'rider'])],
            'seats_available' => ['nullable', 'integer', 'min:0', 'max:20'],
            'calendar_added' => ['nullable', 'boolean'],
        ]);

        $updates = Arr::only($data, ['attending_status', 'carpool_status']);

        if (array_key_exists('seats_available', $data)) {
            $updates['seats_available'] = $data['seats_available'];
        }

        if (! empty($data['calendar_added'])) {
            $updates['calendar_added_at'] = now();
        }

        $preference->fill($updates);
        $preference->save();

        return response()->json([
            'success' => true,
            'preference' => $this->transformPreference($preference),
        ]);
    }

    public function upload(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $resource = $this->resolveResource($type, $id);
        $preference = $this->findOrCreatePreference($user->id, $type, $resource->id);

        if (! $this->preferencesEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Image uploads are disabled until the latest migrations are run.',
                'attachments' => [],
            ], 503);
        }

        $request->validate([
            'image' => ['required', 'image', 'max:8192'], // ~8MB
        ]);

        $file = $request->file('image');
        $path = $file->store('calendar-uploads', 'public');

        $attachments = $preference->attachments ?? [];
        $attachments[] = [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
        ];
        $preference->attachments = $attachments;
        $preference->save();

        return response()->json([
            'success' => true,
            'attachments' => $this->transformPreference($preference)['attachments'],
        ]);
    }

    public function downloadIcs(string $type, int $id)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $resource = $this->resolveResource($type, $id);
        $preference = $this->findOrCreatePreference($user->id, $type, $resource->id);

        if (! $preference->calendar_added_at) {
            if ($this->preferencesEnabled()) {
                $preference->calendar_added_at = now();
                $preference->save();
            }
        }

        $details = $this->buildIcsDetails($type, $resource);

        $ics = $this->renderIcs($details);

        $filename = sprintf('%s-%s.ics', $type, $resource->id);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function resolveResource(string $type, int $id)
    {
        $class = self::TYPE_MAP[$type] ?? null;
        abort_unless($class, 404, 'Unsupported calendar item type.');

        $query = $class::query();

        switch ($type) {
            case 'event':
                $query->with([
                    'team.players',
                    'team.coaches.user',
                    'team.club',
                    'club',
                    'user',
                ]);
                break;
            case 'tournament':
                $query->with([
                    'venue.city',
                    'venue.state',
                    'venue.hotels',
                    'hostClub',
                    'teams.club',
                    'teams.players',
                    'coaches.user',
                ]);
                break;
            case 'match':
                $query->with([
                    'tournament.venue.city',
                    'tournament.venue.state',
                    'tournament.venue.hotels',
                    'tournament.teams.club',
                    'tournament.teams.players',
                    'tournament.coaches.user',
                    'homeClub.players',
                    'awayClub.players',
                    'homeClub',
                    'awayClub',
                    'venue.city',
                    'venue.state',
                ]);
                break;
        }

        $resource = $query->findOrFail($id);

        return $resource;
    }

    protected function findOrCreatePreference(int $userId, string $type, int $resourceId): CalendarEventPreference
    {
        $class = self::TYPE_MAP[$type] ?? null;

        if (! $this->preferencesEnabled() || ! $class) {
            return CalendarEventPreference::make([
                'user_id' => $userId,
                'preferenceable_type' => $class,
                'preferenceable_id' => $resourceId,
            ]);
        }

        return CalendarEventPreference::firstOrCreate([
            'user_id' => $userId,
            'preferenceable_type' => $class,
            'preferenceable_id' => $resourceId,
        ]);
    }

    protected function buildPayload(string $type, $resource, CalendarEventPreference $preference): array
    {
        $title = $this->buildTitle($type, $resource);
        $start = $this->resolveStart($type, $resource);
        $end = $this->resolveEnd($type, $resource, $start);
        $when = $this->formatWhen($start, $end);

        $venue = $this->buildVenue($type, $resource);
        $map = $this->buildMapLinks($venue['lat'], $venue['lng'], $venue['query']);
        $hotels = $this->buildHotels($type, $resource);

        $clubs = $this->summarizeNames($this->collectClubs($type, $resource), 12);
        $coaches = $this->summarizeNames($this->collectCoaches($type, $resource));
        $players = $this->summarizeNames($this->collectPlayers($type, $resource));

        $team = $this->resolveTeamMeta($type, $resource);

        return [
            'type' => $type,
            'id' => $resource->id,
            'title' => $title,
            'when' => $when,
            'start_iso' => $start?->toIso8601String(),
            'end_iso' => $end?->toIso8601String(),
            'venue' => Arr::only($venue, ['name', 'line', 'query']),
            'map' => $map,
            'hotels' => $hotels,
            'clubs' => $clubs,
            'coaches' => $coaches,
            'players' => $players,
            'description' => (string) ($resource->description ?? ''),
            'team' => $team,
            'preference' => $this->transformPreference($preference),
            'preferences_enabled' => $this->preferencesEnabled(),
            'routes' => [
                'preference' => route('calendar.preference.update', ['type' => $type, 'id' => $resource->id]),
                'upload' => route('calendar.preference.upload', ['type' => $type, 'id' => $resource->id]),
                'ics' => route('calendar.preference.ics', ['type' => $type, 'id' => $resource->id]),
            ],
        ];
    }

    protected function buildTitle(string $type, $resource): string
    {
        return match ($type) {
            'event' => ($resource->type ? ucfirst($resource->type) . ': ' : 'Event: ') . ($resource->title ?? 'Event'),
            'tournament' => ($resource->host_club_id ? 'Tournament Hosted: ' : 'Tournament: ') . ($resource->name ?? 'Tournament'),
            'match' => 'Match: ' . trim(($resource->homeClub->name ?? 'Home') . ' vs ' . ($resource->awayClub->name ?? 'Away')),
            default => 'Calendar Item',
        };
    }

    protected function resolveStart(string $type, $resource): ?Carbon
    {
        return match ($type) {
            'event' => $resource->start ? Carbon::parse($resource->start) : null,
            'tournament' => $resource->start_date ? $resource->start_date->copy()->startOfDay() : null,
            'match' => $this->combineDateTimeAsCarbon($resource->match_date, $resource->match_time),
            default => null,
        };
    }

    protected function resolveEnd(string $type, $resource, ?Carbon $start): ?Carbon
    {
        return match ($type) {
            'event' => $resource->end ? Carbon::parse($resource->end) : ($start ? $start->copy()->addHours(2) : null),
            'tournament' => $resource->end_date ? $resource->end_date->copy()->endOfDay() : ($start ? $start->copy()->addDay() : null),
            'match' => $start ? $start->copy()->addHours(2) : null,
            default => null,
        };
    }

    protected function formatWhen(?Carbon $start, ?Carbon $end): string
    {
        if (! $start) {
            return '—';
        }

        $startStr = $start->format('M j, Y g:i a');

        if (! $end) {
            return $startStr;
        }

        if ($start->isSameDay($end)) {
            return $startStr . ' - ' . $end->format('g:i a');
        }

        return $startStr . ' - ' . $end->format('M j, Y g:i a');
    }

    protected function buildVenue(string $type, $resource): array
    {
        $name = null;
        $line = null;
        $query = null;
        $lat = null;
        $lng = null;

        $venue = null;
        if ($type === 'event') {
            $name = $resource->location ?? 'TBD';
            $line = $resource->location ?? '';
            $query = $resource->location ?? '';
        } elseif ($type === 'tournament') {
            $venue = $resource->venue;
        } elseif ($type === 'match') {
            $venue = $resource->venue ?? optional($resource->tournament)->venue;
        }

        if ($venue instanceof Venue) {
            $name = $venue->name ?? $name;
            $addressParts = array_filter([
                $venue->location,
                optional($venue->city)->name,
                optional($venue->state)->name,
            ]);
            $line = $addressParts ? implode(', ', $addressParts) : $line;
            $query = $line ?: $name;
            if (isset($venue->latitude) && isset($venue->longitude)) {
                $lat = is_numeric($venue->latitude) ? (float) $venue->latitude : null;
                $lng = is_numeric($venue->longitude) ? (float) $venue->longitude : null;
            }
        }

        return [
            'name' => $name ?: '—',
            'line' => $line ?: $name ?: '—',
            'query' => $query,
            'lat' => $lat,
            'lng' => $lng,
        ];
    }

    protected function buildHotels(string $type, $resource): array
    {
        $hotels = collect();

        if ($type === 'tournament') {
            $hotels = $resource->hotels ?? collect();
            if ($hotels->isEmpty() && $resource->venue) {
                $hotels = $resource->venue->hotels ?? collect();
            }
        } elseif ($type === 'match' && $resource->tournament) {
            $hotels = $resource->tournament->hotels ?? collect();
            if ($hotels->isEmpty() && $resource->tournament->venue) {
                $hotels = $resource->tournament->venue->hotels ?? collect();
            }
        }

        return collect($hotels)
            ->map(function ($hotel) {
                $mapsUrl = null;
                if (! empty($hotel->google_place_id)) {
                    $mapsUrl = 'https://www.google.com/maps/place/?q=place_id:' . urlencode($hotel->google_place_id);
                } elseif (! empty($hotel->address)) {
                    $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($hotel->address);
                }

                return array_filter([
                    'id' => $hotel->id ?? null,
                    'name' => $hotel->name ?? null,
                    'address' => $hotel->address ?? null,
                    'maps_url' => $mapsUrl,
                ]);
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function collectClubs(string $type, $resource)
    {
        return match ($type) {
            'event' => array_filter([
                optional($resource->club)->name,
                optional($resource->team?->club)->name,
            ]),
            'tournament' => collect([
                optional($resource->hostClub)->name,
            ])->merge(
                $resource->teams->map(fn ($team) => optional($team->club)->name)
            ),
            'match' => array_filter([
                optional($resource->homeClub)->name,
                optional($resource->awayClub)->name,
                optional(optional($resource->tournament)->hostClub)->name,
            ]),
            default => [],
        };
    }

    protected function collectCoaches(string $type, $resource)
    {
        return match ($type) {
            'event' => $resource->team?->coaches ?? [],
            'tournament' => $resource->coaches ?? [],
            'match' => optional($resource->tournament)->coaches ?? [],
            default => [],
        };
    }

    protected function collectPlayers(string $type, $resource)
    {
        return match ($type) {
            'event' => $resource->team?->players ?? [],
            'tournament' => $resource->teams?->flatMap(fn ($team) => $team->players) ?? [],
            'match' => collect()
                ->merge($resource->homeClub?->players ?? [])
                ->merge($resource->awayClub?->players ?? [])
                ->merge(optional($resource->tournament)->teams?->flatMap(fn ($team) => $team->players) ?? []),
            default => [],
        };
    }

    protected function resolveTeamMeta(string $type, $resource): ?array
    {
        if ($type === 'event' && $resource->team) {
            return [
                'id' => $resource->team->id,
                'name' => $resource->team->name,
                'routes' => [
                    'player' => route('player.teams.chat', $resource->team),
                    'club' => route('club.teams.chat', $resource->team),
                ],
            ];
        }

        if ($type === 'match' && $resource->tournament) {
            $team = $resource->tournament->teams->first();
            if ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'routes' => [
                        'player' => route('player.teams.chat', $team),
                        'club' => route('club.teams.chat', $team),
                    ],
                ];
            }
        }

        return null;
    }

    protected function preferencesEnabled(): bool
    {
        static $cache = null;

        if ($cache === null) {
            try {
                $cache = Schema::hasTable('calendar_event_preferences');
            } catch (\Throwable $e) {
                $cache = false;
            }
        }

        return $cache;
    }

    protected function transformPreference(CalendarEventPreference $preference): array
    {
        $attachments = collect($preference->attachments ?? [])
            ->map(function ($item) {
                $path = $item['path'] ?? null;
                if (! $path) {
                    return null;
                }

                return [
                    'path' => $path,
                    'name' => $item['name'] ?? basename($path),
                    'url' => Storage::disk('public')->url($path),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'attending_status' => $preference->attending_status,
            'carpool_status' => $preference->carpool_status,
            'seats_available' => $preference->seats_available,
            'calendar_added_at' => optional($preference->calendar_added_at)?->toIso8601String(),
            'attachments' => $attachments,
        ];
    }

    protected function combineDateTimeAsCarbon($date, $time): ?Carbon
    {
        if (! $date) {
            return null;
        }

        $dateObj = $date instanceof Carbon ? $date : Carbon::parse($date);
        if ($time) {
            try {
                $timeObj = Carbon::parse($time);
                return Carbon::create(
                    $dateObj->year,
                    $dateObj->month,
                    $dateObj->day,
                    $timeObj->hour,
                    $timeObj->minute,
                    $timeObj->second
                );
            } catch (\Throwable $e) {
                // fall back to date only
            }
        }

        return $dateObj->copy()->startOfDay();
    }

    protected function buildIcsDetails(string $type, $resource): array
    {
        $start = $this->resolveStart($type, $resource) ?? now();
        $end = $this->resolveEnd($type, $resource, $start) ?? $start->copy()->addHour();
        $venue = $this->buildVenue($type, $resource);

        return [
            'uid' => sprintf('%s-%s@play2earn', $type, $resource->id),
            'title' => $this->buildTitle($type, $resource),
            'description' => $resource->description ?? '',
            'start' => $start,
            'end' => $end,
            'location' => $venue['line'] ?? $venue['name'],
        ];
    }

    protected function renderIcs(array $details): string
    {
        $format = fn (Carbon $value) => $value->copy()->utc()->format('Ymd\THis\Z');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Play2Earn//Calendar//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:' . $details['uid'],
            'DTSTAMP:' . $format(now()),
            'DTSTART:' . $format($details['start']),
            'DTEND:' . $format($details['end']),
            'SUMMARY:' . $this->escapeIcsText($details['title']),
        ];

        if (! empty($details['description'])) {
            $lines[] = 'DESCRIPTION:' . $this->escapeIcsText($details['description']);
        }

        if (! empty($details['location'])) {
            $lines[] = 'LOCATION:' . $this->escapeIcsText($details['location']);
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    protected function escapeIcsText(string $text): string
    {
        $escaped = str_replace(['\\', ';', ',', "\n"], ['\\\\', '\;', '\,', '\n'], $text);
        return preg_replace('/[\r]+/', '', $escaped);
    }
}

<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * Shared helpers for composing calendar payloads.
 */
trait CalendarEventHelpers
{
    protected function summarizeNames($items, int $limit = 20): array
    {
        $names = collect($items)
            ->map(function ($value) {
                if ($value === null) {
                    return null;
                }

                if (is_string($value)) {
                    $trimmed = trim($value);
                    return $trimmed !== '' ? $trimmed : null;
                }

                if (is_object($value)) {
                    $composed = trim(($value->name ?? '') ?: (($value->first_name ?? '') . ' ' . ($value->last_name ?? '')));
                    if ($composed !== '') {
                        return $composed;
                    }

                    if (! empty($value->email)) {
                        $mail = trim($value->email);
                        if ($mail !== '') {
                            return $mail;
                        }
                    }

                    if (isset($value->title)) {
                        $title = trim($value->title);
                        if ($title !== '') {
                            return $title;
                        }
                    }

                    if (isset($value->user) && $value->user) {
                        $userName = trim($value->user->name ?? '');
                        if ($userName !== '') {
                            return $userName;
                        }
                    }
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values();

        return [
            'list' => $names->take($limit)->values()->all(),
            'total' => $names->count(),
            'overflow' => max(0, $names->count() - $limit),
        ];
    }

    protected function formatDateRange($start, $end): ?string
    {
        if (! $start && ! $end) {
            return null;
        }

        if ($start && $end) {
            return $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
        }

        if ($start) {
            return $start->format('M d, Y');
        }

        return $end->format('M d, Y');
    }

    protected function combineDateAndTime($date, $time): ?string
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof Carbon) {
            $dateString = $date->format('Y-m-d');
        } else {
            try {
                $dateString = Carbon::parse($date)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $timeString = null;
        if ($time) {
            try {
                $timeString = Carbon::parse($time)->format('H:i:s');
            } catch (\Throwable $e) {
                $timeString = null;
            }
        }

        return $timeString ? "{$dateString}T{$timeString}" : "{$dateString}T00:00:00";
    }

    protected function buildMapLinks(?float $lat, ?float $lng, ?string $query): array
    {
        $q = null;

        if ($lat !== null && $lng !== null) {
            $q = "{$lat},{$lng}";
        } elseif ($query && trim($query) !== '') {
            $q = trim($query);
        }

        if (! $q) {
            return [
                'embed' => null,
                'view' => null,
            ];
        }

        return [
            'embed' => 'https://www.google.com/maps?q=' . urlencode($q) . '&hl=en&z=15&output=embed',
            'view' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode($q),
        ];
    }
}


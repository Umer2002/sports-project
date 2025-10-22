<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class EventService
{
    /**
     * Get events relevant to a specific user
     */
    public static function getRelevantEvents(User $user)
    {
        return Event::where(function($query) use ($user) {
            // Events created by the user
            $query->where('user_id', $user->id)
                // Events where user is invited
                ->orWhereHas('invites', function($inviteQuery) use ($user) {
                    $inviteQuery->where('type', 'user')
                        ->where('reference_id', $user->id);
                })
                // Events created by user's club
                ->orWhereHas('user', function($userQuery) use ($user) {
                    $userQuery->where('club_id', $user->club_id);
                })
                // Events created by user's team members
                ->orWhereHas('user', function($userQuery) use ($user) {
                    $userQuery->whereHas('player', function($playerQuery) use ($user) {
                        $playerQuery->where('club_id', $user->club_id);
                    });
                })
                // Team-specific events where user is a team member
                ->orWhereHas('team', function($teamQuery) use ($user) {
                    $teamQuery->whereHas('players', function($playerQuery) use ($user) {
                        $playerQuery->where('user_id', $user->id);
                    });
                });
        })
        ->select('id', 'title', 'start', 'end', 'location', 'type', 'user_id')
        ->get()
        ->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->getFormattedDateTime(),
                'url' => self::getEventUrl($event),
                'color' => $event->getEventColor(),
                'location' => $event->location,
                'type' => $event->type,
                'created_by' => $event->user->name ?? 'Unknown',
            ];
        });
    }

    /**
     * Get events for admin (all events)
     */
    public static function getAllEvents()
    {
        return Event::select('id', 'title', 'start', 'end', 'location', 'type', 'user_id')
        ->get()
        ->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->getFormattedDateTime(),
                'url' => self::getEventUrl($event),
                'color' => $event->getEventColor(),
                'location' => $event->location,
                'type' => $event->type,
                'created_by' => $event->user->name ?? 'Unknown',
            ];
        });
    }

    /**
     * Get events for club
     */
    public static function getClubEvents($clubId)
    {
        return Event::where(function($query) use ($clubId) {
            // Events directly associated with the club
            $query->where('club_id', $clubId)
            // Events created by club members
            ->orWhereHas('user', function($userQuery) use ($clubId) {
                $userQuery->where('club_id', $clubId);
            })
            // Events where club is invited
            ->orWhereHas('invites', function($inviteQuery) use ($clubId) {
                $inviteQuery->where('type', 'club')
                    ->where('reference_id', $clubId);
            })
            // Events created by club's players
            ->orWhereHas('user', function($userQuery) use ($clubId) {
                $userQuery->whereHas('player', function($playerQuery) use ($clubId) {
                    $playerQuery->where('club_id', $clubId);
                });
            });
        })
        ->select('id', 'title', 'start', 'end', 'location', 'type', 'user_id')
        ->get()
        ->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->getFormattedDateTime(),
                'url' => self::getEventUrl($event),
                'color' => $event->getEventColor(),
                'location' => $event->location,
                'type' => $event->type,
                'created_by' => $event->user->name ?? 'Unknown',
            ];
        });
    }

    /**
     * Get events for player
     */
    public static function getPlayerEvents($playerId, $clubId)
    {
        return Event::where(function($query) use ($playerId, $clubId) {
            // Events created by the player
            $query->whereHas('user', function($userQuery) use ($playerId) {
                $userQuery->whereHas('player', function($playerQuery) use ($playerId) {
                    $playerQuery->where('id', $playerId);
                });
            })
            // Events where player is invited
            ->orWhereHas('invites', function($inviteQuery) use ($playerId) {
                $inviteQuery->where('type', 'player')
                    ->where('reference_id', $playerId);
            })
            // Events created by player's club
            ->orWhereHas('user', function($userQuery) use ($clubId) {
                $userQuery->where('club_id', $clubId);
            })
            // Events created by player's teammates
            ->orWhereHas('user', function($userQuery) use ($clubId) {
                $userQuery->whereHas('player', function($playerQuery) use ($clubId) {
                    $playerQuery->where('club_id', $clubId);
                });
            });
        })
        ->select('id', 'title', 'event_date', 'event_time', 'location', 'type', 'user_id')
        ->get()
        ->map(function ($event) {
            return [
                'title' => $event->title,
                'start' => $event->getFormattedDateTime(),
                'url' => self::getEventUrl($event),
                'color' => $event->getEventColor(),
                'location' => $event->location,
                'type' => $event->type,
                'created_by' => $event->user->name ?? 'Unknown',
            ];
        });
    }

    /**
     * Get the appropriate URL for an event based on user role
     */
    private static function getEventUrl($event)
    {
        $user = Auth::user();
        
        if ($user && $user->hasRole('admin') && Route::has('admin.events.show')) {
            return route('admin.events.show', $event->id);
        }

        if ($user && $user->hasRole('club') && Route::has('club.events.show')) {
            return route('club.events.show', $event->id);
        }

        if ($user && $user->hasRole('player')) {
            if (Route::has('player.events.show')) {
                return route('player.events.show', $event->id);
            }

            return Route::has('player.calendar')
                ? route('player.calendar', ['event' => $event->id])
                : '#';
        }

        if (Route::has('club.events.show')) {
            return route('club.events.show', $event->id);
        }

        return '#';
    }
}

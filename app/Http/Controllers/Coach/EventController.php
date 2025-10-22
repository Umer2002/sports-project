<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Coach;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Get events for teams the coach is assigned to
        $teamIds = $coach->teams()->pluck('teams.id');
        $events = Event::whereIn('team_id', $teamIds)
            ->orWhere('user_id', auth()->id())
            ->orderBy('event_date', 'desc')
            ->paginate(20);

        return view('coach.events.index', compact('events'));
    }

    public function create()
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        $teams = $coach->teams;
        return view('coach.events.create', compact('teams'));
    }

    public function store(Request $request)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $data['user_id'] = auth()->id();

        Event::create($data);

        return redirect()->route('coach.events.index')->with('success', 'Event created successfully!');
    }

    public function edit(Event $event)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $coach = auth()->user()->coach;
        if (!$coach) {
            return redirect()->route('coach.setup');
        }

        // Check if coach owns this event or is assigned to the team
        if ($event->user_id !== auth()->id() && !$coach->teams()->where('teams.id', $event->team_id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $teams = $coach->teams;
        return view('coach.events.edit', compact('event', 'teams'));
    }

    public function update(Request $request, Event $event)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $event->update($data);

        return redirect()->route('coach.events.index')->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        if (!auth()->check() || !auth()->user()->hasRole('coach')) {
            abort(403);
        }

        // Check if coach owns this event
        if ($event->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $event->delete();

        return redirect()->route('coach.events.index')->with('success', 'Event deleted successfully!');
    }
}


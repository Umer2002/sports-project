<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('user_id', Auth::id())->latest()->get();
        return view('club.events.index', compact('events'));
    }

    public function create()
    {
        return view('club.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'location' => 'nullable|string',
            'status' => 'nullable|in:draft,published',
            'event_data' => 'nullable|array'
        ]);

        try {
            $club = Auth::user()->club;
            
            if (!$club) {
                return response()->json([
                    'success' => false,
                    'message' => 'Club not found'
                ], 404);
            }

            $startDateTime = $request->event_date . ' ' . $request->event_time;
            $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime) + 7200); // 2 hours duration

            $event = Event::create([
                'user_id' => Auth::id(),
                'club_id' => $club->id,
                'title' => $request->title,
                'description' => $request->description,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'start' => $startDateTime,
                'end' => $endDateTime,
                'location' => $request->location,
                'status' => $request->status ?? 'draft',
                'event_type' => 'match',
                'event_data' => json_encode($request->event_data ?? []),
                'privacy' => 'public'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'event' => $event
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating event: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Event $event)
    {
        // Check if the event belongs to the user's club
        if ($event->club_id !== Auth::user()->club->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        return view('club.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        // Check if the event belongs to the user's club
        if ($event->club_id !== Auth::user()->club->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        return view('club.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        // Check if the event belongs to the user's club
        if ($event->club_id !== Auth::user()->club->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $request->validate([
            'eventname' => 'required|max:255',
            'eventdate' => 'required|date',
            'eventtime' => 'required',
            'eventlocation' => 'required',
        ]);

        $startDateTime = $request->eventdate . ' ' . $request->eventtime;

        $event->update([
            'title' => $request->eventname,
            'description' => $request->description,
            'event_date' => $request->eventdate,
            'event_time' => $request->eventtime,
            'start' => $startDateTime,
            'end' => date('Y-m-d H:i:s', strtotime($startDateTime) + 3600),
            'location' => $request->eventlocation,
            'privacy' => $request->privacy ?? 'public',
        ]);

        return redirect()->route('club.events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        // Check if the event belongs to the user's club
        if ($event->club_id !== Auth::user()->club->id) {
            abort(403, 'Unauthorized access to this event.');
        }

        $event->delete();

        return redirect()->route('club.events.index')->with('success', 'Event deleted successfully.');
    }

    public function invite(Request $request, Event $event)
    {
        $request->validate(['email' => 'required|email']);

        $invite = Invite::create([
            'sender_id' => Auth::id(),
            'receiver_email' => $request->email,
            'receiver_id' => null,
            'type' => 'event',
            'reference_id' => $event->id,
        ]);

        if (!$invite->receiver_id) {
            Mail::to($request->email)->send(new \App\Mail\InviteToJoin($invite));
        }

        return back()->with('success', 'Invitation sent.');
    }
}

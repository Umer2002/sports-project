<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invite;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    public function index()
    {
        $page_data['events'] = Event::whereNull('group_id')->orderBy('id', 'DESC')->limit(20)->get();
        // $page_data['view_path'] = 'frontend.events.events';
        return view('admin.events.index', $page_data);
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'coverphoto' => 'mimes:jpeg,jpg,png,gif|nullable',
            'eventname' => 'required|max:255',
            'eventdate' => 'required',
            'eventtime' => 'required',
            'eventlocation' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['validationError' => $validator->getMessageBag()->toArray()]);
        }

        if ($request->hasFile('coverphoto')) {
            $file_name = rand(1, 35000) . '.' . $request->coverphoto->getClientOriginalExtension();

            // Image::make($request->coverphoto)->resize(325, null, fn($c) => $c->aspectRatio()->upsize())->save(uploadTo('event/thumbnail') . $file_name);
            // Image::make($request->coverphoto)->resize(1120, null, fn($c) => $c->aspectRatio()->upsize())->save(uploadTo('event/coverphoto') . $file_name);
        }

        $event = new Event();
        $event->user_id = Auth::id();
        $event->title = $request->eventname;
        $event->description = $request->description;
        $event->event_date = $request->eventdate;
        $event->end = $request->eventdate;
        $event->start = $request->eventdate;
        $event->event_time = $request->eventtime;
        $event->location = $request->eventlocation;
        $event->group_id = $request->group_id ?? null;
        $event->banner = $file_name ?? null;
        $event->going_users_id = json_encode([]);
        $event->interested_users_id = json_encode([]);
        $event->privacy = $request->privacy;
        // $event->start = strtotime($request->eventdate.''. $request->eventtime) ?? null;
        // $event->end = strtotime($request->eventdate.''. $request->eventtime) ?? null;
        if ($event->save()) {
            Post::create([
                'user_id' => Auth::id(),
                'privacy' => $request->privacy,
                'publisher' => 'event',
                'publisher_id' => $event->id,
                'post_type' => 'event',
                'status' => 'active',
                'description' => $request->description,
                'user_reacts' => json_encode([]),
                'tagged_user_ids' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Session::flash('success_message', 'Event Created Successfully');
            // redirect to event page
            return redirect()->route('admin.events.index')->with('success', 'Event created successfully.');
        }
    }

    public function show(Event $event)
    {
        $page_data['event'] = $event;
        return view('admin.events.show', $page_data);
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $rules = [
            'coverphoto' => 'mimes:jpeg,jpg,png,gif|nullable',
            'eventname' => 'required|max:255',
            'eventdate' => 'required',
            'eventtime' => 'required',
            'eventlocation' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['validationError' => $validator->getMessageBag()->toArray()]);
        }

        $imagename = $event->banner;

        if ($request->hasFile('coverphoto')) {
            $file_name = rand(1, 35000) . '.' . $request->coverphoto->getClientOriginalExtension();
            // Image::make($request->coverphoto)->resize(325, null, fn($c) => $c->aspectRatio()->upsize())->save(uploadTo('event/thumbnail') . $file_name);
            // Image::make($request->coverphoto)->resize(1120, null, fn($c) => $c->aspectRatio()->upsize())->save(uploadTo('event/coverphoto') . $file_name);
            $event->banner = $file_name;
        }

        $event->user_id = Auth::id();
        $event->title = $request->eventname;
        $event->description = $request->description;
        $event->event_date = $request->eventdate;
        $event->event_time = $request->eventtime;
        $event->location = $request->eventlocation;
        $event->privacy = $request->privacy;
        $event->start = $request->start ?? null;
        $event->end = $request->end ?? null;
        if ($event->save()) {
            $this->removeFile('event', $imagename);
            Session::flash('success_message', 'Event Updated Successfully');
            return redirect()->route('admin.events.index')->with('success', 'Event Updated successfully.');
        }

    }

    public function destroy(Event $event)
    {
        $imagename = $event->banner;
        if ($event->delete()) {
            $this->removeFile('event', $imagename);
            Session::flash('success_message', 'Event Deleted Successfully');
            return redirect()->route('admin.events.index');
        }

    }
    public function removeFile($folderName, $imagename)
    {

        $filePath = public_path("uploads/$folderName/$imagename");
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
    public function userevent()
    {
        $page_data['events'] = Event::where('user_id', Auth::id())->whereNull('group_id')->orderBy('id', 'DESC')->get();
        $page_data['view_path'] = 'frontend.events.user_event';
        return view('frontend.index', $page_data);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:event,club,game',
            'reference_id' => 'required|integer',
        ]);

        $existingUser = User::where('email', $request->email)->first();

        // Check if invite already exists
        $existingInvite = Invite::where('receiver_email', $request->email)
            ->where('type', $request->type)
            ->where('reference_id', $request->reference_id)
            ->first();

        if ($existingInvite) {
            return back()->with('error', 'An invitation has already been sent to this user for this item.');
        }

        $invite = new Invite();
        $invite->sender_id = Auth::id();
        $invite->receiver_email = $request->email;
        $invite->receiver_id = $existingUser?->id;
        $invite->type = $request->type;
        $invite->reference_id = $request->reference_id;
        $invite->save();

        if (!$existingUser) {
            Mail::to($request->email)->send(new \App\Mail\InviteToJoin($invite));
        } else {
            Auth::user()->increment('invite_points'); // optional
        }

        return back()->with('success', 'Invitation sent successfully!');
    }

}

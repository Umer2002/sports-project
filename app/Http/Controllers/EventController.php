<?php
namespace App\Http\Controllers;
use App\Models\Event;

class EventController extends Controller
{
    public function fetch()
    {
        return Event::select('title', 'event_date as start')->get();
    }
}

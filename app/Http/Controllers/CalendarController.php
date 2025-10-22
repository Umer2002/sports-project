<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function calendar()
    {
        return view('players.calendar.calendar');
    }
}



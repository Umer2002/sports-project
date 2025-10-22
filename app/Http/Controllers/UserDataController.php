<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserDataController extends Controller
{
    public function index()
    {
        //
    }

    public function getUserData()
    {
        // If the route requires auth, make sure you’re logged in.
        return response()->json(auth()->user());
    }
}

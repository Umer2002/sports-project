<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class EmailController extends Controller
{
    public function email()
    {
        return view('players.email.email');
    }
}

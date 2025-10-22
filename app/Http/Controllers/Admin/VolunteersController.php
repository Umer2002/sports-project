<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Http\Request;

class VolunteersController extends Controller
{
    public function index()
    {
        // Fetch User with role_id = 4 (assuming this corresponds to Sponsors)
        $roleUser = RoleUser::where('role_id', 5)->pluck('user_id');

        // Fetch user details from the User model
        $users = User::whereIn('id', $roleUser)->get();

        // Add status column (Activated or Pending)
        foreach ($users as $user) {
            // Check if the user is activated
            // $user->status = Activation::completed($user) ? 'Activated' : 'Pending';
        }

        return view('admin.Volunteers.index', compact('users'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use App\Models\Users;
use App\Models\RoleUser;
use Illuminate\Http\Request;

class SponsorsController extends Controller
{
    public function index()
    {
        // Fetch users with role_id = 4 (assuming this corresponds to Sponsors)
        $roleUsers = RoleUser::where('role_id', 4)->pluck('user_id');

        // Fetch user details from the Users model
        $users = Users::whereIn('id', $roleUsers)->get();

        // Add status column (Activated or Pending)
        foreach ($users as $user) {
            // Check if the user is activated
            $user->status = Activation::completed($user) ? 'Activated' : 'Pending';
        }

        // Pass users to the view
        return view('admin.Sponsors.index', compact('users'));
    }
}

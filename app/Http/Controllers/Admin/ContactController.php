<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Users;
use App\Models\RoleUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // Fetch user IDs based on the roles
        $userIds = RoleUser::whereIn('role_id', [4, 5, 6, 7, 8, 9])
            ->pluck('user_id');  // Get the user IDs for the given role IDs

        // Fetch users based on the user IDs from the RoleUser table
        $users = Users::whereIn('id', $userIds)  // Use user IDs to fetch data from the Users table
            ->when($request->role, function ($query, $role) {
                // Optionally filter by specific role if needed
                $query->whereHas('RoleUser', function ($query) use ($role) {
                    $query->where('role_id', $role); // Filter by a specific role
                });
            })
            ->paginate(10);

        // Add the club_name (first_name from the Club table) dynamically to each user in the collection
        $users->getCollection()->transform(function ($user) {
            // Query the Club table manually using the club_id of the current user
            $club = DB::table('users')->where('id', $user->club_id)->first(); // Assuming 'clubs' is the table name
            $user->club_name = $club ? $club->first_name : null;  // If a club is found, get the first_name
            return $user;
        });

        return view('admin.contacts.index', compact('users'));
    }
    public function filterContacts(Request $request)
    {
        // Start with the base query for Users with roles
        $query = Users::query()->with('roles');  // Ensure roles are loaded

        // Check if any filter is applied
        $isFiltered = false;

        // Filter by first_name (which is used for both Name and Club search)
        if ($request->has('first_name') && !empty($request->first_name)) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
            $isFiltered = true;
        }

        // Filter by Role (based on the role ID selected in the dropdown)
        if ($request->has('role') && !empty($request->role)) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('id', $request->role);
            });
            $isFiltered = true;
        }

        // Filter by associated_club (also using first_name as club name)
        if ($request->has('associated_club') && !empty($request->associated_club)) {
            // First, get the IDs of users that match the associated_club
            $userIds = Users::where('first_name', 'like', '%' . $request->associated_club . '%')->pluck('id');
            // Filter the users query by the club_id, ensuring the roles are loaded correctly
            $query->whereIn('club_id', $userIds);
            $isFiltered = true;
        }

        // If no filters are applied, fetch users based on predefined roles
        if (!$isFiltered) {
            // Fetch user IDs based on the roles (4, 5, 6, 7, 8, 9)
            $userIds = RoleUser::whereIn('role_id', [4, 5, 6, 7, 8, 9])
                ->pluck('user_id');  // Get the user IDs for the given role IDs

            // Fetch users based on the user IDs from the RoleUser table
            $query = Users::whereIn('id', $userIds);

            // Optionally, you can still filter by a specific role if needed
            if ($request->has('role') && !empty($request->role)) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->where('id', $request->role); // Filter by a specific role if specified
                });
            }
        }

        // Fetch the users with the applied filters and paginate
        $users = $query->paginate(10); // Adjust pagination limit as necessary

        // Load roles relationship if necessary (if it's not loaded already)
        $users->load('roles');

        // Dynamically add 'club_name' for all users (whether or not 'associated_club' is filtered)
        $users->getCollection()->transform(function ($user) {
            // Manually add the club_name (first_name from the Club table)
            $club = DB::table('users')->where('id', $user->club_id)->first(); // Assuming 'users' is the table name
            $user->club_name = $club ? $club->first_name : null;  // If a club is found, get the first_name
            return $user;
        });

        return view('admin.contacts.index', compact('users')); // Return view with paginated users
    }

}

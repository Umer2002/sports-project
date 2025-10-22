<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show all User
    public function index()
    {
        $users = User::with(['roles', 'club', 'player', 'coach'])->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function lockScreen($id)
    {
        // return view('admin.users.lock-screen');
    }
    // Show form to create user
    public function create()
    {
        $clubs = Club::all();
        $players = Player::all();
        $coaches = Coach::all();
        $roles = Role::all();
        return view('admin.users.create', compact('clubs', 'players', 'coaches', 'roles'));
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'club_id' => 'nullable|exists:clubs,id',
            'player_id' => 'nullable|exists:players,id',
            'coach_id' => 'nullable|exists:coaches,id',
            // 'is_admin' => 'required|boolean|nullable',
        ]);

        User::create([
            'name' => $request->first_name. ' '. $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'club_id' => $request->club_id,
            'player_id' => $request->player_id,
            'coach_id' => $request->coach_id,
            // 'is_admin' => $request->is_admin,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    // Show user detail
    public function show($id)
    {
        $user = User::with(['club', 'player', 'coach'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    // Show edit form
    public function edit($id)
    {
        $user = User::with(['club', 'player', 'coach'])->findOrFail($id);

        $clubs = Club::pluck('name', 'id');
        $players = Player::pluck('full_name', 'id');
        $coaches = Coach::pluck('full_name', 'id');
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id', 'id')->toArray();

        return view('admin.users.edit', compact('user', 'clubs', 'players', 'coaches', 'roles', 'userRoles'));
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:User,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'club_id' => 'nullable|exists:clubs,id',
            'player_id' => 'nullable|exists:players,id',
            'coach_id' => 'nullable|exists:coaches,id',
            'is_admin' => 'required|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'club_id' => $request->club_id,
            'player_id' => $request->player_id,
            'coach_id' => $request->coach_id,
            'is_admin' => $request->is_admin,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    // Delete user
    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function data()
    {
        $User = User::with(['club', 'player', 'coach'])->get();
    }
}

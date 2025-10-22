<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClubsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeVolunteer();
        $q = $request->input('q');
        $ambSportId = optional(Auth::user())->sport_id;

        $baseQuery = Club::query()
            ->when($q, function($query) use ($q) {
                $query->where(function($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($ambSportId, function($query) use ($ambSportId) {
                $query->where('sport_id', $ambSportId);
            });

        // Total players across filtered clubs
        $totalPlayers = (clone $baseQuery)->withCount('players')->get()->sum('players_count');

        // Paginated list with per-club player count
        $clubs = $baseQuery
            ->withCount('players')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('volunteer.clubs.index', compact('clubs', 'q', 'totalPlayers'));
    }

    public function importForm()
    {
        $this->authorizeVolunteer();
        return view('volunteer.clubs.import');
    }

    public function import(Request $request)
    {
        $this->authorizeVolunteer();
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
            'sport_id' => 'nullable|integer',
            'send_invites' => 'nullable|boolean',
        ]);

        $file = $request->file('csv');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $created = 0; $skipped = 0;
        $ambSportId = Auth::user()->sport_id; // Ambassador's sport overrides provided sport
        $effectiveSportId = $ambSportId ?: $request->integer('sport_id');
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if (!isset($data['name']) || !isset($data['email'])) { $skipped++; continue; }

            $club = Club::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'sport_id' => $effectiveSportId,
                    'is_registered' => 1,
                ]
            );

            // Ensure linked user account exists
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt(Str::random(12)),
                    'club_id' => $club->id,
                ]
            );
            if (!$user->club_id) { $user->update(['club_id' => $club->id]); }

            // Attach club role
            if ($role = Role::where('name','club')->first()) {
                if (!$user->roles()->where('roles.id', $role->id)->exists()) {
                    $user->roles()->attach($role->id);
                }
            }

            $created++;
        }
        fclose($handle);

        // Optionally send invites after import
        if ($request->boolean('send_invites')) {
            // We'll just report; the volunteer can trigger reminders per club in UI
        }

        return redirect()->route('volunteer.clubs.index')
            ->with('success', "Imported {$created} clubs, skipped {$skipped} rows.");
    }

    public function exportLogins(Request $request): StreamedResponse
    {
        $this->authorizeVolunteer();
        $clubs = Club::with('user')->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="club_logins.csv"',
        ];

        return response()->stream(function() use ($clubs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['club_name', 'email', 'set_password_url']);
            foreach ($clubs as $club) {
                if (!$club->user) continue;
                $token = Password::broker()->createToken($club->user);
                $url = url(route('password.reset', ['token' => $token, 'email' => $club->user->email], false));
                fputcsv($out, [$club->name, $club->user->email, $url]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function resendInvite(Request $request, Club $club)
    {
        $this->authorizeVolunteer();
        $request->validate(['email' => 'nullable|email']);
        $email = $request->input('email', $club->email);

        // Proxy to existing invite sender with force
        $proxy = app(\App\Http\Controllers\InviteLinkController::class);
        $proxyRequest = Request::create('/send-invite-email', 'POST', [
            'email' => $email,
            'force' => true,
            'type' => 'club',
            'reference_id' => $club->id,
        ]);
        $proxyRequest->headers->set('Accept', 'application/json');
        $proxyRequest->setUserResolver(fn() => Auth::user());
        $response = $proxy->sendInviteEmail($proxyRequest);
        if (method_exists($response, 'getData')) {
            $data = $response->getData(true);
            return redirect()->back()->with('success', $data['msg'] ?? 'Invite sent');
        }
        return redirect()->back()->with('success', 'Invite sent');
    }

    private function authorizeVolunteer(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('volunteer'), 403);
    }
}

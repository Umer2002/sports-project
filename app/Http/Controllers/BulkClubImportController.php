<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Response;

class BulkClubImportController extends Controller
{
    /**
     * Display the upload form for bulk club import.
     */
    public function showForm(): View
    {
        return view('admin.bulk-clubs.import');
    }

    /**
     * Import clubs from an uploaded CSV file and create login credentials.
     */
    public function import(Request $request): RedirectResponse
    {
        // Allow long-running imports for large CSVs
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        
        // Open CSV efficiently (streaming) to avoid loading entire file into memory
        $file = new \SplFileObject($request->file('csv_file')->getRealPath());
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);

        $header = null;

        // Prepare role once (avoid per-row firstOrCreate)
        $clubRole = Role::firstOrCreate(['name' => 'club']);

        // Prepare output CSV writer
        $loginsPath = Storage::disk('local')->path('club_logins.csv');
        $fh = fopen($loginsPath, 'w');
        fputcsv($fh, ['Club Name', 'Email', 'Password']);

        foreach ($file as $row) {
            if ($row === [null] || $row === false) {
                continue; // skip empty lines
            }

            if ($header === null) {
                $header = array_map('trim', $row);
                continue;
            }

            // Normalize row length to header
            $row = array_pad($row, count($header), null);
            $data = array_combine($header, $row);
            $rawEmail = (string)($data['Email'] ?? '');
            $email = $this->extractFirstEmail($rawEmail);
            $name = trim((string)($data['Club Name'] ?? ''));
            if (!$email || $name === '' || User::where('email', $email)->exists()) {
                // Skip rows with no valid primary email, empty name, or duplicate user
                continue;
            }

            $password = Str::random(10);

            // 1) Create the user first (so we have an owner for the club)
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            // 2) Create the club linked to this user
            $club = Club::create([
                'name' => $name,
                'logo' => $data['Club Logo'] ?? null,
                'address' => $data['Address'] ?? null,
                'phone' => is_string($data['Phone No'] ?? null)
                    ? trim(explode(',', $data['Phone No'])[0])
                    : null,
                'email' => $email,
                // Store as JSON array to satisfy cast; CSV often contains malformed JSON-like text
                'social_links' => [],
                'user_id' => $user->id,
            ]);

            // 3) Link user back to the club (if applicable in your schema)
            $user->update(['club_id' => $club->id]);

            // 4) Attach role
            $user->roles()->attach($clubRole->id);

            // 5) Write login line incrementally to avoid memory growth
            fputcsv($fh, [$name, $email, $password]);
        }

        if (is_resource($fh)) {
            fclose($fh);
        }

        return redirect()->route('bulk-clubs.import.complete');
    }

    /**
     * Show completion page with download link.
     */
    public function complete(): View
    {
        return view('admin.bulk-clubs.import-complete');
    }

    /**
     * Export the generated login credentials as a CSV file.
     */
    public function export(): Response
    {
        if (!Storage::disk('local')->exists('club_logins.csv')) {
            return response('No logins file found', 404);
        }

        return response(
            Storage::disk('local')->get('club_logins.csv'),
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="club_logins.csv"',
            ]
        );
    }

    /**
     * Extract the first valid email from a potentially comma/semicolon-separated string.
     */
    private function extractFirstEmail(string $raw): ?string
    {
        $parts = preg_split('/[\s,;]+/', $raw);
        foreach ($parts as $part) {
            $candidate = trim($part, "<>\"'()");
            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return strtolower($candidate);
            }
        }
        return null;
    }
}

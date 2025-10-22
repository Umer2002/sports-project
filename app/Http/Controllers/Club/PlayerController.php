<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Club;
use App\Models\Position;
use App\Models\Invite;
use App\Models\Team;
use App\Mail\PlayerInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PlayerController extends Controller
{
    public function index()
    {
        $club = auth()->user()->club;
        $players = $club->players()->with('user')->paginate(15);
        
        return view('club.players.index', compact('players'));
    }

    public function invite()
    {
        $club = auth()->user()->club;
        $positions = Position::where('sports_id', $club->sport_id)->get();

        $invites = Invite::where('type', 'club_invite')
            ->where('reference_id', $club->id)
            ->latest()
            ->get();

        $invites->each(function (Invite $invite) {
            $invite->generateToken();
        });
        
        return view('club.players.invite', compact('positions', 'invites'));
    }

    public function storeInvite(Request $request)
    {
        $club = auth()->user()->club;

        $validated = $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email',
            'positions' => 'nullable|array',
            'positions.*' => 'nullable|exists:positions,id',
            'personal_message' => 'nullable|string|max:1000',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $emails = $validated['emails'];
        $positions = $validated['positions'] ?? [];
        $personalMessage = $validated['personal_message'] ?? null;
        $teamId = $validated['team_id'] ?? null;
        $team = null;

        if ($teamId) {
            $team = Team::where('id', $teamId)
                ->where('club_id', $club->id)
                ->first();

            if (! $team) {
                return back()->withInput()->withErrors(['team_id' => 'You can only invite players to teams registered under your club.']);
            }
        }

        $invitedCount = 0;
        $errors = [];
        $generatedInviteLinks = [];

        foreach ($emails as $index => $email) {
            try {
                // Check if player already exists
                $existingPlayer = Player::where('email', $email)->first();
                
                if ($existingPlayer) {
                    if ($existingPlayer->club_id && $existingPlayer->club_id !== $club->id) {
                        $errors[] = "{$email} is already registered with another club.";
                        continue;
                    }

                    if (! $existingPlayer->club_id) {
                        $existingPlayer->update(['club_id' => $club->id]);
                    }

                    if ($team && ! $team->players()->where('players.id', $existingPlayer->id)->exists()) {
                        $team->players()->attach($existingPlayer->id, [
                            'sport_id' => $team->sport_id,
                            'position_id' => $positions[$index] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $invitedCount++;
                } else {
                    // Create invitation for new player
                    $invite = Invite::updateOrCreate([
                        'receiver_email' => $email,
                        'type' => 'club_invite',
                        'reference_id' => $club->id,
                    ], [
                        'sender_id' => auth()->id(),
                    ]);

                    $metadata = array_merge($invite->metadata ?? [], [
                        'club_name' => $club->name,
                        'sport_name' => optional($club->sport)->name,
                        'preferred_position' => $positions[$index] ?? null,
                        'personal_message' => $personalMessage,
                        'team_id' => $team?->id,
                        'team_name' => $team?->name,
                    ]);

                    $invite->fill([
                        'metadata' => $metadata,
                        'is_accepted' => $invite->is_accepted ?? false,
                    ]);
                    $invite->generateToken();
                    $invite->save();

                    $registerLink = $invite->getClubPlayerRegistrationLink();
                    if (! $registerLink) {
                        $registerLink = route('register.player', [
                            'club' => $club->id,
                            'sport' => optional($club->sport)->id,
                            'invite_token' => $invite->token,
                        ]);
                    }

                    $generatedInviteLinks[] = [
                        'email' => $email,
                        'link' => $registerLink,
                    ];

                    $teamMessage = $team ? "\n\nThey would like you to join the team {$team->name}." : '';
                    $messageForMail = $personalMessage ? $personalMessage . $teamMessage : ($team ? "We're excited for you to join our team {$team->name}!" : null);

                    try {
                        Mail::to($email)->queue(new PlayerInvitation($club, $messageForMail, null, $registerLink));
                        $invite->forceFill([
                            'email_sent_at' => now(),
                            'email_last_attempt_at' => now(),
                            'email_attempts' => ($invite->email_attempts ?? 0) + 1,
                        ])->save();
                        $invitedCount++;
                    } catch (\Throwable $mailException) {
                        report($mailException);
                        $errors[] = "Failed to send invitation email to {$email}.";
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to invite {$email}: " . $e->getMessage();
            }
        }

        if ($invitedCount > 0) {
            $message = "Successfully sent {$invitedCount} invitation(s).";
            if (!empty($errors)) {
                $message .= " " . implode(' ', $errors);
            }

            if (!empty($generatedInviteLinks)) {
                session()->flash('generated_invite_links', $generatedInviteLinks);
            }

            if ($team) {
                $message .= " Invite links are available on your player invites page.";
            }

            return redirect()->route('club.players.invite')->with('success', $message);
        } else {
            return redirect()->back()->withInput()->with('error', 'No invitations were sent. ' . implode(' ', $errors));
        }
    }

    public function storeBulkInvites(Request $request)
    {
        $club = auth()->user()->club;

        $validated = $request->validate([
            'bulk_file' => 'required|file|mimes:csv,txt|max:10240',
            'bulk_personal_message' => 'nullable|string|max:1000',
        ]);

        $bulkFile = $request->file('bulk_file');

        [$rows, $parseErrors] = $this->parseBulkInviteCsv($bulkFile->getRealPath());

        if (!empty($parseErrors) && empty($rows)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($parseErrors);
        }

        $personalMessage = $validated['bulk_personal_message'] ?? null;

        $processedEmails = [];
        $errors = $parseErrors;
        $invitedCount = 0;
        $skippedExistingClub = 0;
        $skippedOtherClub = 0;
        $claimedPlayers = 0;
        $now = Carbon::now();

        foreach ($rows as $row) {
            $rowNumber = $row['_row'];
            $email = strtolower($row['player_email'] ?? '');
            $name = $row['name'] ?? '';

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNumber}: Invalid email address.";
                continue;
            }

            if (empty($name)) {
                $errors[] = "Row {$rowNumber}: Name column is required.";
                continue;
            }

            if (in_array($email, $processedEmails, true)) {
                $errors[] = "Row {$rowNumber}: Duplicate email {$email} within uploaded file.";
                continue;
            }
            $processedEmails[] = $email;

            $existingPlayer = Player::where('email', $email)->first();

            if ($existingPlayer) {
                if ($existingPlayer->club_id === $club->id) {
                    $skippedExistingClub++;
                    continue;
                }

                if ($existingPlayer->club_id && $existingPlayer->club_id !== $club->id) {
                    $errors[] = "Row {$rowNumber}: {$email} is already registered with another club.";
                    $skippedOtherClub++;
                    continue;
                }

                // Player exists without club, associate with current club
                $existingPlayer->update(['club_id' => $club->id]);
                $claimedPlayers++;
                continue;
            }

            $invite = Invite::firstOrNew([
                'receiver_email' => $email,
                'type' => 'club_invite',
                'reference_id' => $club->id,
            ]);
            $invite->sender_id = auth()->id();
            if ($invite->is_accepted === null) {
                $invite->is_accepted = false;
            }

            $metadata = array_merge($invite->metadata ?? [], [
                'club_name' => $club->name,
                'sport_name' => optional($club->sport)->name,
                'player_name' => $name,
                'source' => 'bulk_upload',
                'uploaded_at' => $now->toIso8601String(),
                'personal_message' => $personalMessage,
            ]);

            if (! $invite->token) {
                $invite->token = Str::uuid();
            }

            $invite->metadata = $metadata;
            $invite->email_sent_at = null;
            $invite->email_last_attempt_at = null;
            $invite->email_attempts = 0;
            $invite->save();

            $invitedCount++;
        }

        $messages = [];
        if ($invitedCount > 0) {
            $messages[] = "Saved {$invitedCount} bulk invitation" . ($invitedCount === 1 ? '' : 's') . " for scheduled delivery.";
        }

        if ($claimedPlayers > 0) {
            $messages[] = "Linked {$claimedPlayers} existing player" . ($claimedPlayers === 1 ? '' : 's') . " without a club to your roster.";
        }

        if ($skippedExistingClub > 0) {
            $messages[] = "Skipped {$skippedExistingClub} player" . ($skippedExistingClub === 1 ? '' : 's') . " already associated with your club.";
        }

        if ($skippedOtherClub > 0) {
            $messages[] = "Found {$skippedOtherClub} player" . ($skippedOtherClub === 1 ? '' : 's') . " registered with another club.";
        }

        if (empty($messages)) {
            $messages[] = 'No invitations were sent.';
        }

        $redirect = redirect()->route('club.players.invite')->with('success', implode(' ', $messages));

        if (!empty($errors)) {
            $redirect->with('bulk_errors', $errors);
        }

        return $redirect;
    }

    private function parseBulkInviteCsv(string $absolutePath): array
    {
        $rows = [];
        $errors = [];

        if (!is_readable($absolutePath)) {
            return [[], ['Uploaded file could not be read.']];
        }

        $handle = fopen($absolutePath, 'rb');
        if ($handle === false) {
            return [[], ['Unable to open uploaded file.']];
        }

        $headers = null;
        $lineNumber = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if ($lineNumber === 1) {
                $headers = array_map(static function ($header) {
                    $header = $header ?? '';
                    $header = ltrim($header, "\xEF\xBB\xBF");
                    $header = strtolower(trim($header));
                    $header = str_replace([' ', '-'], '_', $header);

                    return match ($header) {
                        'email', 'playeremail' => 'player_email',
                        'player_name', 'full_name', 'playerfullname' => 'name',
                        default => $header,
                    };
                }, $data);

                $requiredColumns = ['player_email', 'name'];
                $missingColumns = array_diff($requiredColumns, $headers);

                if (!empty($missingColumns)) {
                    $errors[] = 'Missing required columns: ' . implode(', ', $missingColumns) . '. Expected headers: player_email, name.';
                    break;
                }

                continue;
            }

            if ($headers === null) {
                $errors[] = 'CSV header row is missing.';
                break;
            }

            if ($data === [null]) {
                continue;
            }

            $hasContent = false;
            foreach ($data as $value) {
                if (trim((string) $value) !== '') {
                    $hasContent = true;
                    break;
                }
            }

            if (!$hasContent) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = isset($data[$index]) ? trim((string) $data[$index]) : null;
            }

            $row['_row'] = $lineNumber;
            $rows[] = $row;

            if (count($rows) > 500) {
                array_pop($rows);
                $errors[] = 'CSV may contain at most 500 player rows per upload. Please split your file and try again.';
                break;
            }
        }

        fclose($handle);

        if (empty($rows) && empty($errors)) {
            $errors[] = 'CSV file does not contain any data rows.';
        }

        return [$rows, $errors];
    }

    public function show(Player $player)
    {
        // Ensure player belongs to current club
        $this->authorizeClubPlayer($player);
        $player->load(['teams.club', 'sport', 'position', 'user']);

        return view('club.players.show', compact('player'));
    }

    public function edit(Player $player)
    {
        // Ensure player belongs to current club
        $this->authorizeClubPlayer($player);
        return view('admin.players.edit', compact('player'));
    }

    public function update(Request $request, Player $player)
    {
        // Ensure player belongs to current club
        $this->authorizeClubPlayer($player);

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'email' => 'required|email|unique:players,email,' . $player->id,
            'phone' => 'required|string|max:191',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('players/photos', 'public');
        }

        $player->update($data);

        // Update user name if changed
        if ($player->user) {
            $player->user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
            ]);
        }

        return redirect()->route('club.players.index')->with('success', 'Player updated successfully!');
    }

    public function destroy(Player $player)
    {
        // Ensure player belongs to current club
        $this->authorizeClubPlayer($player);

        // Delete user account
        if ($player->user) {
            $player->user->delete();
        }

        // Delete player profile
        $player->delete();

        return redirect()->route('club.players.index')->with('success', 'Player deleted successfully!');
    }

    private function authorizeClubPlayer(Player $player)
    {
        $club = auth()->user()->club;
        if ($player->club_id !== $club->id) {
            abort(403, 'Unauthorized access to this player.');
        }
    }
}

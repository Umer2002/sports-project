<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ClubPlayerRegistration;
use App\Models\City;
use App\Models\Club;
use App\Models\ClubInvite;
use App\Models\Coach;
use App\Models\CollegeUniversity;
use App\Models\Country;
use App\Models\Invite;
use App\Models\Player;
use App\Models\Referee;
use App\Models\Role;
use App\Models\Sport;
use App\Models\State;
use App\Models\TournamentRegistration;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration landing view.
     */
    public function create(Request $request): View
    {
        $sports    = Sport::orderBy('name')->get();
        $clubs     = Club::where('is_registered', 1)->orderBy('name')->get();
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return view('auth.register', [
            'sports'    => $sports,
            'clubs'     => $clubs,
            'countries' => $countries,
        ]);
    }

    public function createPlayer(Request $request): View
    {
        $sports = Sport::orderBy('name')->get();
        $clubs  = Club::where('is_registered', 1)->orderBy('name')->get();

        $inviteToken = $request->query('invite_token') ?: session('pending_player_invite_token');
        $clubInviter = null;
        $emailInvited = null;

        $is_club = false;
        if ($inviteToken) {
            $invite = Invite::where('token', $inviteToken)->first();

            if ($invite && $invite->type === 'club_invite') {
                $clubInviter = Club::find($invite->reference_id);
                $is_club = true;
                                $emailInvited = $invite->receiver_email;

            }

            if ($invite && in_array($invite->type, ['player', 'player_free'], true)) {
                $emailInvited = $invite->receiver_email;
            }
        }

        $selectedClubId = $clubInviter?->id ?? $request->integer('club');
        $selectedSportId = $request->integer('sport') ?: $clubInviter?->sport_id;

        return view('auth.register-player', [
            'sports'       => $sports,
            'clubs'        => $clubs,
            'invitation'   => $clubInviter,
            'ref'          => $request->ref,
            'selectedClub' => $selectedClubId,
            'selectedSport'=> $selectedSportId,
            'inviteToken'  => $inviteToken,
            'is_club'      => $is_club,
            'emailInvited' => $emailInvited,
        ]);
    }

    public function createClub(Request $request): View
    {
        $sports    = Sport::orderBy('name')->get();
        $countries = Country::orderBy('name')->get(['id', 'name']);

        $selectedCountryId = session()->getOldInput('club_country_id');
        $states            = $selectedCountryId
            ? State::where('country_id', $selectedCountryId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedStateId = session()->getOldInput('club_state_id');
        $cities          = $selectedStateId
            ? City::where('state_id', $selectedStateId)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('auth.register-club', [
            'sports'      => $sports,
            'countries'   => $countries,
            'states'      => $states,
            'cities'      => $cities,
            'inviteToken' => $request->query('invite_token') ?: session('pending_tournament_invite_token'),
        ]);
    }

    public function createAmbassador(): View
    {
        $sports = Sport::orderBy('name')->get();

        return view('auth.register-ambassador', [
            'sports' => $sports,
        ]);
    }

    public function createCollege(): View
    {
        return view('auth.register-college');
    }

    public function createCoach(): View
    {
        $sports = Sport::orderBy('name')->get();

        return view('auth.register-coach', [
            'sports' => $sports,
        ]);
    }

    public function clubsForSport(Request $request): JsonResponse
    {
        $sportId = $request->input('sport');

        $clubs = Club::query()
            ->where('is_registered', 1)
            ->when($sportId, function ($query) use ($sportId) {
                $query->where(function ($innerQuery) use ($sportId) {
                    $innerQuery->whereNull('sport_id')
                        ->orWhere('sport_id', $sportId);
                });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'sport_id']);

        return response()->json([
            'data' => $clubs,
        ]);
    }

    public function statesForCountry(Request $request): JsonResponse
    {
        $countryId = $request->integer('country_id') ?? $request->integer('country');

        if (! $countryId) {
            return response()->json(['data' => []]);
        }

        $states = State::query()
            ->where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']);
        // dd($states);
        return response()->json(['data' => $states]);
    }

    public function citiesForState(Request $request): JsonResponse
    {
        $stateId = $request->integer('state_id') ?? $request->integer('state');

        if (! $stateId) {
            return response()->json(['data' => []]);
        }

        $cities = City::query()
            ->where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['data' => $cities]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request, ?string $userType = null): RedirectResponse
    {
        $type = $userType ?? $request->input('user_type');

        if (! $type) {
            return redirect()->route('register')->withErrors(['user_type' => 'Please choose a registration type.']);
        }

        return $this->processRegistration($request, $type);
    }

    public function storePlayer(Request $request): RedirectResponse
    {

        return $this->store($request, 'player');
    }

    public function storeClub(Request $request): RedirectResponse
    {
        return $this->store($request, 'club');
    }

    public function storeAmbassador(Request $request): RedirectResponse
    {
        return $this->store($request, 'ambassador');
    }

    public function storeCollege(Request $request): RedirectResponse
    {
        return $this->store($request, 'college');
    }

    public function storeCoach(Request $request): RedirectResponse
    {
        return $this->store($request, 'coach');
    }

    protected function processRegistration(Request $request, string $userType): RedirectResponse
    {
        $data                   = $request->all();
        $tournamentRegistration = null;

        $rules = [
            'first_name' => 'required|string|max:191',
            'last_name'  => 'required|string|max:191',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|confirmed|min:6',
        ];

        switch ($userType) {
            case 'player':
                $rules = array_merge($rules, [
                    'sport'               => 'required|exists:sports,id',
                    'club_selection_mode' => 'nullable|in:existing,new',
                    'club'                => [
                        'nullable',
                        function ($attribute, $value, $fail) use ($request) {
                            $mode = $request->input('club_selection_mode', 'existing');
                            if ($mode === 'new') {
                                if ($value && $value !== '__new__') {
                                    $fail('Please choose a club from the list or add a new club.');
                                }
                                return;
                            }

                            if ($value && ! Club::whereKey($value)->exists()) {
                                $fail('The selected club is invalid.');
                            }
                        },
                    ],
                    'new_club_name'       => 'nullable|string|max:191',
                    'new_club_email'      => 'nullable|email|max:191',
                    'new_club_address'    => 'nullable|string|max:500',
                    'new_club_phone'      => 'nullable|string|max:191',
                    'new_club_website'    => 'nullable|url|max:191',
                    'new_club_place_id'   => 'nullable|string|max:191',
                    'dob'                 => 'required|date|before_or_equal:today',
                    'college'             => 'nullable|string|max:191',
                    'university'          => 'nullable|string|max:191',
                    'referee_affiliation' => 'nullable|string|max:191',
                    'guardian_first_name' => 'nullable|string|max:191',
                    'guardian_last_name'  => 'nullable|string|max:191',
                    'guardian_email'      => 'nullable|email|max:191',
                ]);
                break;
            case 'club':
                $rules = array_merge($rules, [
                    'sport'           => 'required|exists:sports,id',
                    'club_name'       => 'required|string|max:191',
                    'club_country_id' => 'required|exists:countries,id',
                    'club_state_id'   => [
                        'required',
                        Rule::exists('states', 'id')->where(function ($query) use ($request) {
                            $countryId = $request->integer('club_country_id');
                            if ($countryId) {
                                $query->where('country_id', $countryId);
                            }
                        }),
                    ],
                    'club_city_id'    => [
                        'required',
                        Rule::exists('cities', 'id')->where(function ($query) use ($request) {
                            $stateId = $request->integer('club_state_id');
                            if ($stateId) {
                                $query->where('state_id', $stateId);
                            }
                        }),
                    ],
                    'invite_token'    => 'nullable|string|max:191',
                ]);
                break;
            case 'college':
                $rules = array_merge($rules, [
                    'college_name' => 'required|string|max:191',
                ]);
                break;
            case 'coach':
                $rules = array_merge($rules, [
                    'sport_id'         => 'required|exists:sports,id',
                    'gender'           => 'required|in:male,female,other',
                    'country_id'       => 'required|exists:countries,id',
                    'city_id'          => 'required|exists:cities,id',
                    'phone'            => 'nullable|string|max:20',
                    'bio'              => 'nullable|string|max:1000',
                    'experience_years' => 'nullable|integer|min:0',
                ]);
                break;
            case 'volunteer':
            case 'ambassador':
                $rules = array_merge($rules, [
                    'sport' => 'nullable|exists:sports,id',
                ]);
                break;
            case 'referee':
                $rules = array_merge($rules, [
                    'club'  => 'nullable|exists:clubs,id',
                    'sport' => 'nullable|exists:sports,id',
                ]);
                break;
        }

        $validator = Validator::make($data, $rules);

        if ($userType === 'player') {
            $validator->sometimes('guardian_first_name', 'required|string|max:191', function ($input) {
                return $this->requiresGuardian($input->dob ?? null);
            });
            $validator->sometimes('guardian_last_name', 'required|string|max:191', function ($input) {
                return $this->requiresGuardian($input->dob ?? null);
            });
            $validator->sometimes('guardian_email', 'required|email|max:191', function ($input) {
                return $this->requiresGuardian($input->dob ?? null);
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $userData = [
                'name'     => trim($request->first_name . ' ' . $request->last_name),
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ];

            $user = User::create($userData);

            $roleName = match ($userType) {
                'club'    => 'club',
                'coach'   => 'coach',
                'referee' => 'referee',
                'college' => 'college',
                'volunteer', 'ambassador' => 'volunteer',
                default   => 'player',
            };

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            if ($userType === 'club') {
                $clubName  = trim($request->club_name);
                $clubEmail = $request->email;
                $clubSlug  = Str::slug($clubName);

                $existingClub = Club::query()
                    ->where(function ($query) use ($clubName, $clubSlug, $clubEmail) {
                        $query->where('slug', $clubSlug)
                            ->orWhere('name', $clubName);

                        if ($clubEmail) {
                            $query->orWhere('email', $clubEmail);
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                $socialLinks                        = (array) ($existingClub?->social_links ?? []);
                $socialLinks['registration_source'] = 'club_signup';

                $clubAttributes = [
                    'name'          => $clubName,
                    'email'         => $clubEmail,
                    'user_id'       => $user->id,
                    'sport_id'      => $request->integer('sport'),
                    'country_id'    => $request->integer('club_country_id'),
                    'state_id'      => $request->integer('club_state_id'),
                    'city_id'       => $request->integer('club_city_id'),
                    'social_links'  => $socialLinks,
                    'is_registered' => true,
                ];

                if ($existingClub) {
                    $existingClub->fill($clubAttributes);
                    $existingClub->save();
                    $club = $existingClub;
                } else {
                    $club = Club::create($clubAttributes);
                }

                $user->update(['club_id' => $club->id]);

                $inviteToken = $request->input('invite_token') ?: session('pending_tournament_invite_token');

                if ($inviteToken) {
                    $invite = ClubInvite::where('token', $inviteToken)
                        ->with('tournament')
                        ->lockForUpdate()
                        ->first();

                    if ($invite && $invite->tournament) {
                        $invite->inviter_club_id    = $invite->inviter_club_id ?: $invite->tournament->host_club_id;
                        $invite->registered_club_id = $club->id;
                        $invite->registered_at      = now();
                        $invite->status             = ClubInvite::STATUS_REGISTERED;
                        if (! $invite->accepted_at) {
                            $invite->accepted_at = now();
                        }
                        $invite->save();

                        $invite->scheduleReward(1000, $invite->registered_at);

                        $tournament = $invite->tournament;

                        $tournamentRegistration = TournamentRegistration::updateOrCreate(
                            [
                                'tournament_id' => $tournament->id,
                                'club_id'       => $club->id,
                            ],
                            [
                                'club_invite_id' => $invite->id,
                                'status'         => TournamentRegistration::STATUS_PENDING_PAYMENT,
                                'joining_type'   => $tournament->joining_type ?? 'per_club',
                                'joining_fee'    => $tournament->joining_fee ?? 0,
                                'team_quantity'  => $tournament->joining_type === 'per_team' ? 0 : 1,
                                'amount_due'     => 0,
                                'amount_paid'    => 0,
                                'metadata'       => ['source' => 'invite'],
                            ]
                        );
                    }
                }

                session()->forget('pending_tournament_invite_token');
            }

            if ($userType === 'player') {
                $dob = $request->filled('dob') ? Carbon::parse($request->dob) : null;

                $clubMode = $request->input('club_selection_mode', 'existing');
                $club     = null;
                $clubId   = null;

                $inviteToken  = trim((string) ($request->input('invite_token', session('pending_player_invite_token'))));
                $inviteRecord = null;

                if ($inviteToken !== '') {
                    $inviteRecord = Invite::where('token', $inviteToken)
                        ->whereIn('type', ['player', 'player_free', 'club_invite', 'event'])
                        ->lockForUpdate()
                        ->first();

                    if ($inviteRecord && $inviteRecord->type === 'club_invite' && $inviteRecord->reference_id) {
                        $clubFromInvite = Club::find($inviteRecord->reference_id);
                        if ($clubFromInvite) {
                            $club     = $clubFromInvite;
                            $clubId   = $clubFromInvite->id;
                            $clubMode = 'existing';
                        }
                    }
                }

                if ($clubMode === 'new' && $request->filled('new_club_name')) {
                    $clubMeta = ['source' => 'player_registration'];
                    if ($request->filled('new_club_place_id')) {
                        $clubMeta['google_place_id'] = $request->input('new_club_place_id');
                    }

                    $clubName  = trim($request->input('new_club_name'));
                    $clubEmail = $request->input('new_club_email') ?: $request->email;
                    $clubSlug  = Str::slug($clubName);

                    $existingClub = Club::query()
                        ->where(function ($query) use ($clubName, $clubSlug, $clubEmail) {
                            $query->where('slug', $clubSlug)
                                ->orWhere('name', $clubName);

                            if ($clubEmail) {
                                $query->orWhere('email', $clubEmail);
                            }
                        })
                        ->lockForUpdate()
                        ->first();

                    if ($existingClub) {
                        // Only update placeholder metadata if the club is not fully registered yet.
                        if (! $existingClub->is_registered) {
                            $existingClub->fill([
                                'email'       => $existingClub->email ?: $clubEmail,
                                'address'     => $existingClub->address ?: $request->input('new_club_address'),
                                'phone'       => $existingClub->phone ?: $request->input('new_club_phone'),
                                'joining_url' => $existingClub->joining_url ?: $request->input('new_club_website'),
                                'sport_id'    => $existingClub->sport_id ?: $request->integer('sport'),
                            ]);

                            $existingSocialLinks        = (array) ($existingClub?->social_links ?? []);
                            $existingClub->social_links = array_replace($existingSocialLinks, $clubMeta);

                            if ($existingClub->isDirty()) {
                                $existingClub->save();
                            }
                        }

                        $club = $existingClub;
                    } else {
                        $club = Club::create([
                            'name'          => $clubName,
                            'email'         => $clubEmail,
                            'address'       => $request->input('new_club_address'),
                            'phone'         => $request->input('new_club_phone'),
                            'joining_url'   => $request->input('new_club_website'),
                            'social_links'  => $clubMeta,
                            'is_registered' => false,
                            'sport_id'      => $request->integer('sport'),
                            'user_id'       => null,
                        ]);
                    }
                    $clubId = $club->id;
                } elseif ($request->filled('club')) {
                    $club   = Club::find($request->input('club'));
                    $clubId = $club?->id;
                }

                $player = Player::create([
                    'name'                => trim($request->first_name . ' ' . $request->last_name),
                    'email'               => $request->email,
                    'phone'               => $request->input('phone', ''),
                    'gender'              => $request->input('gender', ''),
                    'birthday'            => $dob,
                    'age'                 => $dob ? $dob->age : null,
                    'address'             => $request->input('address', ''),
                    'city'                => $request->input('city', ''),
                    'state'               => $request->input('state', ''),
                    'user_id'             => $user->id,
                    'college'             => $request->input('college'),
                    'university'          => $request->input('university'),
                    'referee_affiliation' => $request->input('referee_affiliation'),
                    'club_id'             => $clubId,
                    'sport_id'            => $request->integer('sport'),
                    'social_links'        => '',
                    'guardian_first_name' => $request->input('guardian_first_name') ?: null,
                    'guardian_last_name'  => $request->input('guardian_last_name') ?: null,
                    'guardian_email'      => $request->input('guardian_email') ?: null,
                ]);

                $user->update(['player_id' => $player->id]);

                $lifetimeFreeGranted = false;

                if ($inviteRecord) {
                    $inviteRecord->loadMissing('sender');

                    $now = now();
                    $derivedType = match ($inviteRecord->type) {
                        'club' => 'club_invite',
                        'player_free' => 'player',
                        default => $inviteRecord->type,
                    };

                    if ($inviteRecord->type === 'event') {
                        $sender = $inviteRecord->sender;
                        if ($sender?->club_id) {
                            $derivedType = 'club_invite';
                        } elseif ($sender?->player_id) {
                            $derivedType = 'player';
                        }
                    }

                    $metadata = $inviteRecord->metadata ?? [];
                    if (! is_array($metadata)) {
                        $metadata = [];
                    }

                    if ($inviteRecord->type === 'player_free') {
                        $metadata['lifetime_free'] = true;
                        $lifetimeFreeGranted = true;
                    } elseif (! empty($metadata['lifetime_free'])) {
                        $lifetimeFreeGranted = true;
                    }

                    $metadata = array_merge($metadata, [
                        'registered_user_id'        => $user->id,
                        'registered_player_id'      => $player->id,
                        'accepted_via'              => 'registration',
                        'registration_accepted_at'  => $now->toIso8601String(),
                        'derived_invite_type'       => $derivedType,
                    ]);

                    $inviteUpdates = [
                        'receiver_id'    => $user->id,
                        'receiver_email' => $user->email,
                        'metadata'       => $metadata,
                        'is_accepted'    => true,
                    ];

                    if (! $inviteRecord->accepted_at) {
                        $inviteUpdates['accepted_at'] = $now;
                    }

                    if ($inviteRecord->type === 'club' && $derivedType !== $inviteRecord->type) {
                        $inviteUpdates['type'] = $derivedType;
                    }

                    $inviteRecord->forceFill($inviteUpdates)->save();

                    if ($lifetimeFreeGranted && (! $player->is_lifetime_free)) {
                        $player->forceFill([
                            'is_lifetime_free' => true,
                            'lifetime_free_granted_at' => $player->lifetime_free_granted_at ?? $now,
                        ])->save();
                    }
                }

                if ($club && $club->email) {
                    try {
                        Mail::to($club->email)->send(new ClubPlayerRegistration($player, $club, $clubMode === 'new'));
                    } catch (\Throwable $mailException) {
                        report($mailException);
                    }
                }

                session()->forget('pending_player_invite_token');
                session()->forget('pending_invite_type');
            }

            if ($userType === 'college') {
                $college = CollegeUniversity::create([
                    'college_name' => $request->college_name,
                    'email'        => $request->email,
                    'social_links' => '',
                    'user_id'      => $user->id,
                ]);
            }

            if ($userType === 'coach') {
                $coach = Coach::create([
                    'first_name'   => $request->first_name,
                    'last_name'    => $request->last_name,
                    'email'        => $request->email,
                    'gender'       => $request->gender,
                    'country_id'   => $request->integer('country_id'),
                    'city_id'      => $request->integer('city_id'),
                    'phone'        => $request->input('phone'),
                    'sport_id'     => $request->integer('sport_id'),
                    'bio'          => $request->input('bio'),
                    'user_id'      => $user->id,
                    'socail_links' => [],
                ]);

                // Update user's coach_id
                $user->update(['coach_id' => $coach->id]);
            }

            if ($userType === 'referee') {
                Referee::create([
                    'full_name' => trim($request->first_name . ' ' . $request->last_name),
                    'email'     => $request->email,
                    'user_id'   => $user->id,
                    'club_id'   => $request->input('club'),
                ]);
            }

            if (in_array($userType, ['volunteer', 'ambassador'], true) && $request->filled('sport')) {
                $user->sport_id = $request->integer('sport');
                $user->save();
            }

            Auth::login($user);

            $refToken = $request->input('ref');
            $refType  = $request->input('ref_type');
            if ($refToken && in_array($refType, ['player', 'club'], true)) {
                $ambassador = User::where('ambassador_token', $refToken)->first();
                if ($ambassador && $ambassador->hasRole('volunteer')) {
                    if (($refType === 'player' && $userType === 'player') || ($refType === 'club' && $userType === 'club')) {
                        \App\Models\AmbassadorReferral::firstOrCreate([
                            'ambassador_id'    => $ambassador->id,
                            'referred_user_id' => $user->id,
                            'type'             => $refType,
                        ]);
                    }
                }
            }

            // Handle invite/referral tracking for players and clubs
            $inviteTracking    = session('invite_tracking');
            $inviterId         = $inviteTracking['inviter_id'] ?? null;
            $trackedInviteType = $inviteTracking['invite_type'] ?? null;
            $trackedInviteId   = $inviteTracking['invite_id'] ?? null;

            if ($request->user_type === 'player') {
                if ($inviterId && $trackedInviteType && ! in_array($trackedInviteType, ['player', 'player_free'], true)) {
                    $inviterId = null;
                }

                if (! $inviterId && $request->input('ref')) {
                    $referralCode = $request->input('ref');
                    $inviter      = User::where('referral_code', $referralCode)->first();
                    if ($inviter) {
                        $inviterId = $inviter->id;
                    }
                }

                if ($inviterId) {
                    \App\Models\Invite::create([
                        'sender_id'        => $inviterId,
                        'receiver_id'      => $user->id,
                        'receiver_email'   => $user->email,
                        'token'            => \Illuminate\Support\Str::uuid(),
                        'type'             => 'player',
                        'reference_id'     => $user->id,
                        'is_accepted'      => true,
                        'accepted_at'      => now(),
                        'payout_processed' => false,
                    ]);

                    // Update user's referred_by field
                    $user->update(['referred_by' => $inviterId]);
                }
            } elseif ($request->user_type === 'club') {
                if ($inviterId && $trackedInviteType && ! in_array($trackedInviteType, ['club', 'club_invite'], true)) {
                    $inviterId = null;
                }

                if (! $inviterId && $request->input('ref')) {
                    $referralCode = $request->input('ref');
                    $inviter      = User::where('referral_code', $referralCode)->first();
                    if ($inviter) {
                        $inviterId = $inviter->id;
                    }
                }

                if ($inviterId && isset($club) && $club) {
                    $now = now();

                    $inviteRecord = null;
                    if ($trackedInviteId) {
                        $inviteRecord = \App\Models\Invite::query()
                            ->whereKey($trackedInviteId)
                            ->where('sender_id', $inviterId)
                            ->first();
                    }

                    if (! $inviteRecord) {
                        $inviteRecord = \App\Models\Invite::query()
                            ->where('sender_id', $inviterId)
                            ->where('receiver_email', $user->email)
                            ->whereIn('type', ['club', 'club_invite'])
                            ->orderByDesc('created_at')
                            ->first();
                    }

                    $metadataUpdate = array_filter([
                        'club_name'                => $club->name,
                        'club_id'                  => $club->id,
                        'registered_user_id'       => $user->id,
                        'accepted_via'             => 'registration',
                        'registration_accepted_at' => $now->toIso8601String(),
                        'referral_code_used'       => $request->input('ref'),
                    ], static function ($value) {
                        return ! is_null($value) && $value !== '';
                    });

                    if ($inviteRecord) {
                        $existingMetadata = is_array($inviteRecord->metadata) ? $inviteRecord->metadata : [];
                        $inviteRecord->forceFill([
                            'receiver_id'    => $user->id,
                            'receiver_email' => $user->email,
                            'reference_id'   => $club->id,
                            'type'           => 'club',
                            'is_accepted'    => true,
                            'accepted_at'    => $inviteRecord->accepted_at ?? $now,
                            'metadata'       => array_merge($existingMetadata, $metadataUpdate),
                        ])->save();
                    } else {
                        \App\Models\Invite::create([
                            'sender_id'      => $inviterId,
                            'receiver_id'    => $user->id,
                            'receiver_email' => $user->email,
                            'token'          => \Illuminate\Support\Str::uuid(),
                            'type'           => 'club',
                            'reference_id'   => $club->id,
                            'is_accepted'    => true,
                            'accepted_at'    => $now,
                            'metadata'       => $metadataUpdate,
                        ]);
                    }
                }
            }

            if ($inviteTracking) {
                session()->forget('invite_tracking');
            }

            Invite::markAcceptedForUser($user);

            DB::commit();

            return match ($userType) {
                'club'    => $tournamentRegistration
                    ? redirect()->route('club.tournament-registrations.setup', $tournamentRegistration)
                    ->with('success', 'Welcome to Play2Earn! Let\'s finish your tournament registration.')
                    : redirect()->route('club-dashboard')->with('success', 'Welcome! Complete your profile.'),
                'coach'   => redirect()->route('coach-dashboard')->with('success', 'Welcome! Complete your profile.'),
                'referee' => redirect()->route('referee.dashboard')->with('success', 'Welcome! Complete your profile.'),
                'college' => redirect()->route('college.dashboard')->with('success', 'Welcome! Complete your profile.'),
                'volunteer', 'ambassador' => redirect()->route('volunteer.dashboard')->with('success', 'Welcome! Complete your profile.'),
                default   => redirect()->route('my-account')->with('success', 'Thanks for registering! Please review your account and complete payment to continue.'),
            };
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function requiresGuardian(?string $dob): bool
    {
        if (! $dob) {
            return false;
        }

        try {
            return Carbon::parse($dob)->age < 13;
        } catch (\Exception $e) {
            return false;
        }
    }
}

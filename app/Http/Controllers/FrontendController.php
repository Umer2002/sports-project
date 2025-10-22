<?php
namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Club;
use App\Models\Country;
use App\Models\Player;
use App\Models\Position;
use App\Models\Sport;
use App\Models\Stat;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontendController extends Controller
{
    //
    public function index()
    {
        // get sports data from database
        if (Auth::check()) {
            $user = Auth::user();
            $role = $user->role;
            // dd($user);
            if ($user->is_admin) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }
            // Role-based default landing
            if ($user->roles->contains('name', 'club')) {
                return redirect()->intended(route('club-dashboard', absolute: false));
            }
            if ($user->roles->contains('name', 'coach')) {
                return redirect()->intended(route('coach-dashboard', absolute: false));
            }
            if ($user->roles->contains('name', 'referee')) {
                return redirect()->intended(route('referee.dashboard', absolute: false));
            }
            if ($user->roles->contains('name', 'college')) {
                return redirect()->intended(route('college.dashboard', absolute: false));
            }
            return redirect()->intended(route('player.dashboard', absolute: false));
        }

        $categoryId = BlogCategory::where('title', 'Admin')->value('id');

        if (is_null($categoryId)) {
            // handle it: create, throw, or default
            $categoryId = BlogCategory::create(['title' => 'Admin'])->id;
        }
        //dd($category_id);
        $top_blogs = Blog::where('blog_category_id', $categoryId)->limit(5)->get();
        // dd($top_blogs);
        $sports = Sport::where('is_top_sport', 0)->get();
        // $top_sports = Sport::orderBy('is_top_sport', 'desc')->limit(5)->get();

        // return view with sports data
        return view('frontend.index', compact('sports', 'top_blogs'));
    }

    public function my_account()
    {
        $user = Auth::user(); // Use Laravel Auth

        $positions = Position::all();
        $countries = Country::all()->pluck('name', 'sortname')->toArray();

        $userType = $user->roles->first()->name ?? null; // assuming roles are related

        $player  = null;
        $club    = null;
        $college = null;
        $referee = null;
        $stats   = null;
        // $club-
        if ($userType === 'player' && $user->id) {
            $player = Player::where('user_id', $user->id)->first(); // ✅ correct player ID from user
            if ($player) {
                $stats = Stat::where('sports_id', $player->sport_id)->get();
            }
        }
        if ($userType === 'club' && $user->club_id) {

            $club = Club::find($user->club_id);
        }

        if ($userType === 'college') {
            $college = \App\Models\CollegeUniversity::where('user_id', $user->id)->first();
        }

        if ($userType === 'referee') {
            $referee = \App\Models\Referee::where('user_id', $user->id)->first();
        }

        $requiresPlayerPayment = false;
        if ($userType === 'player') {
            $isLifetimeFree = $player?->is_lifetime_free;
            $hasPaid = $player?->payments()->exists();
            $requiresPlayerPayment = ! ($isLifetimeFree || $hasPaid);
        }

        // Use different layouts based on user type
        if ($userType === 'referee') {
            return view('referee.my_account', compact('user', 'countries', 'positions', 'player', 'club', 'college', 'referee', 'stats', 'userType'))
                ->with('requiresPlayerPayment', false);
        }

        return view('frontend.my_account', compact('user', 'countries', 'positions', 'player', 'club', 'college', 'referee', 'stats', 'userType', 'requiresPlayerPayment'));
    }

    public function saveAccount(Request $request)
    {
        $user     = Auth::user();
        $userType = $request->input('userType');

        try {
            DB::beginTransaction();

            $photoPath = null;
            if ($request->filled('captured_image')) {
                $imageData = $request->input('captured_image');

                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    $imageType = strtolower($matches[1]);
                    if (! in_array($imageType, ['jpg', 'jpeg', 'png', 'gif'])) {
                        throw new \Exception('Unsupported image format.');
                    }

                    $base64Str    = substr($imageData, strpos($imageData, ',') + 1);
                    $decodedImage = base64_decode($base64Str);
                    if ($decodedImage === false) {
                        throw new \Exception('Image decoding failed.');
                    }

                    $imageName = $userType . '_' . uniqid() . '.' . $imageType;
                    $uploadDir = public_path('uploads/' . $userType . 's');
                    if (! file_exists($uploadDir)) {
                        mkdir($uploadDir, 0775, true);
                    }

                    $fullPath = $uploadDir . '/' . $imageName;
                    if (file_put_contents($fullPath, $decodedImage) === false) {
                        // Log error but do not throw
                        \Log::error('Failed to save image at ' . $fullPath);
                    } else {
                        $photoPath = 'uploads/' . $userType . 's/' . $imageName;
                    }
                }
            }

            $age = $request->dob ? \Carbon\Carbon::parse($request->dob)->age : null;

            // 🎯 Player
            if ($userType === 'player') {

                $player               = \App\Models\Player::where('user_id', $user->id)->first() ?? new \App\Models\Player();
                $player->user_id      = $user->id;
                $player->name         = $request->name;
                $player->phone        = $request->phone;
                $player->birthday     = $request->dob;
                $player->nationality  = $request->nationality;
                $player->position_id  = $request->position;
                $player->jersey_no    = $request->jersey_no;
                $player->age          = $age;
                $player->club_id      = $request->club_id ?? null;
                $player->team_id      = 1;
                $player->height       = $request->height;
                $player->weight       = $request->weight;
                $player->debut        = $request->debut_date;
                $player->bio          = $request->bio;
                $player->paypal_link  = $request->paypal_link;
                $player->social_links = $request->filled('social_links') ? json_encode($request->social_links) : null;

                if ($photoPath) {
                    $player->photo = $photoPath;
                }

                $player->save();

                if ($request->has('stats')) {
                    $player->stats()->detach();
                    foreach ($request->input('stats') as $statId => $value) {
                        if ($value !== null && $value !== '') {
                            DB::table('player_stats')->insert([
                                'player_id'  => $player->id,
                                'stat_id'    => $statId,
                                'value'      => $value,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            // 🏟 Club
            if ($userType === 'club') {
                $club               = \App\Models\Club::where('user_id', $user->id)->first() ?? new \App\Models\Club();
                $club->user_id      = $user->id;
                $club->name         = $request->name;
                $club->email        = $user->email;
                $club->address      = $request->address;
                $club->phone        = $request->phone;
                $club->bio          = $request->bio;
                $club->paypal_link  = $request->paypal_link;
                $club->social_links = $request->filled('social_links') ? json_encode($request->social_links) : null;

                if ($request->hasFile('logo')) {
                    $logo     = $request->file('logo');
                    $logoName = 'club_' . time() . '.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('uploads/clubs'), $logoName);
                    $club->logo = 'uploads/clubs/' . $logoName;
                }

                $club->save();
            }

            // 🎓 College/University
            if ($userType === 'college') {
                $college               = \App\Models\CollegeUniversity::where('user_id', $user->id)->first() ?? new \App\Models\CollegeUniversity();
                $college->user_id      = $user->id;
                $college->college_name = $request->college_name;
                $college->email        = $user->email;
                $college->address      = $request->address;
                $college->phone        = $request->phone;
                $college->bio          = $request->bio;
                $college->paypal_link  = $request->paypal_link;
                $college->social_links = $request->filled('social_links') ? json_encode($request->social_links) : null;

                if ($request->hasFile('logo')) {
                    $logo     = $request->file('logo');
                    $logoName = 'college_' . time() . '.' . $logo->getClientOriginalExtension();
                    $logo->move(public_path('uploads/colleges'), $logoName);
                    $college->logo = 'uploads/colleges/' . $logoName;
                }

                $college->save();
            }

            // 🧑‍🏫 Coach
            if ($userType === 'coach') {
                $coach               = \App\Models\Coach::where('user_id', $user->id)->first() ?? new \App\Models\Coach();
                $coach->user_id      = $user->id;
                $coach->first_name   = $request->first_name;
                $coach->last_name    = $request->last_name;
                $coach->email        = $user->email;
                $coach->phone        = $request->phone;
                $coach->gender       = $request->gender;
                $coach->city         = $request->city;
                $coach->bio          = $request->bio;
                $coach->country_id   = $request->country_id;
                $coach->age          = $age;
                $coach->sport_id     = $request->sport_id;
                $coach->socail_links = $request->filled('social_links') ? json_encode($request->social_links) : null;

                if ($photoPath) {
                    $coach->photo = $photoPath;
                } elseif ($request->hasFile('photo')) {
                    $photo     = $request->file('photo');
                    $photoName = 'coach_' . time() . '.' . $photo->getClientOriginalExtension();
                    $photo->move(public_path('uploads/coaches'), $photoName);
                    $coach->photo = 'uploads/coaches/' . $photoName;
                }

                $coach->save();
            }

            // 👨‍⚖️ Referee
            if ($userType === 'referee') {
                $referee = \App\Models\Referee::where('user_id', $user->id)->first() ?? new \App\Models\Referee();
                $referee->user_id = $user->id;
                $referee->full_name = $request->first_name . ' ' . $request->last_name;
                $referee->email = $user->email;
                $referee->phone = $request->phone;
                $referee->country = $request->country;
                $referee->certification_level = $request->certification_level;
                $referee->experience_years = $request->experience_years;
                $referee->specialties = $request->specialties;
                $referee->bio = $request->bio;

                if ($photoPath) {
                    $referee->profile_picture = $photoPath;
                } elseif ($request->hasFile('profile_picture')) {
                    $profile = $request->file('profile_picture');
                    $profileName = 'ref_' . time() . '.' . $profile->getClientOriginalExtension();
                    $profile->move(public_path('uploads/referees'), $profileName);
                    $referee->profile_picture = 'uploads/referees/' . $profileName;
                }

                $referee->save();
            }

            DB::commit();

            // Update user basic info
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->country = $request->country;
            $user->save();

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'redirect' => route('my-account'),
                ]);
            }

            // Regular form submission - redirect with success message
            return redirect()->route('my-account')->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Check if request is AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save profile.',
                    'errors' => [$e->getMessage()],
                ], 422);
            }

            // Regular form submission - redirect with error message
            return redirect()->route('my-account')->with('error', 'Failed to save profile: ' . $e->getMessage());
        }
    }

    public function show($slug)
    {
        $blog = Blog::with(['category', 'user', 'club'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Find related blogs — same sport or same Admin category
        $related = Blog::with('user')
            ->where('id', '!=', $blog->id)
            ->where(function ($q) use ($blog) {
                $q->where('blog_category_id', $blog->blog_category_id)
                    ->orWhereHas('club', fn($q2) => $q2->where('sport_id', $blog->club->sport_id ?? null));
            })
            ->whereHas('category', fn($q) => $q->where('title', 'Admin'))
            ->latest()
            ->take(4)
            ->get();

        return view('frontend.blogs.show', compact('blog', 'related'));
    }

}

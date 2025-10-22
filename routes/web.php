<?php

use App\Http\Controllers\Admin\AgeGroupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenderController;
use App\Http\Controllers\Admin\InjuryReportController;
use App\Http\Controllers\Admin\PlayerInviteController;
use App\Http\Controllers\Admin\PickupGameController;
use App\Http\Controllers\Admin\SchedulerController;
use App\Http\Controllers\Admin\SportClassificationGroupController;
use App\Http\Controllers\Admin\SportClassificationOptionController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TeamWizardController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\HelpChatController as AdminHelpChatController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BulkClubImportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ClubBrowseController;
use App\Http\Controllers\Club\DashboardController as ClubDashboardForCollege;
use App\Http\Controllers\Club\Store\OrderController as ClubStoreOrderController;
use App\Http\Controllers\Club\Store\ProductCategoryController as ClubStoreCategoryController;
use App\Http\Controllers\Club\Store\ProductController as ClubStoreProductController;
use App\Http\Controllers\Club\Store\SettingsController as ClubStoreSettingsController;
use App\Http\Controllers\Club\TeamCoachController as ClubTeamCoachController;
use App\Http\Controllers\Club\TournamentInviteController as ClubTournamentInviteController;
use App\Http\Controllers\Club\TournamentRegistrationController as ClubTournamentRegistrationController;
use App\Http\Controllers\Club\VideoController as ClubVideoController;
use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Email\MailboxController as MailboxController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventResponseController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PlayerAvailabilityController;
use App\Http\Controllers\PlayerTransferController;
use App\Http\Controllers\Player\BlogInteractionController;
use App\Http\Controllers\Player\BugReportController;
use App\Http\Controllers\Player\HelpChatController;
use App\Http\Controllers\Player\ProductController as PlayerProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Referee\RefereeController;
use App\Http\Controllers\TeamChatController;
use App\Http\Controllers\TournamentBrowseController;
use App\Http\Controllers\TournamentChatController;
use App\Http\Controllers\TournamentInviteAcceptanceController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\UserDataController;

use App\Http\Middleware\CheckRole;
use App\Models\Division;
use App\Services\PlaytubeSsoService;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

// routes/web.php
use Illuminate\Support\Facades\Schema;

Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('states', [LocationController::class, 'states'])->name('states');
    Route::get('cities', [LocationController::class, 'cities'])->name('cities');
});
Route::get('/user-data', [UserDataController::class, 'getUserData']);

// Public Route
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/tournaments', [TournamentBrowseController::class, 'index'])->name('tournaments.search');
Route::get('/tournaments/invite/{token}', TournamentInviteAcceptanceController::class, '__invoke')->name('tournaments.invites.accept');

//Route::get('/locations/states', [LocationLookupController::class, 'states'])->name('locations.states');
//Route::get('/locations/cities', [LocationLookupController::class, 'cities'])->name('locations.cities');
Route::get('/clubs', [ClubBrowseController::class, 'index'])->name('clubs.search');

// Public Player Profile Route
Route::get('/profile/{playerId}', [App\Http\Controllers\PublicProfileController::class, 'show'])->name('public.player.profile');

// Test route for public profile system
Route::get('/test/public-profile', [App\Http\Controllers\PublicProfileController::class, 'test'])->name('test.public-profile');

// Public Club Profile Route
Route::get('/clubs/{club:slug}', [App\Http\Controllers\PublicClubProfileController::class, 'show'])->name('public.club.profile');
Route::get('/club-profile/{club}', function (\App\Models\Club $club) {
    return redirect()->route('public.club.profile', $club->slug);
})->name('legacy.public.club.profile');

// Test route for public club profile system
Route::get('/test/public-club-profile', [App\Http\Controllers\PublicClubProfileController::class, 'test'])->name('test.public-club-profile');

// Bulk club import and export
Route::get('/bulk-clubs/import', [BulkClubImportController::class, 'showForm'])->name('bulk-clubs.import.form');
Route::post('/bulk-clubs/import', [BulkClubImportController::class, 'import'])->name('bulk-clubs.import');
Route::get('/bulk-clubs/import/complete', [BulkClubImportController::class, 'complete'])->name('bulk-clubs.import.complete');
Route::get('/bulk-clubs/export-logins', [BulkClubImportController::class, 'export'])->name('bulk-clubs.export');

// Test route for club dashboard (temporary, remove in production)
Route::get('/test/club-dashboard', function () {
    $clubUsers = App\Models\User::whereHas('roles', function ($q) {
        $q->where('name', 'club');
    })->with('club')->first();

    if ($clubUsers) {
        auth()->login($clubUsers);
        return redirect()->route('club.dashboard');
    }

    return response()->json(['error' => 'No club users found'], 404);
})->name('test.club-dashboard');

// Quick login route for club users (temporary, remove in production)
Route::get('/quick-login-club', function () {
    $clubUsers = App\Models\User::whereHas('roles', function ($q) {
        $q->where('name', 'club');
    })->with('club')->first();

    if ($clubUsers) {
        auth()->login($clubUsers);
        return response()->json([
            'success'       => true,
            'message'       => 'Logged in as: ' . $clubUsers->name,
            'club'          => $clubUsers->club ? $clubUsers->club->name : 'None',
            'dashboard_url' => route('club.dashboard'),
            'redirect_url'  => route('club.dashboard'),
        ]);
    }

    return response()->json(['error' => 'No club users found'], 404);
})->name('quick-login-club');

// Direct club dashboard route (temporary, remove in production)
Route::get('/club-dashboard', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (! auth()->user()->hasRole('club')) {
        abort(403, 'Access denied. Club role required.');
    }

    $controller = new App\Http\Controllers\Club\DashboardController();
    return $controller->index();
})->middleware('auth')->name('club-dashboard');

// Coach Dashboard Routes
Route::get('/coach-dashboard', function() {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!auth()->user()->hasRole('coach')) {
        abort(403, 'Access denied. Coach role required.');
    }

    $controller = new App\Http\Controllers\Coach\DashboardController();
    return $controller->index();
})->middleware('auth')->name('coach-dashboard');

// Coach management endpoints
Route::middleware(['auth'])->prefix('coach')->name('coach.')->group(function () {
    // Dashboard & Setup
    Route::get('/setup', [\App\Http\Controllers\Coach\DashboardController::class, 'setup'])->name('setup');
    Route::post('/setup', [\App\Http\Controllers\Coach\DashboardController::class, 'storeSetup'])->name('setup.store');
    Route::get('/dashboard', [\App\Http\Controllers\Coach\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Coach\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Coach\ProfileController::class, 'update'])->name('profile.update');

    // Events
    Route::get('/events', [\App\Http\Controllers\Coach\EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [\App\Http\Controllers\Coach\EventController::class, 'create'])->name('events.create');
    Route::post('/events', [\App\Http\Controllers\Coach\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [\App\Http\Controllers\Coach\EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [\App\Http\Controllers\Coach\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [\App\Http\Controllers\Coach\EventController::class, 'destroy'])->name('events.destroy');

    // Teams
    Route::get('/teams', [\App\Http\Controllers\Coach\TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}', [\App\Http\Controllers\Coach\TeamController::class, 'show'])->name('teams.show');

    // Players
    Route::get('/players', [\App\Http\Controllers\Coach\PlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{player}', [\App\Http\Controllers\Coach\PlayerController::class, 'show'])->name('players.show');

    // Training Sessions (placeholder)
    Route::get('/training', function() { return redirect()->route('coach-dashboard')->with('info', 'Training sessions feature coming soon!'); })->name('training.index');

    // Matches (placeholder)
    Route::get('/matches', function() { return redirect()->route('coach-dashboard')->with('info', 'Match analysis feature coming soon!'); })->name('matches.index');

            // Tournaments
            Route::get('/tournaments', [\App\Http\Controllers\Coach\TournamentController::class, 'index'])->name('tournaments.index');
            Route::get('/tournaments/{tournament}', [\App\Http\Controllers\Coach\TournamentController::class, 'show'])->name('tournaments.show');
            Route::get('/tournaments/match/{match}/stats', [\App\Http\Controllers\Coach\TournamentController::class, 'matchStats'])->name('tournaments.match-stats');
            Route::put('/tournaments/match/{match}/stats', [\App\Http\Controllers\Coach\TournamentController::class, 'updateMatchStats'])->name('tournaments.update-match-stats');

           // Awards
           Route::get('/awards', [\App\Http\Controllers\Coach\AwardController::class, 'index'])->name('awards.index');
           Route::get('/awards/assign', [\App\Http\Controllers\Coach\AwardController::class, 'create'])->name('awards.assign');
           Route::post('/awards/assign', [\App\Http\Controllers\Coach\AwardController::class, 'store'])->name('awards.store');
           Route::get('/awards/{award}/details', [\App\Http\Controllers\Coach\AwardController::class, 'getAwardDetails'])->name('awards.details');
           Route::get('/awards/players', [\App\Http\Controllers\Coach\AwardController::class, 'getPlayers'])->name('awards.players');
           Route::get('/awards/log', [\App\Http\Controllers\Coach\AwardController::class, 'log'])->name('awards.log');

           // Tasks
           Route::get('/tasks', [\App\Http\Controllers\Coach\TaskController::class, 'index'])->name('tasks.index');
           Route::get('/tasks/create', [\App\Http\Controllers\Coach\TaskController::class, 'create'])->name('tasks.create');
           Route::post('/tasks', [\App\Http\Controllers\Coach\TaskController::class, 'store'])->name('tasks.store');
           Route::get('/tasks/{task}/edit', [\App\Http\Controllers\Coach\TaskController::class, 'edit'])->name('tasks.edit');
           Route::put('/tasks/{task}', [\App\Http\Controllers\Coach\TaskController::class, 'update'])->name('tasks.update');
           Route::delete('/tasks/{task}', [\App\Http\Controllers\Coach\TaskController::class, 'destroy'])->name('tasks.destroy');
           Route::patch('/tasks/{task}/status', [\App\Http\Controllers\Coach\TaskController::class, 'updateStatus'])->name('tasks.update-status');

           // Blog
           Route::get('/blog', [\App\Http\Controllers\Coach\BlogController::class, 'index'])->name('blog.index');
           Route::get('/blog/create', [\App\Http\Controllers\Coach\BlogController::class, 'create'])->name('blog.create');
           Route::post('/blog', [\App\Http\Controllers\Coach\BlogController::class, 'store'])->name('blog.store');
           Route::get('/blog/{blog}', [\App\Http\Controllers\Coach\BlogController::class, 'show'])->name('blog.show');
           Route::get('/blog/{blog}/edit', [\App\Http\Controllers\Coach\BlogController::class, 'edit'])->name('blog.edit');
           Route::put('/blog/{blog}', [\App\Http\Controllers\Coach\BlogController::class, 'update'])->name('blog.update');
           Route::delete('/blog/{blog}', [\App\Http\Controllers\Coach\BlogController::class, 'destroy'])->name('blog.destroy');

           // Blog interactions
           Route::post('/blog/{blog}/like', [\App\Http\Controllers\Coach\BlogController::class, 'toggleLike'])->name('blog.like');
           Route::get('/blog/{blog}/comments', [\App\Http\Controllers\Coach\BlogController::class, 'indexComments'])->name('blog.comments.index');
           Route::post('/blog/{blog}/comments', [\App\Http\Controllers\Coach\BlogController::class, 'storeComment'])->name('blog.comments.store');
        });

// Club management endpoints
Route::middleware(['auth'])->prefix('club')->name('club.')->group(function () {
    // Player attributes modal route
    Route::get('/player-attributes-modal', function() {
        $club = auth()->user()->club;
        $clubPlayers = $club ? $club->players()->with(['position', 'sport'])->get() : collect();
        return view('club.partials.player-attributes-modal', compact('clubPlayers'));
    })->name('player-attributes-modal');

    // API endpoint to get club teams as JSON
    Route::get('/teams-json', function() {
        $club = auth()->user()->club;
        if (!$club) {
            return response()->json(['teams' => []]);
        }

        $teams = $club->teams()->select('id', 'name')->get();
        return response()->json(['teams' => $teams]);
    })->name('teams.json');

    // Attach/detach coaches to teams (club-owned only)
    Route::post('teams/{team}/coaches', [ClubTeamCoachController::class, 'attach'])->name('teams.coaches.attach');
    Route::delete('teams/{team}/coaches/{coach}', [ClubTeamCoachController::class, 'detach'])->name('teams.coaches.detach');

    Route::get('/teams/{team}/chat', [TeamChatController::class, 'show'])->name('teams.chat');
    Route::post('/teams/{team}/chat', [TeamChatController::class, 'send'])->name('teams.chat.send');
    Route::get('/teams/{team}/chat/messages', [TeamChatController::class, 'messages'])->name('teams.chat.messages');
    Route::post('/teams/{team}/chat/messages', [TeamChatController::class, 'sendJson'])->name('teams.chat.messages.send');
    Route::post('/teams/{team}/chat/attachment', [TeamChatController::class, 'sendAttachment'])->name('teams.chat.attachment');
    // Club Store Management
    Route::prefix('store')->name('store.')->middleware(CheckRole::class . ':club')->group(function () {
        Route::get('products', [ClubStoreProductController::class, 'index'])->name('products.index');
        Route::get('products/create', [ClubStoreProductController::class, 'create'])->name('products.create');
        Route::post('products', [ClubStoreProductController::class, 'store'])->name('products.store');

        Route::get('categories', [ClubStoreCategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [ClubStoreCategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [ClubStoreCategoryController::class, 'store'])->name('categories.store');

        Route::get('orders', [ClubStoreOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [ClubStoreOrderController::class, 'show'])->name('orders.show');

        // Store Settings
        Route::get('settings/payments', [ClubStoreSettingsController::class, 'payments'])->name('settings.payments');
        Route::post('settings/payments', [ClubStoreSettingsController::class, 'updatePayments'])->name('settings.payments.update');
    });
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // My Account
    Route::get('/my/account', [FrontendController::class, 'my_account'])->name('my-account');
    Route::post('/my/account/save', [FrontendController::class, 'saveAccount'])->name('my-account-save');

    // Redirect based on role
    Route::get('/redirect', function () {
        $user = auth()->user();
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard.index');
        }
        if ($user->hasRole('club')) {
            return redirect()->route('club-dashboard');
        }
        if ($user->hasRole('coach')) {
            return redirect()->route('coach-dashboard');
        }
        if ($user->hasRole('referee')) {
            return redirect()->route('referee.dashboard');
        }
        if ($user->hasRole('college')) {
            return redirect()->route('college.dashboard');
        }
        return redirect()->route('player.dashboard');
    })->name('redirect');

    // Utility: fetch a club's sport_id for dynamic forms
    Route::get('/clubs/{club}/sport', function (\App\Models\Club $club) {
        $divisions = [];

        if (Schema::hasTable('divisions')) {
            $divisions = Division::where('sport_id', $club->sport_id)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'category'])
                ->map(fn($division) => [
                    'id'       => $division->id,
                    'name'     => $division->name,
                    'category' => $division->category,
                ])->all();
        }

        return response()->json([
            'sport_id'  => $club->sport_id,
            'divisions' => $divisions,
        ]);
    })->name('clubs.sport');
});

Route::middleware('auth')->prefix('dashboard/calendar')->name('calendar.')->group(function () {
    Route::get('/item/{type}/{id}', [CalendarEventController::class, 'show'])->name('item.show');
    Route::post('/preference/{type}/{id}', [CalendarEventController::class, 'updatePreference'])->name('preference.update');
    Route::post('/upload/{type}/{id}', [CalendarEventController::class, 'upload'])->name('preference.upload');
    Route::get('/ics/{type}/{id}', [CalendarEventController::class, 'downloadIcs'])->name('preference.ics');
});

// Public game live feed
Route::get('/games/{game}/feed', [\App\Http\Controllers\GameEventController::class, 'feed'])->name('games.feed');
Route::get('/games/{game}/events', [\App\Http\Controllers\GameEventController::class, 'list']);
Route::post('/games/{game}/events', [\App\Http\Controllers\GameEventController::class, 'store'])->middleware('auth');

// Lock Screen (Public)
Route::get('/lockscreen/{id}', [App\Http\Controllers\Admin\UserController::class, 'lockScreen'])->name('lockscreen');
Route::middleware(['auth'])->group(function () {
    Route::post('/pickup-games/{id}/join', [PickupGameController::class, 'join'])->name('pickup_games.join');
    Route::post('/pickup-games/{id}/leave', [PickupGameController::class, 'leave'])->name('pickup_games.leave');
    Route::post('/tournaments/{tournament}/chat/join', [TournamentChatController::class, 'join'])->name('tournaments.chat.join');
});

Route::prefix('referee')->name('referee.')->middleware(['auth', CheckRole::class . ':referee'])->group(function () {
    Route::get('/dashboard', [RefereeController::class, 'dashboard'])->name('dashboard');

    // Matches
    Route::get('/available-matches', [RefereeController::class, 'availableMatches'])->name('matches.available');
    Route::post('/apply/{match}', [RefereeController::class, 'apply'])->name('matches.apply');
    Route::get('/match/{match}', [RefereeController::class, 'viewMatch'])->name('matches.view');
    Route::post('/cancel/{match}', [RefereeController::class, 'cancelApplication'])->name('matches.cancel');

    Route::get('/chat', [ChatController::class, 'chat'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/attachment', [ChatController::class, 'sendAttachment'])->name('chat.attachment');
    Route::get('/email', [EmailController::class, 'email'])->name('email');
    Route::resource('pickup_games', PickupGameController::class);

    // Availability
    Route::get('/availability', [RefereeController::class, 'availabilityForm'])->name('availability.form');
    Route::post('/availability', [RefereeController::class, 'storeAvailability'])->name('availability.update');
});
// ------------------------------------------------------------------------------------------------------------------ Player Area
Route::middleware(['auth'])->prefix('player')->name('player.')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.new');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/old-dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.old');

    Route::get('/videos', function () {
        return redirect()->route('player.videos.explore');
    })->name('videos.index');
    Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
    Route::get('/videos/explore', [VideoController::class, 'reactIndex'])->name('videos.explore');
    Route::get('/videos/explore/{video}', [VideoController::class, 'show'])->whereNumber('video')->name('videos.show');
    Route::get('/api/videos', [VideoController::class, 'feedJson'])->name('videos.feed-json');
    Route::post('/videos/{video}/comment', [VideoController::class, 'comment'])->name('videos.comment');
    Route::post('/videos/{video}/like', [VideoController::class, 'like'])->name('videos.like');
    Route::post('/videos/{video}/unlike', [VideoController::class, 'unlike'])->name('videos.unlike');
    Route::get('/videos/{video}/comments', [VideoController::class, 'comments'])->name('videos.comments.index');

    Route::get('/merchandise', [PlayerProductController::class, 'clubMerchandise'])->name('products.club');
    Route::get('/storefront', [PlayerProductController::class, 'storeFront'])->name('products.store');

    Route::get('/events/feed', [EventController::class, 'feed'])->name('events.feed');

    Route::get('/chat', [ChatController::class, 'chat'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/attachment', [ChatController::class, 'sendAttachment'])->name('chat.attachment');
    Route::get('/chat/messages/{chatID}', [ChatController::class, 'getMessages']);
    Route::get('/chat/initiate/{userId}', [ChatController::class, 'initiate']);

    Route::post('/help-chat/message', [HelpChatController::class, 'send'])->name('help-chat.send');
    Route::post('/bug-reports', [BugReportController::class, 'store'])->name('bug-reports.store');

    // Team chat
    Route::get('/teams/{team}/chat', [TeamChatController::class, 'show'])->name('teams.chat');
    Route::post('/teams/{team}/chat', [TeamChatController::class, 'send'])->name('teams.chat.send');
    // React live chat UI + JSON endpoints
    Route::get('/teams/{team}/chat/react', function (\App\Models\Team $team) {
        return view('teams.react-chat', compact('team'));
    })->name('teams.chat.react');
    Route::get('/teams/{team}/chat/messages', [TeamChatController::class, 'messages'])->name('teams.chat.messages');
    Route::post('/teams/{team}/chat/messages', [TeamChatController::class, 'sendJson'])->name('teams.chat.messages.send');
    Route::post('/teams/{team}/chat/attachment', [TeamChatController::class, 'sendAttachment'])->name('teams.chat.attachment');

    Route::get('/email', [EmailController::class, 'email'])->name('email');
    Route::get('/calendar', [CalendarController::class, 'calendar'])->name('calendar');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'updatePlayerProfile'])->name('profile.update');
    // Blog Index Page (List or Feed)
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');

    // Blog Detail Page
    Route::get('/blogs/{post}', [BlogController::class, 'show'])->name('blogs.show');

    // Blog Create Page (Form + Recent Posts)
    Route::get('/blogs/create', [BlogController::class, 'viewPage'])->name('blogs.create');

    // Blog Save (AJAX POST)
    Route::post('/blogs/save', [BlogController::class, 'saveBlog'])->name('blogs.save');

    // CKEditor Inline Image Upload
    Route::post('/blogs/upload-image', [BlogController::class, 'uploadImage'])->name('blogs.upload-image');

    // Blog interactions
    Route::post('/blogs/{blog}/like', [BlogInteractionController::class, 'toggleLike'])->name('blogs.like');
    Route::get('/blogs/{blog}/comments', [BlogInteractionController::class, 'indexComments'])->name('blogs.comments.index');
    Route::post('/blogs/{blog}/comments', [BlogInteractionController::class, 'storeComment'])->name('blogs.comments.store');

    // Availability
    Route::get('/availability', [PlayerAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [PlayerAvailabilityController::class, 'store'])->name('availability.store');
    Route::put('/availability/{availability}', [PlayerAvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/availability/{availability}', [PlayerAvailabilityController::class, 'destroy'])->name('availability.destroy');

    // Event responses
    Route::post('/events/{event}/respond', [EventResponseController::class, 'respond'])->name('events.respond');

    // Transfer Management
    Route::resource('transfers', \App\Http\Controllers\Player\TransferController::class);
    Route::post('/transfer/request', [PlayerTransferController::class, 'request'])->name('transfer.request');

    // Pickup Games
    Route::resource('pickup-games', \App\Http\Controllers\Player\PickupGameController::class);
    Route::post('pickup-games/{pickup_game}/join', [\App\Http\Controllers\Player\PickupGameController::class, 'join'])->name('pickup-games.join');
    Route::post('pickup-games/{pickup_game}/leave', [\App\Http\Controllers\Player\PickupGameController::class, 'leave'])->name('pickup-games.leave');
    Route::post('pickup-games/{pickup_game}/share', [\App\Http\Controllers\Player\PickupGameController::class, 'share'])->name('pickup-games.share');

    // Player Invites
    Route::get('/invite', [\App\Http\Controllers\Player\InviteController::class, 'create'])->name('invite.create');
    Route::post('/invite', [\App\Http\Controllers\Player\InviteController::class, 'store'])->name('invite.store');
    // Player Club Invites (for players to refer clubs)
    Route::get('/invite-club', [\App\Http\Controllers\Player\InviteController::class, 'createClub'])->name('invite.club.create');
    Route::post('/invite-club', [\App\Http\Controllers\Player\InviteController::class, 'storeClub'])->name('invite.club.store');
    Route::get('/invite-dashboard', [\App\Http\Controllers\Player\InviteController::class, 'overview'])->name('invite.overview');

    // Player Payouts
    Route::get('/payouts', [\App\Http\Controllers\Player\PayoutController::class, 'index'])->name('payouts');
    Route::post('/payouts/request', [\App\Http\Controllers\Player\PayoutController::class, 'requestPayout'])->name('payout.request');

    // Challenges
    Route::get('/challenge', [\App\Http\Controllers\Player\ChallengeController::class, 'create'])->name('challenge.create');
    Route::post('/challenge', [\App\Http\Controllers\Player\ChallengeController::class, 'store'])->name('challenge.store');
});

// Blog Area
Route::middleware('auth')->prefix('post')->name('blogs.')->group(function () {
    Route::get('/all', [BlogController::class, 'index'])->name('index');
    Route::get('/show/{id}', [BlogController::class, 'show'])->name('show');
    Route::get('/post/{post}', [BlogController::class, 'show'])->name('detail');
    Route::get('/add-new', [BlogController::class, 'viewPage'])->name('viewPage');
    Route::post('/save', [BlogController::class, 'saveBlog'])->name('save');
    Route::post('/ckeditor/upload', [BlogController::class, 'uploadImage'])->name('ckeditor.upload');
});

// Ecommerce Area
Route::middleware('auth')->prefix('ecommerce')->name('ecommerce.')->group(function () {
    Route::get('/shop', [EcommerceController::class, 'shop'])->name('shop');
});

// Broadcasting
Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->middleware('auth');

// Admin routes
Route::get('/admin/player-teams/{player}', function ($playerId) {
    $player = \App\Models\Player::with('teams')->find($playerId);
    return response()->json([
        'teams' => $player?->teams->pluck('name') ?? [],
    ]);
})->middleware(['auth', CheckRole::class . ':admin']);

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('player-invites/free', [PlayerInviteController::class, 'index'])->name('player-invites.free.index');
    Route::post('player-invites/free', [PlayerInviteController::class, 'store'])->name('player-invites.free.store');
    Route::get('help-chats', [AdminHelpChatController::class, 'index'])->name('help-chats.index');

    Route::prefix('teams/wizard')->name('teams.wizard.')->group(function () {

        // Step 1: Create Team
        Route::get('/step1', [TeamWizardController::class, 'step1Form'])->name('step1');
        Route::post('/step1', [TeamWizardController::class, 'storeStep1'])->name('storeStep1');

        // Step 2: Define Eligibility
        Route::get('/{team}/step2', [TeamWizardController::class, 'step2Form'])->name('step2');
        Route::post('/{team}/step2', [TeamWizardController::class, 'storeStep2'])->name('storeStep2');

        // Step 3: Select Players
        Route::get('/{team}/step3', [TeamWizardController::class, 'step3Form'])->name('step3');
        Route::post('/{team}/step3', [TeamWizardController::class, 'storePlayers'])->name('storePlayers');

        // Step 4: Build Formation
        Route::get('/{team}/step4', [TeamWizardController::class, 'step4Form'])->name('step4');
        Route::post('/{team}/step4', [TeamWizardController::class, 'finalizeFormation'])->name('finalizeFormation');
    });
    Route::get('productcategory/{category}/confirm-delete', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'confirmDelete'])->name('admin.productcategory.confirm-delete');
    Route::get('/injury-reports', [InjuryReportController::class, 'index'])->name('injury_reports.index');
    Route::get('/injury-reports/create', [InjuryReportController::class, 'create'])->name('injury_reports.create');
    Route::post('/injury-reports', [InjuryReportController::class, 'store'])->name('injury_reports.store');
    Route::get('/injury-reports/{injuryReport}/edit', [InjuryReportController::class, 'edit'])->name('injury_reports.edit');
    Route::put('/injury-reports/{injuryReport}', [InjuryReportController::class, 'update'])->name('injury_reports.update');
    Route::post('/injury-reports/{injuryReport}', [InjuryReportController::class, 'update'])->name('injury_reports.update.post');
    Route::delete('/injury-reports/{injuryReport}', [InjuryReportController::class, 'destroy'])->name('injury_reports.destroy');

    Route::resource('dashboard', DashboardController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // GET: Preview generated schedule
    Route::get('tournaments/{tournament}/scheduler', [SchedulerController::class, 'generate'])->name('scheduler.generate');

    // POST: Save the generated schedule
    Route::post('admin/tournaments/{tournament}/scheduler', [SchedulerController::class, 'store'])->name('scheduler.store');
    Route::resource('blogcategory', \App\Http\Controllers\Admin\BlogCategoryController::class);
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class);
    Route::resource('clubs', \App\Http\Controllers\Admin\ClubController::class);
    Route::resource('coaches', \App\Http\Controllers\Admin\CoachController::class);
    // Extra email routes
    Route::prefix('email')->name('email.')->group(function () {
        // Core pages
        Route::get('inbox', [\App\Http\Controllers\Admin\EmailController::class, 'inbox'])->name('inbox');
        Route::get('sent', [\App\Http\Controllers\Admin\EmailController::class, 'sent'])->name('sent');
        Route::get('compose', [\App\Http\Controllers\Admin\EmailController::class, 'compose'])->name('compose');
        Route::get('drafts', [\App\Http\Controllers\Admin\EmailController::class, 'drafts'])->name('drafts');
        Route::get('trash', [\App\Http\Controllers\Admin\EmailController::class, 'trash'])->name('trash');
        Route::get('show/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'show'])->name('show');
        // Store & Delete
        Route::post('store', [\App\Http\Controllers\Admin\EmailController::class, 'store'])->name('store');
        Route::post('delete/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'delete'])->name('delete');

        // Reply
        Route::get('reply/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'reply'])->name('reply');
        Route::post('reply/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'sendReply'])->name('sendReply');

        // Trash & Restore individual
        Route::post('move-to-trash/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'moveToTrash'])->name('moveToTrash');
        Route::post('restore/{id}', [\App\Http\Controllers\Admin\EmailController::class, 'restore'])->name('restore');

        // Bulk actions
        Route::post('bulk-move-to-trash', [\App\Http\Controllers\Admin\EmailController::class, 'bulkMoveToTrash'])->name('bulk-move-to-trash');
        Route::post('bulk-delete', [\App\Http\Controllers\Admin\EmailController::class, 'bulkDelete'])->name('bulk-delete');
    });

    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    // admin.events.invite
    Route::post('/events/{event}/invite', [\App\Http\Controllers\Admin\EventController::class, 'invite'])->name('events.invite');

    Route::resource('games', \App\Http\Controllers\Admin\GameController::class);

    Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
    Route::resource('referees', \App\Http\Controllers\Admin\RefereeController::class);

    Route::resource('order', \App\Http\Controllers\Admin\OrderController::class);
    Route::resource('players', \App\Http\Controllers\Admin\PlayerController::class);
    Route::resource('player-stats', \App\Http\Controllers\Admin\PlayerStatsController::class);
    Route::get('player-stats/team/{team}/players', [\App\Http\Controllers\Admin\PlayerStatsController::class, 'getTeamPlayers'])->name('player-stats.team-players');
    Route::get('player-stats/sport/{sport}/stats', [\App\Http\Controllers\Admin\PlayerStatsController::class, 'getSportStats'])->name('player-stats.sport-stats');
    Route::post('player-stats/bulk-delete', [\App\Http\Controllers\Admin\PlayerStatsController::class, 'bulkDelete'])->name('player-stats.bulk-delete');
    Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::post('products/pull-woocommerce', [\App\Http\Controllers\Admin\ProductController::class, 'pullWooCommerce'])->name('products.pull-woocommerce');
    Route::resource('productcategory', \App\Http\Controllers\Admin\ProductCategoryController::class);
    Route::post('productcategory/pull-woocommerce', [\App\Http\Controllers\Admin\ProductCategoryController::class, 'pullWooCommerce'])->name('productcategory.pull-woocommerce');
    Route::resource('rewards', \App\Http\Controllers\Admin\RewardsController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RolesController::class);
    Route::resource('sponsors', \App\Http\Controllers\Admin\SponsorsController::class);
    Route::resource('sports', \App\Http\Controllers\Admin\SportController::class);
    Route::resource('age-groups', AgeGroupController::class)->names('age_groups');
    Route::resource('genders', GenderController::class);
    Route::resource('sport-classification-groups', SportClassificationGroupController::class)->names('sport_classification_groups');
    Route::resource('sport-classification-options', SportClassificationOptionController::class)->names('sport_classification_options');
    Route::resource('stats', \App\Http\Controllers\Admin\StatController::class);
    Route::resource('tasks', \App\Http\Controllers\Admin\TaskController::class);
    Route::resource('teams', \App\Http\Controllers\Admin\TeamController::class);
    Route::post('teams/{team}/add-player', [\App\Http\Controllers\Admin\TeamController::class, 'addPlayer'])->name('teams.addPlayer');
    Route::post('teams/{team}/remove-player', [\App\Http\Controllers\Admin\TeamController::class, 'removePlayer'])->name('teams.removePlayer');
    Route::post('teams/{team}/update-player-position', [\App\Http\Controllers\Admin\TeamController::class, 'updatePlayerPosition'])->name('teams.updatePlayerPosition');
    Route::post('teams/{team}/add-coach', [\App\Http\Controllers\Admin\TeamController::class, 'addCoach'])->name('teams.addCoach');
    Route::post('teams/{team}/remove-coach', [\App\Http\Controllers\Admin\TeamController::class, 'removeCoach'])->name('teams.removeCoach');
    Route::post('transfers/{transfer}/approve', [PlayerTransferController::class, 'approve'])->name('transfers.approve');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('tournamentformats', \App\Http\Controllers\Admin\TournamentFormatController::class);
    Route::get('news/data', [\App\Http\Controllers\Admin\NewsController::class, 'data'])->name('news.data');
    Route::get('locations/venue/states', [VenueController::class, 'states'])->name('locations.venue.states');
    Route::get('locations/venue/cities', [VenueController::class, 'cities'])->name('locations.venue.cities');
    Route::get('locations/venues', [TournamentController::class, 'venuesForCity'])->name('locations.venues');
    Route::resource('venues', VenueController::class);
    Route::post('venues/{venue}/availability', [VenueController::class, 'storeAvailability'])->name('venues.availability.store');
    Route::delete('venues/availability/{availability}', [VenueController::class, 'deleteAvailability'])->name('venues.availability.delete');

    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{id}/assign', [TaskController::class, 'assign'])->name('tasks.assign');

    Route::resource('tournaments', \App\Http\Controllers\Admin\TournamentController::class);
    Route::get('clubs/{club}/teams-for-sport', [\App\Http\Controllers\Admin\TournamentController::class, 'teamsForHostClub'])->name('clubs.teams-for-sport');
    Route::post('tournaments/matches/{match}/assign-referee', [\App\Http\Controllers\Admin\TournamentController::class, 'assignRefereeToMatch'])->name('tournaments.assign-referee-match');
    Route::delete('tournaments/matches/{match}/remove-referee', [\App\Http\Controllers\Admin\TournamentController::class, 'removeRefereeFromMatch'])->name('tournaments.remove-referee-match');

    Route::resource('volunteers', \App\Http\Controllers\Admin\VolunteersController::class);
    // define this route admin.productcategory.confirm-delete/{id}
    // Additional routes
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getUsersData'])->name('users.data');
    Route::get('players/stats-by-sport/{id}', [\App\Http\Controllers\Admin\PlayerController::class, 'getStatsBySport'])->name('players.getStats');

    Route::resource('ads', \App\Http\Controllers\Admin\AdController::class);
    Route::resource('payout_plans', \App\Http\Controllers\Admin\PayoutPlanController::class);
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/payout', [\App\Http\Controllers\Admin\PaymentController::class, 'store'])->name('payments.payout');

    // Donation management routes
    Route::get('donations', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
    Route::get('donations/{donation}', [\App\Http\Controllers\Admin\DonationController::class, 'show'])->name('donations.show');
    Route::get('donations/export/csv', [\App\Http\Controllers\Admin\DonationController::class, 'export'])->name('donations.export');

    // Order management routes
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/ship', [\App\Http\Controllers\Admin\OrderController::class, 'markAsShipped'])->name('orders.ship');
    Route::post('orders/{order}/deliver', [\App\Http\Controllers\Admin\OrderController::class, 'markAsDelivered'])->name('orders.deliver');
    Route::post('orders/{order}/cancel', [\App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('orders/export/csv', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');

    // Game Expertise Management
    Route::get('game-expertise', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'index'])->name('game-expertise.index');
    Route::put('game-expertise/games/{game}', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'updateGameExpertise'])->name('game-expertise.update-game');
    Route::put('game-expertise/pickup-games/{pickupGame}', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'updatePickupGameExpertise'])->name('game-expertise.update-pickup-game');
    Route::get('game-expertise/games/{game}/referees', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'getAvailableReferees'])->name('game-expertise.game-referees');
    Route::get('game-expertise/pickup-games/{pickupGame}/referees', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'getAvailableRefereesForPickupGame'])->name('game-expertise.pickup-game-referees');

    // Referee Assignment Routes
    Route::post('game-expertise/games/{game}/assign-referee', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'assignRefereeToGame'])->name('game-expertise.assign-referee-game');
    Route::post('game-expertise/pickup-games/{pickupGame}/assign-referee', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'assignRefereeToPickupGame'])->name('game-expertise.assign-referee-pickup-game');
    Route::delete('game-expertise/games/{game}/remove-referee', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'removeRefereeFromGame'])->name('game-expertise.remove-referee-game');
    Route::delete('game-expertise/pickup-games/{pickupGame}/remove-referee', [\App\Http\Controllers\Admin\GameExpertiseController::class, 'removeRefereeFromPickupGame'])->name('game-expertise.remove-referee-pickup-game');
});
Route::prefix('referee')->name('referee.')->middleware(['auth', CheckRole::class . ':referee'])->group(function () {
    // Referee Expertise Management
    Route::get('/expertise', [\App\Http\Controllers\Referee\ExpertiseController::class, 'index'])->name('expertise.index');
    Route::put('/expertise', [\App\Http\Controllers\Referee\ExpertiseController::class, 'update'])->name('expertise.update');
    Route::get('/expertise/available-games', [\App\Http\Controllers\Referee\ExpertiseController::class, 'availableGames'])->name('expertise.available-games');
    Route::get('/expertise/all-games', [\App\Http\Controllers\Referee\ExpertiseController::class, 'allGames'])->name('expertise.all-games');
});

Route::prefix('club')->name('club.')->middleware(['auth'])->group(function () {
    Route::middleware(CheckRole::class . ':club,coach')->group(function () {
        Route::get('/test', function () {
            return response()->json(['message' => 'Club route group working', 'user' => auth()->user()->name ?? 'No user']);
        })->name('test');

        Route::get('/dashboard', [\App\Http\Controllers\Club\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/videos', [ClubVideoController::class, 'index'])->name('videos.index');
        Route::post('/videos/upload', [VideoController::class, 'upload'])->name('videos.upload');
        Route::get('/videos/{video}', [VideoController::class, 'show'])->whereNumber('video')->name('videos.show');

        Route::resource('teams', \App\Http\Controllers\Club\TeamController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
        Route::post('teams/{team}/add-player', [\App\Http\Controllers\Club\TeamController::class, 'addPlayer'])->name('teams.addPlayer');
        Route::post('teams/{team}/remove-player', [\App\Http\Controllers\Club\TeamController::class, 'removePlayer'])->name('teams.removePlayer');
        Route::post('teams/{team}/update-player-position', [\App\Http\Controllers\Club\TeamController::class, 'updatePlayerPosition'])->name('teams.updatePlayerPosition');

        Route::prefix('teams/wizard')->name('teams.wizard.')->group(function () {
            Route::get('/step1', [\App\Http\Controllers\Club\TeamWizardController::class, 'step1Form'])->name('step1');
            Route::post('/step1', [\App\Http\Controllers\Club\TeamWizardController::class, 'storeStep1'])->name('storeStep1');
            Route::get('/{team}/step2', [\App\Http\Controllers\Club\TeamWizardController::class, 'step2Form'])->name('step2');
            Route::post('/{team}/step2', [\App\Http\Controllers\Club\TeamWizardController::class, 'storeStep2'])->name('storeStep2');
            Route::get('/{team}/step3', [\App\Http\Controllers\Club\TeamWizardController::class, 'step3Form'])->name('step3');
            Route::post('/{team}/step3', [\App\Http\Controllers\Club\TeamWizardController::class, 'storePlayers'])->name('storePlayers');
            Route::get('/{team}/step4', [\App\Http\Controllers\Club\TeamWizardController::class, 'step4Form'])->name('step4');
            Route::post('/{team}/step4', [\App\Http\Controllers\Club\TeamWizardController::class, 'finalizeFormation'])->name('finalizeFormation');
        });
    });

    Route::middleware(CheckRole::class . ':club')->group(function () {
        Route::get('/financial-dashboard', [\App\Http\Controllers\Club\FinancialDashboardController::class, 'index'])
            ->name('financial.dashboard');
        Route::post('/financial-dashboard/calculate', [\App\Http\Controllers\Club\FinancialDashboardController::class, 'calculate'])
            ->name('financial.dashboard.calculate');
        Route::get('/financial-dashboard/export', [\App\Http\Controllers\Club\FinancialDashboardController::class, 'export'])
            ->name('financial.dashboard.export');

        Route::resource('events', \App\Http\Controllers\Club\EventController::class);
        Route::post('events/store', [\App\Http\Controllers\Club\EventController::class, 'store'])->name('events.store');
        Route::post('events/{event}/invite', [\App\Http\Controllers\Club\EventController::class, 'invite'])->name('events.invite');
        Route::get('team/{team}/players', [\App\Http\Controllers\Club\TeamController::class, 'getPlayers'])->name('team.players');

        Route::resource('games', \App\Http\Controllers\Club\GameController::class)->only(['index', 'create', 'store']);
        Route::post('games/{game}/invite', [\App\Http\Controllers\Club\GameController::class, 'invite'])->name('games.invite');

        Route::resource('injury-reports', \App\Http\Controllers\Club\InjuryReportController::class)->names('injury_reports');

        Route::get('calendar', [\App\Http\Controllers\Club\CalendarController::class, 'index'])->name('calendar');
        Route::post('/clubs/{club}/process-payout', [App\Http\Controllers\Admin\ClubController::class, 'processPayout'])->name('clubs.processPayout');
        Route::get('/setup', [App\Http\Controllers\Club\DashboardController::class, 'setup'])->name('setup');
        Route::post('/setup', [App\Http\Controllers\Club\DashboardController::class, 'storeSetup'])->name('storeSetup');

        // Club Awards
        Route::resource('awards', \App\Http\Controllers\Club\AwardController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::get('/awards/{id}/details', [\App\Http\Controllers\Club\AwardController::class, 'getAwardDetails'])->name('awards.details');
        Route::get('/awards/players', [\App\Http\Controllers\Club\AwardController::class, 'getPlayers'])->name('awards.players');
        Route::get('/awards/log', [\App\Http\Controllers\Club\AwardController::class, 'log'])->name('awards.log');

        // Club Player Stats
        Route::get('/team/{team}/players-stats', [\App\Http\Controllers\Club\PlayerStatsController::class, 'getTeamPlayersWithStats'])->name('team.players-stats');
        Route::post('/player-stats/save', [\App\Http\Controllers\Club\PlayerStatsController::class, 'savePlayerStats'])->name('player-stats.save');

        Route::get('/invite', [\App\Http\Controllers\Club\InviteController::class, 'create'])->name('invite.create');
        Route::post('/invite', [\App\Http\Controllers\Club\InviteController::class, 'store'])->name('invite.store');

        Route::resource('transfers', \App\Http\Controllers\Club\TransferController::class)->only(['index', 'show']);
        Route::post('transfers/{transfer}/approve', [\App\Http\Controllers\Club\TransferController::class, 'approve'])->name('transfers.approve');
        Route::post('transfers/{transfer}/reject', [\App\Http\Controllers\Club\TransferController::class, 'reject'])->name('transfers.reject');
        Route::post('transfers/{transfer}/cancel', [\App\Http\Controllers\Club\TransferController::class, 'cancel'])->name('transfers.cancel');

        Route::resource('coaches', \App\Http\Controllers\Club\CoachController::class);

        Route::get('players/invite', [\App\Http\Controllers\Club\PlayerController::class, 'invite'])->name('players.invite');
        Route::post('players/invite', [\App\Http\Controllers\Club\PlayerController::class, 'storeInvite'])->name('players.invite.store');
        Route::post('players/invite/bulk', [\App\Http\Controllers\Club\PlayerController::class, 'storeBulkInvites'])->name('players.invite.bulk');
        Route::resource('players', \App\Http\Controllers\Club\PlayerController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);

        Route::get('teams-for-host-club/{club}', [\App\Http\Controllers\Club\TournamentController::class, 'teamsForHostClub'])->name('teams.for.host.club');
        Route::get('locations/venues', [\App\Http\Controllers\Club\TournamentController::class, 'venuesForCity'])->name('locations.venues');
        Route::get('tournaments/{tournament}/schedule', [\App\Http\Controllers\Club\SchedulerController::class, 'generate'])->name('tournaments.schedule');
        Route::post('tournaments/{tournament}/schedule', [\App\Http\Controllers\Club\SchedulerController::class, 'store'])->name('tournaments.schedule.store');
        Route::post('matches/{match}/assign-referee', [\App\Http\Controllers\Club\TournamentController::class, 'assignRefereeToMatch'])->name('matches.assign.referee');
        Route::delete('matches/{match}/remove-referee', [\App\Http\Controllers\Club\TournamentController::class, 'removeRefereeFromMatch'])->name('matches.remove.referee');
        Route::post('tournaments/{tournament}/invites', [ClubTournamentInviteController::class, 'store'])->name('tournaments.invites.store');

        Route::prefix('tournament-registrations')->name('tournament-registrations.')->group(function () {
            Route::get('{registration}/setup', [ClubTournamentRegistrationController::class, 'setup'])->name('setup');
            Route::post('{registration}/setup', [ClubTournamentRegistrationController::class, 'storeSetup'])->name('setup.store');
            Route::get('{registration}', [ClubTournamentRegistrationController::class, 'show'])->name('show');
            Route::post('{registration}/teams', [ClubTournamentRegistrationController::class, 'attachTeams'])->name('attach-teams');
            Route::get('tournaments/invites', [ClubTournamentRegistrationController::class, 'listTournaments'])->name('invites.list');

        });

        Route::resource('tournaments', \App\Http\Controllers\Club\TournamentController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::post('tournaments/modal', [\App\Http\Controllers\Club\TournamentController::class, 'storeFromModal'])->name('tournaments.store-modal');

        Route::get('profile', [\App\Http\Controllers\Club\ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [\App\Http\Controllers\Club\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\Club\ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::prefix('volunteer')->name('volunteer.')->middleware(['auth', CheckRole::class . ':volunteer'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Volunteer\DashboardController::class, 'index'])->name('dashboard');

    // Clubs management
    Route::get('/clubs', [\App\Http\Controllers\Volunteer\ClubsController::class, 'index'])->name('clubs.index');
    Route::get('/clubs/import', [\App\Http\Controllers\Volunteer\ClubsController::class, 'importForm'])->name('clubs.importForm');
    Route::post('/clubs/import', [\App\Http\Controllers\Volunteer\ClubsController::class, 'import'])->name('clubs.import');
    Route::get('/clubs/export-logins', [\App\Http\Controllers\Volunteer\ClubsController::class, 'exportLogins'])->name('clubs.exportLogins');
    Route::post('/clubs/{club}/resend-invite', [\App\Http\Controllers\Volunteer\ClubsController::class, 'resendInvite'])->name('clubs.resendInvite');

    // Promotions
    Route::get('/promotions', [\App\Http\Controllers\Volunteer\PromotionsController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [\App\Http\Controllers\Volunteer\PromotionsController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [\App\Http\Controllers\Volunteer\PromotionsController::class, 'store'])->name('promotions.store');
});

// user lougout
Route::get('/front/logout', function () {
    auth()->logout();
    return redirect()->route('home');
})->name('front.logout');

// Add a GET route for logout for direct access
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

// API route for loading cities by country
Route::get('/api/cities', function (\Illuminate\Http\Request $request) {
    $countryId = $request->get('country_id');

    if (!$countryId) {
        return response()->json(['data' => []]);
    }

    $cities = \App\Models\City::where('country_id', $countryId)
        ->orderBy('name')
        ->get(['id', 'name']);

    return response()->json(['data' => $cities]);
});

// Public invite acceptance route
Route::get('/invite/{token}', [\App\Http\Controllers\InviteLinkController::class, 'accept'])->name('invite.accept');

// Club registration route
Route::get('/club-register/{clubId}', [\App\Http\Controllers\PublicClubProfileController::class, 'showRegistration'])->name('club.register');

// Auth scaffolding
require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::post('users/{user}/follow', [\App\Http\Controllers\FollowController::class, 'follow'])->name('users.follow');
    Route::delete('users/{user}/follow', [\App\Http\Controllers\FollowController::class, 'unfollow'])->name('users.unfollow');
});

Route::post('/send-invite-email', [\App\Http\Controllers\InviteLinkController::class, 'sendInviteEmail'])->name('send-invite-email')->middleware('auth');
Route::post('/player/checkout', [\App\Http\Controllers\Player\CheckoutController::class, 'createCheckout'])->name('player.checkout');
Route::get('/player/payment/success', [\App\Http\Controllers\Player\CheckoutController::class, 'paymentSuccess'])->name('player.payment.success');
Route::get('/player/payment/cancel', [\App\Http\Controllers\Player\CheckoutController::class, 'paymentCancel'])->name('player.payment.cancel');

// Donation routes
Route::post('/donation/checkout', [\App\Http\Controllers\DonationController::class, 'createCheckout'])->name('donation.checkout');
Route::get('/donation/success', [\App\Http\Controllers\DonationController::class, 'success'])->name('donation.success');
Route::get('/donation/cancel', [\App\Http\Controllers\DonationController::class, 'cancel'])->name('donation.cancel');
Route::post('/donation/webhook', [\App\Http\Controllers\DonationController::class, 'webhook'])->name('donation.webhook');
Route::get('/player/tournaments', [\App\Http\Controllers\DashboardController::class, 'tournamentDirectory'])->name('player.tournaments.directory');

// API routes for location data
Route::get('/api/states/{country}', function($countryId) {
    $states = \App\Models\State::where('country_id', $countryId)->orderBy('name')->get(['id', 'name']);
    return response()->json($states);
});

Route::get('/api/cities/{state}', function($stateId) {
    $cities = \App\Models\City::where('state_id', $stateId)->orderBy('name')->get(['id', 'name']);
    return response()->json($cities);
});

// Shopping Cart routes
Route::middleware('auth')->group(function () {
    Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{cartItem}/update', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}/remove', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    // Player Orders
    Route::get('/orders', [\App\Http\Controllers\Player\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Player\OrderController::class, 'show'])->name('orders.show');
    Route::get('/cart/summary', [\App\Http\Controllers\CartController::class, 'summary'])->name('cart.summary');

    // Checkout routes
    Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/create-session', [\App\Http\Controllers\CheckoutController::class, 'createCheckoutSession'])->name('checkout.create-session');
    Route::get('/checkout/success', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [\App\Http\Controllers\CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Unified Email for all roles
    Route::prefix('mail')->name('email.')->group(function () {
        Route::get('/inbox', [MailboxController::class, 'inbox'])->name('inbox');
        Route::get('/compose', [MailboxController::class, 'compose'])->name('compose');
        Route::post('/send', [MailboxController::class, 'store'])->name('send');
        Route::get('/sent', [MailboxController::class, 'sent'])->name('sent');
        Route::get('/drafts', [MailboxController::class, 'drafts'])->name('drafts');
        Route::get('/trash', [MailboxController::class, 'trash'])->name('trash');
        Route::delete('/{id}', [MailboxController::class, 'destroy'])->name('destroy');
        Route::post('/trash/bulk-delete', [MailboxController::class, 'bulkDelete'])->name('trash.bulk-delete');
        Route::post('/trash/bulk-move', [MailboxController::class, 'bulkMoveToTrash'])->name('trash.bulk-move');
        Route::get('/show/{id}', [MailboxController::class, 'show'])->name('show');
    });

    // Backward-compat route names used in sidebars
    Route::get('/player/email', fn() => redirect()->route('email.inbox'))->name('player.email');
    Route::get('/referee/email', fn() => redirect()->route('email.inbox'))->name('referee.email');
    Route::get('/club/email', fn() => redirect()->route('email.inbox'))->name('club.email');
    Route::get('/college/email', fn() => redirect()->route('email.inbox'))->name('college.email');
    Route::get('/volunteer/email', fn() => redirect()->route('email.inbox'))->name('volunteer.email');
});

// Checkout webhook (no auth required)
Route::post('/checkout/webhook', [\App\Http\Controllers\CheckoutController::class, 'webhook'])->name('checkout.webhook');

// Public product routes
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
// Redirect the logged-in user to PlayTube with an SSO token
Route::middleware('auth')->get('/playtube/sso', function (PlaytubeSsoService $sso) {
    $token = $sso->makeToken(auth()->user());
    $url   = rtrim(config('services.playtube.url'), '/') . '/login.php?token=' . $token;
    return redirect($url);
})->name('playtube.sso');
// Player video upload (syncs to PlayTube)
Route::post('/player/videos', [VideoController::class, 'store'])->name('player.videos.store');
// College dashboard
Route::prefix('college')->name('college.')->group(function () {
    Route::get('/dashboard', [CollegeDashboardController::class, 'index'])->name('dashboard');
    Route::post('/clubs', [CollegeDashboardController::class, 'createManagedClub'])->name('clubs.create');
    Route::post('/coaches', [CollegeDashboardController::class, 'storeCoach'])->name('coaches.store');
    Route::get('/clubs/{club}/dashboard', [ClubDashboardForCollege::class, 'showForCollege'])->name('clubs.dashboard');
});

Route::get('/stripe-config', function () {
    return response()->json([
        'publicKey' => env('STRIPE_PUBLIC_KEY'),
    ]);
})->name('stripe-config');

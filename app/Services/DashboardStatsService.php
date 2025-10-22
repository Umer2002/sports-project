<?php

namespace App\Services;

use App\Models\{User, Club, Player, Team, Coach, Sport, Position, Tournament, TournamentTeam, TournamentFormat, TournamentSession, Blog, News, Task, Event, Video, Product, ProductCategory, Order, Payment, InjuryReport, PlayerTransfer, Reward, PickupGame, Donation};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    public static function getDashboardStats()
    {
        try {
            $now = Carbon::now();
            $lastMonth = $now->copy()->subMonth();
            $lastMonthStart = $lastMonth->startOfMonth();
            $lastMonthEnd = $lastMonth->endOfMonth();
            $currentMonthStart = $now->copy()->startOfMonth();

        // Basic counts
        $totalClubs = Club::count();
        $totalPlayers = Player::count();
        $totalTeams = Team::count();
        $totalCoaches = Coach::count();
        $totalUsers = User::count();

        // Revenue calculations (from payments/orders)
        $totalRevenue = Payment::sum('amount');
        $lastMonthRevenue = Payment::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');
        $currentMonthRevenue = Payment::whereBetween('created_at', [$currentMonthStart, $now])
            ->sum('amount');

        $revenueIncreasePercent = $lastMonthRevenue > 0 
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Injury reports
        $reportedInjuries = InjuryReport::count();
        $lastMonthInjuries = InjuryReport::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $currentMonthInjuries = InjuryReport::whereBetween('created_at', [$currentMonthStart, $now])->count();

        $injuriesIncreasePercent = $lastMonthInjuries > 0 
            ? round((($currentMonthInjuries - $lastMonthInjuries) / $lastMonthInjuries) * 100, 1)
            : 0;

        // Player transfers
        $playerTransfers = PlayerTransfer::count();
        $lastMonthTransfers = PlayerTransfer::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $currentMonthTransfers = PlayerTransfer::whereBetween('created_at', [$currentMonthStart, $now])->count();

        $transfersIncreasePercent = $lastMonthTransfers > 0 
            ? round((($currentMonthTransfers - $lastMonthTransfers) / $lastMonthTransfers) * 100, 1)
            : 0;

        // New registrations (users created this month)
        $newRegistrations = User::whereBetween('created_at', [$currentMonthStart, $now])->count();
        $lastMonthRegistrations = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $registrationsIncreasePercent = $lastMonthRegistrations > 0 
            ? round((($newRegistrations - $lastMonthRegistrations) / $lastMonthRegistrations) * 100, 1)
            : 0;

        // Active players (all players for now, since we don't track login activity)
        $activePlayers = Player::count();
        $lastMonthActivePlayers = Player::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $activePlayerTrendPercent = $lastMonthActivePlayers > 0 
            ? round((($activePlayers - $lastMonthActivePlayers) / $lastMonthActivePlayers) * 100, 1)
            : 0;

        // Pickup games
        $totalPickupGames = PickupGame::count();
        $activePickupGames = PickupGame::where('game_datetime', '>=', $now)->count();

        // Events/Tournaments
        $totalEvents = Event::count();
        $activeEvents = Event::where('event_date', '>=', $now)->count();
        $upcomingEvents = Event::where('event_date', '>=', $now)->count();

        // Estimated payout (based on revenue and commission structure)
        $estimatedPayout = $totalRevenue * 0.15; // Assuming 15% commission
        $lastMonthPayout = $lastMonthRevenue * 0.15;
        $currentMonthPayout = $currentMonthRevenue * 0.15;

        $payoutTrendPercent = $lastMonthPayout > 0 
            ? round((($currentMonthPayout - $lastMonthPayout) / $lastMonthPayout) * 100, 1)
            : 0;

        // Coach statistics
        $coachCount = $totalCoaches;
        $lastMonthCoaches = Coach::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $currentMonthCoaches = Coach::whereBetween('created_at', [$currentMonthStart, $now])->count();

        $coachTrendPercent = $lastMonthCoaches > 0 
            ? round((($currentMonthCoaches - $lastMonthCoaches) / $lastMonthCoaches) * 100, 1)
            : 0;

        // Team statistics
        $teamCount = $totalTeams;
        $lastMonthTeams = Team::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $currentMonthTeams = Team::whereBetween('created_at', [$currentMonthStart, $now])->count();

        $teamTrendPercent = $lastMonthTeams > 0 
            ? round((($currentMonthTeams - $lastMonthTeams) / $lastMonthTeams) * 100, 1)
            : 0;

        // Donation statistics
        $totalDonations = Donation::where('status', 'completed')->sum('amount') / 100;
        $lastMonthDonations = Donation::where('status', 'completed')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount') / 100;
        $currentMonthDonations = Donation::where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, $now])
            ->sum('amount') / 100;

        $donationsTrendPercent = $lastMonthDonations > 0 
            ? round((($currentMonthDonations - $lastMonthDonations) / $lastMonthDonations) * 100, 1)
            : 0;

        $totalDonationsCount = Donation::where('status', 'completed')->count();
        $lastMonthDonationsCount = Donation::where('status', 'completed')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $currentMonthDonationsCount = Donation::where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, $now])
            ->count();

        $donationsCountTrendPercent = $lastMonthDonationsCount > 0 
            ? round((($currentMonthDonationsCount - $lastMonthDonationsCount) / $lastMonthDonationsCount) * 100, 1)
            : 0;

        // Determine trend directions
        $payoutTrendDirection = $payoutTrendPercent >= 0 ? 'up' : 'down';
        $coachTrendDirection = $coachTrendPercent >= 0 ? 'up' : 'down';
        $teamTrendDirection = $teamTrendPercent >= 0 ? 'up' : 'down';
        $activePlayerTrendDirection = $activePlayerTrendPercent >= 0 ? 'up' : 'down';
        $donationsTrendDirection = $donationsTrendPercent >= 0 ? 'up' : 'down';
        $donationsCountTrendDirection = $donationsCountTrendPercent >= 0 ? 'up' : 'down';

        // Format currency values
        $formattedTotalRevenue = '$' . number_format($totalRevenue / 1000, 1) . 'k';
        $formattedEstimatedPayout = '$' . number_format($estimatedPayout / 1000, 1) . 'k';
        $formattedTotalDonations = '$' . number_format($totalDonations, 2);

        return [
            // Basic counts
            'totalClubs' => $totalClubs,
            'totalPlayers' => $totalPlayers,
            'totalTeams' => $totalTeams,
            'totalCoaches' => $totalCoaches,
            'totalUsers' => $totalUsers,
            'totalPickupGames' => $totalPickupGames,
            'activePickupGames' => $activePickupGames,
            'totalEvents' => $totalEvents,
            'activeEvents' => $activeEvents,
            'upcomingEvents' => $upcomingEvents,

            // Revenue stats
            'totalRevenue' => $formattedTotalRevenue,
            'revenueIncreasePercent' => $revenueIncreasePercent,

            // Injury stats
            'reportedInjuries' => $reportedInjuries,
            'injuriesIncreasePercent' => $injuriesIncreasePercent,

            // Transfer stats
            'playerTransfers' => $playerTransfers,
            'transfersIncreasePercent' => $transfersIncreasePercent,

            // Registration stats
            'newRegistrations' => $newRegistrations,
            'registrationsIncreasePercent' => $registrationsIncreasePercent,

            // Active players
            'activePlayers' => $activePlayers,
            'activePlayerTrendPercent' => $activePlayerTrendPercent,
            'activePlayerTrendDirection' => $activePlayerTrendDirection,

            // Payout stats
            'estimatedPayout' => $formattedEstimatedPayout,
            'payoutTrendPercent' => $payoutTrendPercent,
            'payoutTrendDirection' => $payoutTrendDirection,

            // Coach stats
            'coachCount' => $coachCount,
            'coachTrendPercent' => $coachTrendPercent,
            'coachTrendDirection' => $coachTrendDirection,

            // Team stats
            'teamCount' => $teamCount,
            'teamTrendPercent' => $teamTrendPercent,
            'teamTrendDirection' => $teamTrendDirection,

            // Additional stats for other dashboard sections
            'tournamentSessions' => Task::count(),
            'tournamentSessionsChange' => rand(50, 100), // Could be calculated based on task completion rates

            'scores' => rand(1000, 2000), // Could be calculated from actual game scores
            'scoresChange' => rand(50, 100),

            'teams' => $totalTeams,
            'teamsChange' => $teamTrendPercent,

            'injuryReports' => $reportedInjuries,
            'injuryReportsChange' => $injuriesIncreasePercent,

            // Donation stats
            'totalDonations' => $formattedTotalDonations,
            'donationsTrendPercent' => $donationsTrendPercent,
            'donationsTrendDirection' => $donationsTrendDirection,
            'totalDonationsCount' => $totalDonationsCount,
            'donationsCountTrendPercent' => $donationsCountTrendPercent,
            'donationsCountTrendDirection' => $donationsCountTrendDirection,

            // Pickup games stats
            'totalPickupGames' => $totalPickupGames,
            'activePickupGames' => $activePickupGames,

            // Events stats
            'totalEvents' => $totalEvents,
            'activeEvents' => $activeEvents,
            'upcomingEvents' => $upcomingEvents,
        ];
        } catch (\Exception $e) {
            // Log error and return default values
            \Log::error('Dashboard stats calculation error: ' . $e->getMessage());
            
            return [
                'totalClubs' => 0,
                'totalPlayers' => 0,
                'totalTeams' => 0,
                'totalCoaches' => 0,
                'totalUsers' => 0,
                'totalRevenue' => '$0.0k',
                'revenueIncreasePercent' => 0,
                'reportedInjuries' => 0,
                'injuriesIncreasePercent' => 0,
                'playerTransfers' => 0,
                'transfersIncreasePercent' => 0,
                'newRegistrations' => 0,
                'registrationsIncreasePercent' => 0,
                'activePlayers' => 0,
                'activePlayerTrendPercent' => 0,
                'activePlayerTrendDirection' => 'up',
                'estimatedPayout' => '$0.0k',
                'payoutTrendPercent' => 0,
                'payoutTrendDirection' => 'up',
                'coachCount' => 0,
                'coachTrendPercent' => 0,
                'coachTrendDirection' => 'up',
                'teamCount' => 0,
                'teamTrendPercent' => 0,
                'teamTrendDirection' => 'up',
                'totalPickupGames' => 0,
                'activePickupGames' => 0,
                'totalEvents' => 0,
                'activeEvents' => 0,
                'upcomingEvents' => 0,
                'tournamentSessions' => 0,
                'tournamentSessionsChange' => 0,
                'scores' => 0,
                'scoresChange' => 0,
                'teams' => 0,
                'teamsChange' => 0,
                'injuryReports' => 0,
                'injuryReportsChange' => 0,
            ];
        }
    }

    public static function getMonthlyChartData()
    {
        $monthlyPlayers = Player::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $monthlyClubs = Club::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        $playerChartLabels = $monthlyPlayers->map(fn($data) => Carbon::create($data->year, $data->month)->format('M Y'));
        $playerChartData = $monthlyPlayers->pluck('count')->map(fn($c) => (int) $c);

        $clubChartLabels = $monthlyClubs->map(fn($data) => Carbon::create($data->year, $data->month)->format('M Y'));
        $clubChartData = $monthlyClubs->pluck('count')->map(fn($c) => (int) $c);

        return [
            'playerChartLabels' => $playerChartLabels,
            'playerChartData' => $playerChartData,
            'clubChartLabels' => $clubChartLabels,
            'clubChartData' => $clubChartData,
        ];
    }

    public static function getLatestEntries()
    {
        return [
            'players' => Player::latest()->limit(5)->get(),
            'clubs' => Club::latest()->limit(5)->get(),
            'coaches' => Coach::latest()->limit(6)->get(),
            'videos' => Video::latest()->limit(3)->get(),
            'blogs' => Blog::latest()->limit(3)->get(),
            'tasks' => Task::with('user')->latest()->get(),
        ];
    }

    public static function getTournamentStats()
    {
        $latestTournament = Event::where('privacy', 'public')->orderByDesc('event_date')->first();
        $now = Carbon::now();

        $activeTournaments = Event::where('event_date', '>=', $now)->count();
        $upcomingTournaments = $activeTournaments - 1;

        // These could be calculated from actual tournament data
        $teamsRegistered = rand(10, 30);
        $approvedTeams = rand(5, $teamsRegistered);
        $pendingTeams = $teamsRegistered - $approvedTeams;
        $matchesScheduled = rand(10, 30);
        $matchesCompleted = rand(5, $matchesScheduled);
        $winnersFinalized = rand(0, 5);

        $currentTournamentName = $latestTournament->title ?? 'N/A';
        $currentTournamentFormat = $latestTournament->category ?? 'Round Robin';
        $currentTournamentDates = $latestTournament 
            ? Carbon::parse($latestTournament->event_date)->format('M d') . ' – ' . Carbon::parse($latestTournament->event_date)->addDays(4)->format('M d') 
            : 'N/A';
        $currentTournamentLocation = $latestTournament->location ?? 'N/A';
        $currentTournamentTeams = rand(6, 16);
        $currentTournamentDivisions = rand(1, 4);
        $currentTournamentStatus = 'Registration Open';
        $currentTournamentId = $latestTournament->id ?? 0;

        return [
            'activeTournaments' => $activeTournaments,
            'upcomingTournaments' => $upcomingTournaments,
            'teamsRegistered' => $teamsRegistered,
            'approvedTeams' => $approvedTeams,
            'pendingTeams' => $pendingTeams,
            'matchesScheduled' => $matchesScheduled,
            'matchesCompleted' => $matchesCompleted,
            'winnersFinalized' => $winnersFinalized,
            'currentTournamentName' => $currentTournamentName,
            'currentTournamentFormat' => $currentTournamentFormat,
            'currentTournamentDates' => $currentTournamentDates,
            'currentTournamentLocation' => $currentTournamentLocation,
            'currentTournamentTeams' => $currentTournamentTeams,
            'currentTournamentDivisions' => $currentTournamentDivisions,
            'currentTournamentStatus' => $currentTournamentStatus,
            'currentTournamentId' => $currentTournamentId,
        ];
    }

    public static function getReminders()
    {
        return [
            (object) ['text' => 'Team Briefing', 'due' => 'Today', 'color' => '#00c853'],
            (object) ['text' => 'Injury Report', 'due' => 'Due', 'color' => '#ffc107'],
            (object) ['text' => 'Check Equipment', 'due' => 'Tomorrow', 'color' => '#2196f3'],
        ];
    }
} 
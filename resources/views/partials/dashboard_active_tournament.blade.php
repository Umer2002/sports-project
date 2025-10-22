<!-- Active Tournament Card -->
<div class="active-tournament-card mt-xxl-4">
    <!-- Header -->
    <div class="tournament-header">
        <h5>Active Tournament</h5>
        <i class="fas fa-ellipsis-h"></i>
    </div>

    <!-- Search -->
    <div class="tournament-search">
        <div class="search-box">
            <input type="text" placeholder="Search" />
            <!-- <i class="fas fa-search"></i> -->
        </div>
        <button class="btn-search">Search</button>
    </div>

    <!-- Tournament Details -->
    <ul class="tournament-list">
        <li class="tournament-item">
            <div class="text-block">
                <span class="label">Tournament Name:</span>
                <span class="value">{{ $activeTournamentName ?? 'Spring Cup 2025' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>

        <li class="tournament-item">
            <div class="text-block">
                <span class="label">Format:</span>
                <span class="value">{{ $tournamentFormat ?? 'Round Robin' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>

        <li class="tournament-item">
            <div class="text-block">
                <span class="label">Dates:</span>
                <span class="value">{{ $tournamentDates ?? 'May 10 - May 14' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>

        <li class="tournament-item">
            <div class="text-block">
                <span class="label">Location:</span>
                <span class="value">{{ $tournamentLocation ?? 'Ottawa Sports Dome' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>

        <li class="tournament-item">
            <div class="text-block">
                <span class="label">Teams/Divisions:</span>
                <span class="value">{{ $tournamentTeams ?? '12 Teams. 2 Divisions' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>

        <li class="tournament-item last">
            <div class="text-block">
                <span class="label">Status:</span>
                <span class="value">{{ $tournamentStatus ?? 'Registration Open' }}</span>
            </div>
            <i class="fas fa-chevron-right"></i>
        </li>
    </ul>
</div>
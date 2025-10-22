@php
    $clubPlayers = $clubPlayers ?? collect();
    $clubTeams = $clubTeams ?? collect();
@endphp

<div class="modal fade" id="playerAttributesModal" tabindex="-1" aria-labelledby="playerAttributesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-body card container player-attr">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                    <div>
                        <div class="fw-bold player-attr-title">Player Attributes</div>
                        <p class="text-muted mb-0 small">
                            Review individual metrics and fine-tune the club attribute profile.
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <select class="form-select w-auto" id="teamSelect" data-player-team-filter>
                            <option value="">Select Team to Manage Stats</option>
                            @foreach ($clubTeams as $team)
                                <option value="{{ $team->id }}" data-sport-id="{{ $team->sport_id }}">
                                    {{ $team->name }}</option>
                            @endforeach
                        </select>
                        {{-- <button class="player-attr-btn player-attr-btn-blue" type="button">
                            Open Attribute Module
                        </button> --}}
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">
                            Close
                        </button>
                    </div>
                </div>

                <div class="player-attr-card mb-4 table-responsive">
                    <!-- Team Stats Management Section -->
                    <div id="teamStatsSection" style="display: none;">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0" style="color: #000;">Team Stats Management</h6>
                                <small class="text-muted" id="teamStatsInfo">Select a team to manage player
                                    stats</small>
                            </div>
                            <div class="card-body">
                                <div id="teamStatsContent">
                                    <!-- Content will be loaded dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Original Player Attributes Table -->
                    <div id="playerAttributesTable">
                        
                        <table class="player-attr-table mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Team</th>
                                    <th>Position</th>
                                    <th id="stat1-header">Loading...</th>
                                    <th id="stat2-header">Loading...</th>
                                    <th id="stat3-header">Loading...</th>
                                    <th id="stat4-header">Loading...</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clubPlayers as $player)
                                    <tr data-team-id="{{ optional($player->team)->id }}"
                                        data-player-name="{{ strtolower($player->name) }}">
                                        <td class="fw-semibold">{{ $player->name }}</td>
                                        <td>{{ optional($player->team)->name ?? 'Unassigned' }}</td>
                                        <td>{{ optional($player->position)->name ?? '—' }}</td>
                                        @foreach (['stat1', 'stat2', 'stat3', 'stat4'] as $metric)
                                            @php
                                                $playerStat = $player->playerStats ?? null;
                                                $value = $playerStat ? $playerStat->{$metric . '_vlaue'} : null;
                                                $display = $value !== null ? $value : '';
                                            @endphp
                                            <td>
                                                <input type="number"
                                                    class="form-control form-control-sm player-stat-input"
                                                    value="{{ $display }}" min="0" max="100"
                                                    placeholder="0-100" data-player-id="{{ $player->id }}"
                                                    data-stat="{{ $metric }}">
                                            </td>
                                        @endforeach
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary btn-primary-x" type="button"
                                                data-player-edit="{{ $player->id }}">
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            No players available. Invite players and assign them to teams to see
                                            analytics.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="player-attr-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <div class="fw-bold">Club Attribute Profile</div>
                            <small class="text-muted">Adjust target ranges to align training focus with club
                                goals.</small>
                        </div>
                        <div class="player-attr-toolbar-buttons">
                            <button class="player-attr-btn btn-dark rounded-2 small-font"
                                type="button">Balanced</button>
                            <button class="player-attr-btn btn-danger rounded-2 small-font" type="button">Attack
                                Focus</button>
                            <button class="player-attr-btn btn-secondary rounded-2 small-font" type="button">Defense
                                Focus</button>
                        </div>
                    </div>

                    <div class="row g-3">
                        @php
                            // Calculate average stats for the club
                            $clubStats = $clubPlayers->whereNotNull('playerStats')->pluck('playerStats');
                            $stat1Values = $clubStats->pluck('stat1_vlaue')->filter()->map(function($val) { return (float) $val; });
                            $stat2Values = $clubStats->pluck('stat2_vlaue')->filter()->map(function($val) { return (float) $val; });
                            $stat3Values = $clubStats->pluck('stat3_vlaue')->filter()->map(function($val) { return (float) $val; });
                            $stat4Values = $clubStats->pluck('stat4_vlaue')->filter()->map(function($val) { return (float) $val; });
                            
                            $stat1Average = $stat1Values->count() > 0 ? round($stat1Values->avg()) : 0;
                            $stat2Average = $stat2Values->count() > 0 ? round($stat2Values->avg()) : 0;
                            $stat3Average = $stat3Values->count() > 0 ? round($stat3Values->avg()) : 0;
                            $stat4Average = $stat4Values->count() > 0 ? round($stat4Values->avg()) : 0;
                            
                            $attributePresets = [
                                [
                                    'label' => $clubSportStats->stat1 ?? 'Stat 1',
                                    'progress_id' => 'clubShooting',
                                    'value' => $stat1Average,
                                    'class' => 'bg-success',
                                ],
                                [
                                    'label' => $clubSportStats->stat2 ?? 'Stat 2',
                                    'progress_id' => 'clubPassing',
                                    'value' => $stat2Average,
                                    'class' => 'bg-warning',
                                ],
                                [
                                    'label' => $clubSportStats->stat3 ?? 'Stat 3',
                                    'progress_id' => 'clubSpeed',
                                    'value' => $stat3Average,
                                    'class' => 'bg-primary',
                                ],
                                [
                                    'label' => $clubSportStats->stat4 ?? 'Stat 4',
                                    'progress_id' => 'clubStamina',
                                    'value' => $stat4Average,
                                    'class' => 'bg-gradient',
                                ],
                            ];
                        @endphp

                        @foreach ($attributePresets as $preset)
                            <div class="col-md-6">
                                <div class="player-attr-card h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="player-attr-label">{{ $preset['label'] }}</div>
                                        <small id="{{ $preset['progress_id'] }}Value">{{ $preset['value'] }}%</small>
                                    </div>
                                    <div class="player-attr-progress">
                                        <div class="player-attr-progress-bar {{ $preset['class'] }}"
                                            id="{{ $preset['progress_id'] }}Bar"
                                            style="width: {{ $preset['value'] }}%"></div>
                                    </div>
                                    <input type="range" class="form-range" min="0" max="100" style="background: none;"
                                           value="{{ $preset['value'] }}"
                                           data-progress-control="{{ $preset['progress_id'] }}">
                                    
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-end mt-3">
                        <button class="player-attr-btn theme-btn" type="button" style="color: #ffffff; !important">
                            Apply All Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('playerAttributesModal');
            if (!modal) return;

            const teamFilter = modal.querySelector('[data-player-team-filter]');
            const playerRows = Array.from(modal.querySelectorAll('tbody tr[data-team-id]'));

            teamFilter?.addEventListener('change', function() {
                const teamId = teamFilter.value;
                if (teamId) {
                    loadTeamPlayersWithStats(teamId);
                } else {
                    // Show all players
                    playerRows.forEach((row) => {
                        row.style.display = '';
                    });
                    // Hide stats section
                    document.getElementById('teamStatsSection').style.display = 'none';
                    document.getElementById('playerAttributesTable').style.display = 'block';
                }
            });

            modal.addEventListener('shown.bs.modal', function() {
                teamFilter?.focus();
                // Load club's sport stats configuration from server-side data
                loadClubSportStatsFromData();
            });

            modal.querySelectorAll('[data-progress-control]').forEach((slider) => {
                slider.addEventListener('input', function() {
                    const key = slider.getAttribute('data-progress-control');
                    const bar = document.getElementById(key + 'Bar');
                    const label = document.getElementById(key + 'Value');
                    if (!bar || !label) return;
                    bar.style.width = slider.value + '%';
                    label.textContent = slider.value + '%';
                });
            });
            
            // Add event listeners to player stat inputs to update club averages
            modal.addEventListener('input', function(e) {
                if (e.target.classList.contains('player-stat-input')) {
                    updateClubAverages();
                }
            });

        });

        // Load team players with stats (global function)
        function loadTeamPlayersWithStats(teamId) {
            const teamStatsSection = document.getElementById('teamStatsSection');
            const playerAttributesTable = document.getElementById('playerAttributesTable');
            const teamStatsContent = document.getElementById('teamStatsContent');
            const teamStatsInfo = document.getElementById('teamStatsInfo');

            // Show loading state
            teamStatsInfo.textContent = 'Loading team players and stats...';
            teamStatsContent.innerHTML =
                '<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>';
            teamStatsSection.style.display = 'block';
            playerAttributesTable.style.display = 'none';

            // Fetch team players and stats
            fetch(`/club/team/${teamId}/players-stats`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayTeamStatsForm(data.players, data.configured_stats, teamId);
                        teamStatsInfo.textContent = `Managing stats for ${data.players.length} players`;
                    } else {
                        teamStatsContent.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ${data.message}
                            </div>
                        `;
                        teamStatsInfo.textContent = 'No stats configured for this sport';
                    }
                })
                .catch(error => {
                    console.error('Error loading team stats:', error);
                    teamStatsContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Error loading team data. Please try again.
                        </div>
                    `;
                    teamStatsInfo.textContent = 'Error loading team data';
                });
        }

        // Display team stats form (global function)
        function displayTeamStatsForm(players, configuredStats, teamId) {
            const teamStatsContent = document.getElementById('teamStatsContent');

            let html = `
                <form id="teamStatsForm">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 style="color:#000;">Configured Stats for this Sport:</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary">${configuredStats.stat1}</span>
                                <span class="badge bg-primary">${configuredStats.stat2}</span>
                                <span class="badge bg-primary">${configuredStats.stat3}</span>
                                <span class="badge bg-primary">${configuredStats.stat4}</span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Position</th>
                                    <th>${configuredStats.stat1}</th>
                                    <th>${configuredStats.stat2}</th>
                                    <th>${configuredStats.stat3}</th>
                                    <th>${configuredStats.stat4}</th>
                                </tr>
                            </thead>
                            <tbody>
            `;

            players.forEach(player => {
                const existingStats = player.existing_stats || {};
                html += `
                    <tr>
                        <td>
                            <div class="fw-semibold">${player.name}</div>
                        </td>
                        <td>${player.position?.name || '—'}</td>
                        <td>
                            <input type="number" 
                                   name="player_stats[${player.id}][stat1]" 
                                   class="form-control form-control-sm" 
                                   value="${existingStats.stat1 || ''}" 
                                   min="0" max="100" 
                                   placeholder="0-100">
                        </td>
                        <td>
                            <input type="number" 
                                   name="player_stats[${player.id}][stat2]" 
                                   class="form-control form-control-sm" 
                                   value="${existingStats.stat2 || ''}" 
                                   min="0" max="100" 
                                   placeholder="0-100">
                        </td>
                        <td>
                            <input type="number" 
                                   name="player_stats[${player.id}][stat3]" 
                                   class="form-control form-control-sm" 
                                   value="${existingStats.stat3 || ''}" 
                                   min="0" max="100" 
                                   placeholder="0-100">
                        </td>
                        <td>
                            <input type="number" 
                                   name="player_stats[${player.id}][stat4]" 
                                   class="form-control form-control-sm" 
                                   value="${existingStats.stat4 || ''}" 
                                   min="0" max="100" 
                                   placeholder="0-100">
                        </td>
                    </tr>
                `;
            });

            html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="resetTeamStats()">
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary theme-btn">
                            <i class="bi bi-save me-2"></i>Save Player Stats
                        </button>
                    </div>
                </form>
            `;

            teamStatsContent.innerHTML = html;

            // Add form submit handler after form is created
            const form = document.getElementById('teamStatsForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    saveTeamStats(teamId);
                });
            }
            
            // Update club averages after form is loaded
            setTimeout(() => {
                updateClubAverages();
            }, 100);
        }

        // Save team stats (global function)
        function saveTeamStats(teamId) {
            const form = document.getElementById('teamStatsForm');
            const formData = new FormData(form);
            const playerStats = {};

            // Convert form data to the expected format
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('player_stats[')) {
                    const match = key.match(/player_stats\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const playerId = match[1];
                        const statField = match[2];

                        if (!playerStats[playerId]) {
                            playerStats[playerId] = {
                                player_id: parseInt(playerId)
                            };
                        }
                        playerStats[playerId][statField] = value ? parseInt(value) : null;
                    }
                }
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            submitBtn.disabled = true;

            // Send data to server
            fetch('/club/player-stats/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        player_stats: Object.values(playerStats)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const teamStatsContent = document.getElementById('teamStatsContent');
                        teamStatsContent.innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            ${data.message}
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" onclick="loadTeamPlayersWithStats(${teamId})">
                                <i class="bi bi-arrow-clockwise me-2"></i>Refresh Stats
                            </button>
                        </div>
                    `;
                    } else {
                        alert('Error saving stats: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving stats:', error);
                    alert('Error saving stats. Please try again.');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Reset team stats form
        window.resetTeamStats = function() {
            const form = document.getElementById('teamStatsForm');
            if (form) {
                form.reset();
            }
        };

        // Load club's sport stats configuration from server-side data
        function loadClubSportStatsFromData() {
            @if (isset($clubSportStats) && $clubSportStats)
                const stats = {
                    stat1: '{{ $clubSportStats->stat1 }}',
                    stat2: '{{ $clubSportStats->stat2 }}',
                    stat3: '{{ $clubSportStats->stat3 }}',
                    stat4: '{{ $clubSportStats->stat4 }}'
                };
                updateTableHeaders(stats);
                
                // Update club averages
                updateClubAverages();
            @else
                // No stats configured, keep default headers
                console.log('No sport stats configured for this club');
            @endif
        }
        
        // Update club averages based on current player stats
        function updateClubAverages() {
            const statInputs = document.querySelectorAll('.player-stat-input');
            const statValues = {
                stat1: [],
                stat2: [],
                stat3: [],
                stat4: []
            };
            
            // Collect all stat values
            statInputs.forEach(input => {
                const stat = input.getAttribute('data-stat');
                const value = parseFloat(input.value);
                if (!isNaN(value) && value > 0) {
                    statValues[stat].push(value);
                }
            });
            
            // Calculate and update averages
            Object.keys(statValues).forEach(stat => {
                const values = statValues[stat];
                if (values.length > 0) {
                    const average = Math.round(values.reduce((sum, val) => sum + val, 0) / values.length);
                    updateClubStatDisplay(stat, average);
                }
            });
        }
        
        // Update individual club stat display
        function updateClubStatDisplay(stat, average) {
            const statMapping = {
                'stat1': 'clubShooting',
                'stat2': 'clubPassing', 
                'stat3': 'clubSpeed',
                'stat4': 'clubStamina'
            };
            
            const progressId = statMapping[stat];
            if (progressId) {
                const bar = document.getElementById(progressId + 'Bar');
                const label = document.getElementById(progressId + 'Value');
                const slider = document.querySelector(`[data-progress-control="${progressId}"]`);
                
                if (bar) bar.style.width = average + '%';
                if (label) label.textContent = average + '%';
                if (slider) slider.value = average;
            }
        }

        // Load club's sport stats configuration
        function loadClubSportStats() {
            // Get the club's sport ID from the first team (assuming all teams in a club have the same sport)
            const teamSelect = document.getElementById('teamSelect');
            const firstTeamOption = teamSelect.querySelector('option[data-sport-id]');

            if (firstTeamOption) {
                const sportId = firstTeamOption.getAttribute('data-sport-id');
                fetchClubSportStats(sportId);
            } else {
                // Fallback: try to get sport from club data
                fetch('/club/teams-json')
                    .then(response => response.json())
                    .then(data => {
                        if (data.teams && data.teams.length > 0) {
                            // Get sport ID from the first team
                            const firstTeam = data.teams[0];
                            if (firstTeam.sport_id) {
                                fetchClubSportStats(firstTeam.sport_id);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading club teams:', error);
                        // Keep default headers
                    });
            }
        }

        // Fetch club sport stats configuration
        function fetchClubSportStats(sportId) {
            fetch(`/admin/player-stats/sport/${sportId}/stats`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.stats) {
                        updateTableHeaders(data.stats);
                    }
                })
                .catch(error => {
                    console.error('Error loading sport stats:', error);
                    // Keep default headers
                });
        }

        // Update table headers with configured stats
        function updateTableHeaders(stats) {
            const headers = ['stat1', 'stat2', 'stat3', 'stat4'];
            headers.forEach((statKey, index) => {
                const headerElement = document.getElementById(`${statKey}-header`);
                if (headerElement && stats[statKey]) {
                    headerElement.textContent = stats[statKey];
                }
            });
        }

        // Save all player stats functionality
        document.getElementById('saveAllPlayerStats')?.addEventListener('click', function() {
            saveAllPlayerStats();
        });

        // Save all player stats
        function saveAllPlayerStats() {
            const players = @json($clubPlayers);
            const clubSportStats = @json($clubSportStats);

            if (!clubSportStats) {
                alert('No sport stats configured. Please configure stats for this sport first.');
                return;
            }

            // Collect all player stats from the input fields
            const statInputs = document.querySelectorAll('.player-stat-input');
            const playerStatsMap = {};

            statInputs.forEach(input => {
                const playerId = input.getAttribute('data-player-id');
                const stat = input.getAttribute('data-stat');
                const value = input.value ? parseInt(input.value) : null;

                if (!playerStatsMap[playerId]) {
                    playerStatsMap[playerId] = {
                        player_id: parseInt(playerId)
                    };
                }
                playerStatsMap[playerId][stat] = value;
            });

            // Convert map to array
            const playerStats = Object.values(playerStatsMap);

            // Show loading state
            const saveBtn = document.getElementById('saveAllPlayerStats');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            saveBtn.disabled = true;

            // Send data to server
            fetch('/club/player-stats/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        player_stats: playerStats
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Reload the page to show updated stats
                        location.reload();
                    } else {
                        alert('Error saving stats: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error saving stats:', error);
                    alert('Error saving stats. Please try again.');
                })
                .finally(() => {
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                });
        }
    </script>

    @push('styles')
        <style>
            .player-stat-input {
                width: 80px;
                text-align: center;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: white;
            }

            .player-stat-input:focus {
                background: rgba(255, 255, 255, 0.15);
                border-color: #0d6efd;
                color: white;
                box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            }

            .player-stat-input::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

        </style>
    @endpush

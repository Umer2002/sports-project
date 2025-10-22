<div class="card">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-header">
        <h5 class="mb-0">🧾 Step 1: Player & Team Info</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Select Player</label>
                <select name="player_id" class="form-control select2">
                    <option value="">Select Player</option>
                    @foreach (App\Models\Player::all() as $player)
                        <option value="{{ $player->id }}"
                            {{ old('player_id', $report->player_id ?? '') == $player->id ? 'selected' : '' }}>
                            {{ $player->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Date & Time of Injury</label>
                <input type="datetime-local" name="injury_datetime" class="form-control"
                    value="{{ old('injury_datetime', isset($report) && $report->injury_datetime ? $report->injury_datetime->format('Y-m-d\TH:i') : '') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Team Name</label>
                <select name="team_name" id="team_name" class="form-control" required>
                    <option value="">Select Team</option>
                    @foreach (\App\Models\Team::all() as $team)
                        <option value="{{ $team->name }}" {{ old('team_name', $report->team_name ?? '') == $team->name ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Location</label>
                <input type="text" name="location" class="form-control"
                    placeholder="e.g., Field 3 or Away Game - Kingston"
                    value="{{ old('location', $report->location ?? '') }}">
            </div>
        </div>
    </div>
</div>


<script>
    document.querySelector('select[name="player_id"]').addEventListener('change', function () {
        const playerId = this.value;
        const teamSelect = document.getElementById('team_name');
        const currentTeamName = '{{ old("team_name", $report->team_name ?? "") }}';

        if (!teamSelect) return;

        teamSelect.innerHTML = '<option value=\"\">Loading...</option>';

        if (playerId) {
            fetch(`/admin/player-teams/${playerId}`)
                .then(res => res.json())
                .then(data => {
                    teamSelect.innerHTML = '';
                    if (data.teams.length) {
                        data.teams.forEach(name => {
                            const isSelected = name === currentTeamName ? 'selected' : '';
                            teamSelect.innerHTML += `<option ${isSelected} value=\"${name}\">${name}</option>`;
                        });
                    } else {
                        teamSelect.innerHTML = '<option value=\"\">No team assigned</option>';
                    }
                });
        } else {
            teamSelect.innerHTML = '<option value=\"\">Select Team</option>';
        }
    });
</script>

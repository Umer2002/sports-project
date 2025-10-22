<div class="mb-3">
    <label for="name" class="form-label">Name *</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $tournamentFormat->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="type" class="form-label">Type *</label>
    <select name="type" id="type" class="form-select" required>
        <option value="">Select Type</option>
        <option value="round_robin" {{ old('type', $tournamentFormat->type ?? '') == 'round_robin' ? 'selected' : '' }}>Round Robin</option>
        <option value="group" {{ old('type', $tournamentFormat->type ?? '') == 'group' ? 'selected' : '' }}>Group</option>
        <option value="knockout" {{ old('type', $tournamentFormat->type ?? '') == 'knockout' ? 'selected' : '' }}>Knockout</option>
    </select>
</div>

<div class="mb-3">
    <label for="games_per_team" class="form-label">Games per Team</label>
    <input type="number" class="form-control" id="games_per_team" name="games_per_team" value="{{ old('games_per_team', $tournamentFormat->games_per_team ?? '') }}">
</div>

<div class="mb-3">
    <label for="group_count" class="form-label">Group Count</label>
    <input type="number" class="form-control" id="group_count" name="group_count" value="{{ old('group_count', $tournamentFormat->group_count ?? '') }}">
</div>

<div class="mb-3">
    <label for="elimination_type" class="form-label">Elimination Type</label>
    <input type="text" class="form-control" id="elimination_type" name="elimination_type" value="{{ old('elimination_type', $tournamentFormat->elimination_type ?? '') }}">
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $tournamentFormat->description ?? '') }}</textarea>
</div>

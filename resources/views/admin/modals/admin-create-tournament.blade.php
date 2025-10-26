@php
    $tournamentStoreAction = route('club.tournaments.store-modal');
    $clubTeams = $clubTeams ?? collect();
    $countries = \App\Models\Country::orderBy('name')->get(['id', 'name']);
    $states = collect();
    $cities = collect();
@endphp

<div class="modal fade" id="createTournamentModal" tabindex="-1" aria-labelledby="createTournamentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content px-5" style="padding: 20px !important;">
            <div class="modal-header">
                <h5 class="modal-title">Create Tournament</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body px-5">

                <form id="createTournamentForm"
                      action="{{ $tournamentStoreAction }}"
                      method="POST"
                      class="d-flex flex-column gap-4"
                      data-modal-form="create-tournament"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="sport_id" value="{{ auth()->user()->sport_id }}">
                    <div class="d-flex flex-column gap-3">
                        <div class="cr-box-title text-uppercase small text-muted">Basic Info</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="tournament_name">Tournament Name</label>
                                <input type="text" class="form-control" id="tournament_name" name="name" required
                                       placeholder="Spring Invitational Cup">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="tournament_format_id">Format</label>
                                <select class="form-select" id="tournament_format_id" name="tournament_format_id" required>
                                    <option value="">Select Format</option>
                                    <option value="1">Round Robin</option>
                                    <option value="2">Knockout</option>
                                    <option value="3">Group Stage</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required
                                       placeholder="Home Complex">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"
                                          placeholder="Tournament description"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="cr-box-title text-uppercase small text-muted">Scheduling</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required
                                       min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required
                                       min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="registration_cutoff_date">Registration Deadline</label>
                                <input type="date" class="form-control" id="registration_cutoff_date" name="registration_cutoff_date" required
                                       min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="joining_fee">Entry Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" min="0" step="0.01" class="form-control" id="joining_fee" name="joining_fee" required
                                           placeholder="150.00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="joining_type">Fee Type</label>
                                <select class="form-select" id="joining_type" name="joining_type" required>
                                    <option value="">Select Type</option>
                                    <option value="per_team">Per Team</option>
                                    <option value="per_club">Per Club</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="cr-box-title text-uppercase small text-muted">Location Details</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="country_id">Country</label>
                                <select class="form-select" id="country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="state_id">State/Province</label>
                                <select class="form-select" id="state_id" name="state_id" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="city_id">City</label>
                                <select class="form-select" id="city_id" name="city_id" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="cr-box-title text-uppercase small text-muted">Club Participation</div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Teams to Include</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select_all_teams">
                                    <label class="form-check-label" for="select_all_teams">
                                        Select All Club Teams
                                    </label>
                                </div>
                                <div class="row mt-2">
                                    @foreach ($clubTeams as $team)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input team-checkbox" type="checkbox" 
                                                       id="team_{{ $team->id }}" 
                                                       name="team_ids[]" 
                                                       value="{{ $team->id }}">
                                                <label class="form-check-label" for="team_{{ $team->id }}">
                                                    {{ $team->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="cr-box-title text-uppercase small text-muted">Additional Information</div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label" for="description">Tournament Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                          placeholder="Share schedule highlights, venue details, or expectations."></textarea>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="createTournamentForm" class="btn btn-primary" id="submitBtn">
                    <i class="bi bi-trophy-fill me-1"></i>
                    <span class="btn-text">Launch Tournament</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Country change handler
    document.getElementById('country_id').addEventListener('change', function() {
        const countryId = this.value;
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');
        
        // Clear states and cities
        stateSelect.innerHTML = '<option value="">Select State</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        if (countryId) {
            fetch(`/api/states/${countryId}`)
                .then(response => response.json())
                .then(states => {
                    states.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.id;
                        option.textContent = state.name;
                        stateSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading states:', error));
        }
    });
    
    // State change handler
    document.getElementById('state_id').addEventListener('change', function() {
        const stateId = this.value;
        const citySelect = document.getElementById('city_id');
        
        // Clear cities
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        if (stateId) {
            fetch(`/api/cities/${stateId}`)
                .then(response => response.json())
                .then(cities => {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading cities:', error));
        }
    });
    
    // Select all teams handler
    document.getElementById('select_all_teams').addEventListener('change', function() {
        const teamCheckboxes = document.querySelectorAll('.team-checkbox');
        teamCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Individual team checkbox handler
    document.querySelectorAll('.team-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.team-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.team-checkbox:checked');
            const selectAllCheckbox = document.getElementById('select_all_teams');
            
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        });
    });
    
    // Form validation and submission
    document.getElementById('createTournamentForm').addEventListener('submit', function(e) {
        const teamCheckboxes = document.querySelectorAll('.team-checkbox:checked');
        if (teamCheckboxes.length < 2) {
            e.preventDefault();
            alert('Please select at least 2 teams for the tournament.');
            return false;
        }
        
        // Validate required fields
        const requiredFields = ['name', 'tournament_format_id', 'start_date', 'end_date', 'registration_cutoff_date', 'location', 'country_id', 'state_id', 'city_id', 'joining_fee', 'joining_type'];
        for (const fieldName of requiredFields) {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                e.preventDefault();
                alert(`Please fill in the ${fieldName.replace('_', ' ')} field.`);
                field.focus();
                return false;
            }
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.spinner-border');
        
        submitBtn.disabled = true;
        btnText.textContent = 'Creating Tournament...';
        spinner.classList.remove('d-none');
    });
});
</script>

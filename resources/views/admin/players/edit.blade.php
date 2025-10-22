@extends('layouts.admin')

@section('title', isset($player) ? 'Edit Player' : 'Create Player')

@section('header_styles')
<style>
    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    .form-group.is-invalid .form-check-radio {
        color: #dc3545;
    }
    
    .form-group.is-invalid .form-check-radio input[type="radio"] {
        border-color: #dc3545;
    }
    
    .validation-summary {
        margin-bottom: 1rem;
    }
    
    .validation-summary ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }
    
    .validation-summary li {
        margin-bottom: 0.25rem;
    }
    
    .btn:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }
    
    .form-text {
        font-size: 0.875em;
        color: #6c757d;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>{{ isset($player) ? 'Edit Player' : 'Create Player' }}</h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.players.update', $player) }}" method="POST" enctype="multipart/form-data" id="playerEditForm" novalidate>
                        @csrf
                        @method('PUT')
                        @php
                            $fields = [
                                'name' => ['label' => 'Player Name', 'type' => 'text', 'required' => true, 'minlength' => 2, 'maxlength' => 255],
                                'email' => ['label' => 'Email', 'type' => 'email', 'required' => true],
                                'paypal_link' => ['label' => 'PayPal Link', 'type' => 'url', 'required' => false],
                                'password' => ['label' => 'Password', 'type' => 'password', 'required' => false, 'minlength' => 6],
                                'password_confirmation' => ['label' => 'Confirm Password', 'type' => 'password', 'required' => false, 'minlength' => 6],
                                'phone' => ['label' => 'Phone Number', 'type' => 'tel', 'required' => false],
                                'city' => ['label' => 'City', 'type' => 'text', 'required' => false, 'maxlength' => 100],
                                'state' => ['label' => 'State', 'type' => 'text', 'required' => false, 'maxlength' => 100],
                                'zip_code' => ['label' => 'Zip Code', 'type' => 'text', 'required' => false],
                                'nationality' => ['label' => 'Nationality', 'type' => 'text', 'required' => false, 'maxlength' => 100],
                                'height' => ['label' => 'Height (cm)', 'type' => 'number', 'required' => false, 'min' => 50, 'max' => 300, 'step' => '0.1'],
                                'weight' => ['label' => 'Weight (kg)', 'type' => 'number', 'required' => false, 'min' => 20, 'max' => 500, 'step' => '0.1'],
                                'debut' => ['label' => 'Debut Date', 'type' => 'date', 'required' => false],
                                'jersey_no' => ['label' => 'Jersey Number', 'type' => 'number', 'required' => false, 'min' => 0, 'max' => 999],
                                'college' => ['label' => 'College', 'type' => 'text', 'required' => false, 'maxlength' => 255],
                                'university' => ['label' => 'University', 'type' => 'text', 'required' => false, 'maxlength' => 255],
                                'referee_affiliation' => ['label' => 'Referee Affiliation', 'type' => 'text', 'required' => false, 'maxlength' => 255],
                            ];
                        @endphp

                        @php $i = 0; @endphp
                        <div class="row">
                            @foreach ($fields as $field => $config)
                                <div class="col-md-4">
                                    <h2 class="card-inside-title">{{ $config['label'] }}</h2>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input
                                                type="{{ $config['type'] }}"
                                                name="{{ $field }}" 
                                                value="{{ old($field, $player->$field ?? '') }}"
                                                class="form-control"
                                                {{ $config['required'] ? 'required' : '' }}
                                                {{ isset($config['min']) ? 'min="' . $config['min'] . '"' : '' }}
                                                {{ isset($config['max']) ? 'max="' . $config['max'] . '"' : '' }}
                                                {{ isset($config['minlength']) ? 'minlength="' . $config['minlength'] . '"' : '' }}
                                                {{ isset($config['maxlength']) ? 'maxlength="' . $config['maxlength'] . '"' : '' }}
                                                {{ isset($config['step']) ? 'step="' . $config['step'] . '"' : '' }}
                                                {{ isset($config['pattern']) ? 'pattern="' . $config['pattern'] . '"' : '' }}
                                                placeholder="Enter {{ strtolower($config['label']) }}"
                                                data-field="{{ $field }}">
                                            <div class="invalid-feedback" id="error-{{ $field }}"></div>
                                        </div>
                                    </div>
                                </div>

                                @php $i++; @endphp
                                @if ($i % 3 == 0)
                        </div>
                        <div class="row">
                            @endif
                            @endforeach
                        </div>

                        <div class="row">
                            {{-- Dropdowns --}}
                            @foreach (['position_id' => $positions, 'club_id' => $clubs, 'sport_id' => $sports, 'team_id' => $teams] as $key => $list)
                                <div class="col-3">
                                    <h2 class="card-inside-title">{{ ucfirst(str_replace('_', ' ', $key)) }}</h2>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <select name="{{ $key }}" class="form-control"
                                                id="{{ $key }}" required data-field="{{ $key }}">
                                                <option value="">Select {{ ucfirst(str_replace('_', ' ', $key)) }}
                                                </option>
                                                @foreach ($list as $item)
                                                    <option value="{{ $item->id }}"
                                                        {{ old($key, $player->$key) == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name ?? $item->position_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error-{{ $key }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            {{-- Gender --}}
                            <div class="col-md-3">
                                <h2 class="card-inside-title">Gender</h2>
                                <div class="form-group">
                                    @foreach (['male', 'female'] as $gender)
                                        <div class="form-check form-check-radio">
                                            <label>
                                                <input name="gender" type="radio" value="{{ $gender }}"
                                                    {{ old('gender', $player->gender) == $gender ? 'checked' : '' }}
                                                    data-field="gender" />
                                                <span>{{ ucfirst($gender) }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                    <div class="invalid-feedback" id="error-gender"></div>
                                </div>
                            </div>

                            {{-- Address & Bio --}}
                            @foreach (['address' => 'Address', 'bio' => 'Bio'] as $key => $label)
                                <div class="col-md-3">
                                    <h2 class="card-inside-title">{{ $label }}</h2>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <textarea name="{{ $key }}" class="form-control no-resize" rows="2" 
                                                data-field="{{ $key }}" maxlength="1000">{{ old($key, $player->$key) }}</textarea>
                                            <div class="invalid-feedback" id="error-{{ $key }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Birthday & Age --}}
                            <div class="col-md-3">
                                <h2 class="card-inside-title">Birthday</h2>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="date" name="birthday" class="form-control"
                                            value="{{ old('birthday', $player->birthday) }}" 
                                            data-field="birthday" max="{{ date('Y-m-d') }}">
                                        <div class="invalid-feedback" id="error-birthday"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h2 class="card-inside-title">Age</h2>
                            <div class="form-group">
                                <div class="form-line">
                                    <input type="number" name="age" class="form-control"
                                        value="{{ old('age', $player->age) }}" 
                                        data-field="age" min="5" max="120">
                                    <div class="invalid-feedback" id="error-age"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Social Links --}}

                        @php
                            $socials = is_array($player->social_links)
                                ? $player->social_links
                                : json_decode($player->social_links, true) ?? [];
                        @endphp

                        <div class="row">
                            <h2 class="card-inside-title">Social Links</h2>
                            @foreach (['facebook', 'twitter', 'instagram', 'tiktok', 'youtube', 'linkedin', 'snapchat', 'pinterest', 'reddit'] as $platform)
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ ucfirst($platform) }}</label>
                                        <div class="form-line">
                                            <input type="url" class="form-control"
                                                name="social_links[{{ $platform }}]"
                                                value="{{ old('social_links.' . $platform, $socials[$platform] ?? '') }}"
                                                placeholder="Enter {{ ucfirst($platform) }} URL"
                                                data-field="social_links_{{ $platform }}"
                                                pattern="https?://.+">
                                            <div class="invalid-feedback" id="error-social_links_{{ $platform }}"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="card-inside-title">Associated Ads</h2>
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="ads[]" class="form-control" multiple>
                                            @foreach($ads as $ad)
                                                <option value="{{ $ad->id }}" {{ (isset($player) && $player->ads->contains($ad->id)) ? 'selected' : '' }}>{{ $ad->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple ads.</small>
                                </div>
                            </div>
                        </div>

                        {{-- Player Image --}}
                        <h2 class="card-inside-title">Upload Photo</h2>
                        <div class="form-group">
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>Choose File</span>
                                    <input type="file" name="photo" accept="image/*" data-field="photo">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload player photo">
                                </div>
                                <div class="invalid-feedback" id="error-photo"></div>
                                <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                            </div>
                        </div>

                        {{-- Dynamic Stats --}}
                        <h2 class="card-inside-title">Player Stats</h2>
                        
                        @php
                            $playerStats = $player->stats->pluck('value', 'stat_id')->toArray();
                            $statIds = $player->stats->pluck('stat_id')->toArray();
                            $statValues = array_values($statIds);
                            
                            // Get the first 3 stat IDs for the dropdowns
                            $stat1Id = isset($statValues[0]) ? $statValues[0] : null;
                            $stat2Id = isset($statValues[1]) ? $statValues[1] : null;
                            $stat3Id = isset($statValues[2]) ? $statValues[2] : null;
                            
                            // Debug information - uncomment to debug
                            // dd([
                            //     'player_stats' => $player->stats,
                            //     'player_stats_array' => $playerStats,
                            //     'stat_ids' => $statIds,
                            //     'stat_values' => $statValues,
                            //     'available_stats' => $stats,
                            //     'player_sport_id' => $player->sport_id
                            // ]);
                        @endphp
                        
                        {{-- Debug Info --}}
                         
                        <div id="stats-container"></div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_1" class="form-control" id="stat_1">
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}" {{ ($stat1Id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_1"
                                    value="{{ old('stat_value_1', $stat1Id ? $playerStats[$stat1Id] : '') }}" placeholder="Enter Stat">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_2" class="form-control" id="stat_2">
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}" {{ ($stat2Id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_2"
                                    value="{{ old('stat_value_2', $stat2Id ? $playerStats[$stat2Id] : '') }}" placeholder="Enter Stat">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_3" class="form-control" id="stat_3">
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}" {{ ($stat3Id == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_3"
                                    value="{{ old('stat_value_3', $stat3Id ? $playerStats[$stat3Id] : '') }}" placeholder="Enter Stat">
                            </div>
                        </div>
                        @php
                            $playerRewards = $player->rewards->pluck('reward_id')->toArray();
                        @endphp
                        <h2 class="card-inside-title">Player Rewards</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="rewards[]" class="form-control" id="rewards" required multiple>
                                            @foreach ($rewards as $s)
                                                <option value="{{ $s->id }}" {{ in_array($s->id, $playerRewards) ? 'selected' : '' }}>{{ $s->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>


                        {{-- Password Strength Indicator --}}
                        <div class="row" id="password-strength-container" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password Strength</label>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="form-text" id="password-strength-text">Enter a password to see strength</small>
                                </div>
                            </div>
                        </div>

                        {{-- Validation Summary --}}
                        <div class="alert alert-danger d-none" id="validation-summary">
                            <h6>Please correct the following errors:</h6>
                            <ul id="validation-errors-list"></ul>
                        </div>

                        {{-- Success Message --}}
                        <div class="alert alert-success d-none" id="success-message">
                            <i class="fas fa-check-circle"></i> Form validation passed! Submitting...
                        </div>

                        <button type="submit"
                            class="btn btn-primary waves-effect" id="submitBtn">{{ isset($player) ? 'Edit Player' : 'Create Player' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS to load stats dynamically --}}
    <script>
        const existingStats = @json($player->stats->pluck('value', 'stat_id') ?? []);

        function loadStats(sportId) {
            fetch(`/admin/players/stats-by-sport/${sportId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const statsContainer = document.getElementById('stats-container');
                    statsContainer.innerHTML = '';

                    if (Array.isArray(data.stats) && data.stats.length > 0) {
                        data.stats.forEach(stat => {
                            const val = existingStats[stat.id] ?? '';
                            statsContainer.innerHTML += `
                        <div class="form-group">
                            <label>${stat.name}</label>
                            <div class="form-line">
                                <input type="number" step="0.1" name="stats[${stat.id}]" class="form-control" value="${val}" data-field="stats_${stat.id}">
                                <div class="invalid-feedback" id="error-stats_${stat.id}"></div>
                            </div>
                        </div>
                    `;
                        });
                    } else {
                        statsContainer.innerHTML = '<div class="alert alert-info">No stats available for this sport.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    const statsContainer = document.getElementById('stats-container');
                    statsContainer.innerHTML = '<div class="alert alert-warning">Unable to load stats for this sport. Please try again.</div>';
                });
        }

        // Validation functions
        const validators = {
            name: (value) => {
                if (!value.trim()) return 'Player name is required';
                if (value.length < 2) return 'Player name must be at least 2 characters';
                if (value.length > 255) return 'Player name must be less than 255 characters';
                return null;
            },
            email: (value) => {
                if (!value.trim()) return 'Email is required';
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) return 'Please enter a valid email address';
                return null;
            },
            phone: (value) => {
                // Phone validation removed - accept any format
                return null;
            },
            height: (value) => {
                if (value && value.trim()) {
                    const height = parseFloat(value);
                    if (isNaN(height) || height < 50 || height > 300) return 'Height must be between 50 and 300 cm';
                }
                return null;
            },
            weight: (value) => {
                if (value && value.trim()) {
                    const weight = parseFloat(value);
                    if (isNaN(weight) || weight < 20 || weight > 500) return 'Weight must be between 20 and 500 kg';
                }
                return null;
            },
            jersey_no: (value) => {
                if (value && value.trim()) {
                    const jersey = parseInt(value);
                    if (isNaN(jersey) || jersey < 0 || jersey > 999) return 'Jersey number must be between 0 and 999';
                }
                return null;
            },
            age: (value) => {
                if (value && value.trim()) {
                    const age = parseInt(value);
                    if (isNaN(age) || age < 5 || age > 120) return 'Age must be between 5 and 120';
                }
                return null;
            },
            birthday: (value) => {
                if (value && value.trim()) {
                    const birthday = new Date(value);
                    const today = new Date();
                    if (birthday > today) return 'Birthday cannot be in the future';
                }
                return null;
            },
            zip_code: (value) => {
                // ZIP code validation removed - accept any format
                return null;
            },
            password_confirmation: (value) => {
                if (value && value.trim()) {
                    const password = document.querySelector('input[name="password"]').value;
                    if (value !== password) return 'Password confirmation does not match';
                }
                return null;
            },
            password: (value) => {
                if (value && value.trim()) {
                    if (value.length < 6) return 'Password must be at least 6 characters';
                    // Check password strength
                    const hasUpperCase = /[A-Z]/.test(value);
                    const hasLowerCase = /[a-z]/.test(value);
                    const hasNumbers = /\d/.test(value);
                    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value);
                    
                    let strength = 0;
                    if (hasUpperCase) strength++;
                    if (hasLowerCase) strength++;
                    if (hasNumbers) strength++;
                    if (hasSpecialChar) strength++;
                    
                    if (strength < 2) return 'Password should include uppercase, lowercase, numbers, and special characters';
                }
                return null;
            },
            required: (value, fieldName) => {
                if (!value || !value.toString().trim()) return `${fieldName} is required`;
                return null;
            },
            file: (file) => {
                if (file && file.size > 5 * 1024 * 1024) return 'File size must be less than 5MB';
                if (file && !file.type.startsWith('image/')) return 'Please select an image file';
                return null;
            }
        };

        function validateField(field) {
            const value = field.value;
            const fieldName = field.getAttribute('data-field') || field.name;
            const errorElement = document.getElementById(`error-${fieldName}`);
            
            let error = null;
            
            // Check required fields
            if (field.hasAttribute('required')) {
                error = validators.required(value, fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
            }
            
            // Check specific validators
            if (!error && validators[fieldName]) {
                error = validators[fieldName](value);
            }
            
            // Check patterns
            if (!error && field.hasAttribute('pattern')) {
                const pattern = new RegExp(field.getAttribute('pattern'));
                if (value && !pattern.test(value)) {
                    error = `Please enter a valid ${fieldName.replace(/_/g, ' ')}`;
                }
            }
            
            // Check min/max values for numeric fields
            if (!error && field.hasAttribute('min') && field.hasAttribute('max') && field.type === 'number') {
                const min = parseFloat(field.getAttribute('min'));
                const max = parseFloat(field.getAttribute('max'));
                const numValue = parseFloat(value);
                if (value && (isNaN(numValue) || numValue < min || numValue > max)) {
                    error = `${fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} must be between ${min} and ${max}`;
                }
            }
            
            // Check min/max length for text fields
            if (!error && field.hasAttribute('min') && field.hasAttribute('max') && field.type === 'text') {
                const min = parseInt(field.getAttribute('min'));
                const max = parseInt(field.getAttribute('max'));
                if (value && (value.length < min || value.length > max)) {
                    error = `${fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} must be between ${min} and ${max} characters`;
                }
            }
            
            // Check file validation
            if (field.type === 'file' && field.files.length > 0) {
                error = validators.file(field.files[0]);
            }
            
            // Display error or clear it
            if (error) {
                field.classList.add('is-invalid');
                if (errorElement) {
                    errorElement.textContent = error;
                    errorElement.style.display = 'block';
                }
                return error;
            } else {
                field.classList.remove('is-invalid');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
                return null;
            }
        }

        function validateForm() {
            const form = document.getElementById('playerEditForm');
            const fields = form.querySelectorAll('[data-field], select[required], input[required]');
            const errors = [];
            
            fields.forEach(field => {
                const error = validateField(field);
                if (error) {
                    errors.push(error);
                }
            });
            
            // Validate gender radio buttons
            const genderRadios = form.querySelectorAll('input[name="gender"]');
            const genderSelected = Array.from(genderRadios).some(radio => radio.checked);
            if (!genderSelected) {
                errors.push('Please select a gender');
                genderRadios.forEach(radio => {
                    radio.closest('.form-group').classList.add('is-invalid');
                });
            } else {
                genderRadios.forEach(radio => {
                    radio.closest('.form-group').classList.remove('is-invalid');
                });
            }
            
            // Validate required dropdowns
            const requiredSelects = form.querySelectorAll('select[required]');
            requiredSelects.forEach(select => {
                if (!select.value) {
                    errors.push(`${select.name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())} is required`);
                    select.classList.add('is-invalid');
                } else {
                    select.classList.remove('is-invalid');
                }
            });
            
            // Validate social links
            const socialLinks = form.querySelectorAll('input[name^="social_links"]');
            socialLinks.forEach(link => {
                if (link.value && link.value.trim()) {
                    const urlRegex = /^https?:\/\/.+/;
                    if (!urlRegex.test(link.value)) {
                        errors.push(`Please enter a valid ${link.name.replace('social_links[', '').replace(']', '')} URL`);
                        link.classList.add('is-invalid');
                    } else {
                        link.classList.remove('is-invalid');
                    }
                }
            });
            
            return errors;
        }

        function showValidationSummary(errors) {
            const summary = document.getElementById('validation-summary');
            const errorsList = document.getElementById('validation-errors-list');
            
            if (errors.length > 0) {
                errorsList.innerHTML = '';
                errors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorsList.appendChild(li);
                });
                summary.classList.remove('d-none');
                summary.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                summary.classList.add('d-none');
            }
        }

        function updatePasswordStrength(password) {
            const container = document.getElementById('password-strength-container');
            const bar = document.getElementById('password-strength-bar');
            const text = document.getElementById('password-strength-text');
            
            if (!password) {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const isLongEnough = password.length >= 8;
            
            let strength = 0;
            if (hasUpperCase) strength++;
            if (hasLowerCase) strength++;
            if (hasNumbers) strength++;
            if (hasSpecialChar) strength++;
            if (isLongEnough) strength++;
            
            const percentage = (strength / 5) * 100;
            bar.style.width = percentage + '%';
            
            let color = 'bg-danger';
            let strengthText = 'Very Weak';
            
            if (percentage >= 80) {
                color = 'bg-success';
                strengthText = 'Very Strong';
            } else if (percentage >= 60) {
                color = 'bg-info';
                strengthText = 'Strong';
            } else if (percentage >= 40) {
                color = 'bg-warning';
                strengthText = 'Medium';
            } else if (percentage >= 20) {
                color = 'bg-danger';
                strengthText = 'Weak';
            }
            
            bar.className = `progress-bar ${color}`;
            text.textContent = strengthText;
        }

        // Real-time validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('playerEditForm');
            const fields = form.querySelectorAll('[data-field], select, input[type="file"]');
            
            // Add tooltips for validation hints
            const tooltipFields = form.querySelectorAll('input[pattern], input[min], input[max]');
            tooltipFields.forEach(field => {
                let tooltipText = '';
                if (field.hasAttribute('pattern')) {
                    if (field.name === 'email') tooltipText = 'Enter a valid email address';
                    else if (field.name === 'phone') tooltipText = 'Enter a valid phone number';
                    else if (field.name === 'zip_code') tooltipText = 'Enter a valid ZIP code (e.g., 12345 or 12345-6789)';
                    else if (field.name.includes('social_links')) tooltipText = 'Enter a valid URL starting with http:// or https://';
                }
                if (field.hasAttribute('min') && field.hasAttribute('max')) {
                    const min = field.getAttribute('min');
                    const max = field.getAttribute('max');
                    tooltipText = `Value must be between ${min} and ${max}`;
                }
                
                if (tooltipText) {
                    field.title = tooltipText;
                }
            });
            
            // Add real-time validation
            fields.forEach(field => {
                field.addEventListener('blur', () => validateField(field));
                field.addEventListener('input', () => {
                    if (field.classList.contains('is-invalid')) {
                        validateField(field);
                    }
                });
                
                // Password strength indicator
                if (field.name === 'password') {
                    field.addEventListener('input', () => {
                        updatePasswordStrength(field.value);
                    });
                }
            });
            
            // Form submission
            form.addEventListener('submit', function(e) {
                const errors = validateForm();
                
                if (errors.length > 0) {
                    e.preventDefault();
                    showValidationSummary(errors);
                    return false;
                }
                
                // Show success message and loading state
                const successMessage = document.getElementById('success-message');
                const validationSummary = document.getElementById('validation-summary');
                
                validationSummary.classList.add('d-none');
                successMessage.classList.remove('d-none');
                
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                
                // Scroll to top to show success message
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Initialize stats loading
            const select = document.getElementById('sport_id');
            if (select.value) {
                loadStats(select.value);
            }
            select.addEventListener('change', function() {
                loadStats(this.value);
            });
        });
    </script>
@endsection

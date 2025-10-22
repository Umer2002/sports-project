@extends('layouts.admin')

@section('title', isset($player) ? 'Edit Player' : 'Create Player')
@include('partials.alerts')
@section('content')
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>{{ isset($player) ? 'Edit Player' : 'Create Player' }}</h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.players.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @php
                            $fields = [
                                'name' => 'Player Name',
                                'email' => 'Email',
                                'paypal_link' => 'PayPal Link',
                                'password' => 'Password',
                                'password_confirmation' => 'Confirm Password',
                                'phone' => 'Phone Number',
                                'city' => 'City',
                                'state' => 'State',
                                'zip_code' => 'Zip Code',
                                'nationality' => 'Nationality',
                                'height' => 'Height (cm)',
                                'weight' => 'Weight (kg)',
                                'debut' => 'Debut Date',
                                'jersey_no' => 'Jersey Number',
                            ];
                        @endphp

                        @php $i = 0; @endphp
                        <div class="row">
                            @foreach ($fields as $field => $label)
                                <div class="col-md-4">
                                    <h2 class="card-inside-title">{{ $label }}</h2>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input
                                                type="{{ in_array($field, ['email']) ? 'email' : (str_contains($field, 'password') ? 'password' : (str_contains($field, 'date') || $field == 'debut' ? 'date' : (in_array($field, ['height', 'weight', 'jersey_no']) ? 'number' : 'text'))) }}"
                                                step="{{ in_array($field, ['height', 'weight']) ? '0.1' : '' }}"
                                                name="{{ $field }}" value="{{ old($field, $player->$field ?? '') }}"
                                                class="form-control"
                                                {{ str_contains($field, 'password') ? '' : 'required' }}
                                                placeholder="Enter {{ strtolower($label) }}">
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


                        {{-- Dropdowns --}}
                        @foreach (['position' => $positions, 'club_id' => $clubs, 'sport_id' => $sports, 'team_id' => $teams] as $key => $list)
                            <h2 class="card-inside-title">{{ ucfirst(str_replace('_', ' ', $key)) }}</h2>
                            <div class="form-group">
                                <div class="form-line">
                                    <select name="{{ $key }}" class="form-control" id="{{ $key }}"
                                        required>
                                        <option value="">Select {{ ucfirst(str_replace('_', ' ', $key)) }}</option>
                                        @foreach ($list as $item)
                                            <option value="{{ $item->id }}"
                                                {{ old($key) == $item->id ? 'selected' : '' }}>
                                                {{ $item->name ?? $item->position_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        {{-- Gender --}}
                        <h2 class="card-inside-title">Gender</h2>
                        <div class="form-group">
                            @foreach (['male', 'female'] as $gender)
                                <div class="form-check form-check-radio">
                                    <label>
                                        <input name="gender" type="radio" value="{{ $gender }}"
                                            {{ old('gender') == $gender ? 'checked' : '' }} />
                                        <span>{{ ucfirst($gender) }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Address & Bio --}}
                        @foreach (['address' => 'Address', 'bio' => 'Bio'] as $key => $label)
                            <h2 class="card-inside-title">{{ $label }}</h2>
                            <div class="form-group">
                                <div class="form-line">
                                    <textarea name="{{ $key }}" class="form-control no-resize" rows="2">{{ old($key) }}</textarea>
                                </div>
                            </div>
                        @endforeach

                        {{-- Birthday & Age --}}
                        <h2 class="card-inside-title">Birthday</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="date" name="birthday" class="form-control" value="{{ old('birthday') }}">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Age</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="number" name="age" class="form-control" value="{{ old('age') }}">
                            </div>
                        </div>

                        {{-- Social Links --}}
                        <h2 class="card-inside-title">Social Links</h2>
                        @foreach (['facebook', 'twitter', 'instagram'] as $platform)
                            <div class="form-group">
                                <label>{{ ucfirst($platform) }}</label>
                                <div class="form-line">
                                    <input type="url" class="form-control" name="social_links[{{ $platform }}]"
                                        value="{{ old("social_links.$platform") }}">
                                </div>
                            </div>
                        @endforeach

                        {{-- Player Image --}}
                        <h2 class="card-inside-title">Upload Photo</h2>
                        <div class="form-group">
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>Choose File</span>
                                    <input type="file" name="photo">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload player photo">
                                </div>
                            </div>
                        </div>

                        {{-- Dynamic Stats --}}
                        {{-- Dynamic Stats --}}
                        <h2 class="card-inside-title">Player Stats</h2>
                        <div id="stats-container"></div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_1" class="form-control" id="stat_1" required>
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_1"
                                    value="{{ old('stat_value_1') }}" placeholder="Enter Stat">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_2" class="form-control" id="stat_2" required>
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_2"
                                    value="{{ old('stat_value_2') }}" placeholder="Enter Stat">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="stat_3" class="form-control" id="stat_3" required>
                                            <option value="">Select Stat Type</option>
                                            @foreach ($stats as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control" name="stat_value_3"
                                    value="{{ old('stat_value_3') }}" placeholder="Enter Stat">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Player Rewards</h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="rewards" class="form-control" id="rewards" required multiple>
                                            <option value="">Select Reward</option>
                                            @foreach ($rewards as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h2 class="card-inside-title">Associated Ads</h2>
                                <div class="form-group">
                                    <div class="form-line">
                                        <select name="ads[]" class="form-control" multiple>
                                            @foreach($ads as $ad)
                                                <option value="{{ $ad->id }}">{{ $ad->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple ads.</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary waves-effect">Create Player</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS to load stats dynamically --}}
    <script>
        function loadStats(sportId) {
            fetch(`stats-by-sport/${sportId}`)
                .then(response => response.json())
                .then(data => {
                    const statsContainer = document.getElementById('stats-container');
                    statsContainer.innerHTML = '';

                    if (Array.isArray(data.stats)) {
                        data.stats.forEach(stat => {
                            statsContainer.innerHTML += `
                        <div class="form-group">
                            <label>${stat.name}</label>
                            <div class="form-line">
                                <input type="number" step="0.1" name="stats[${stat.id}]" class="form-control" value="">
                            </div>
                        </div>
                    `;
                        });
                    }
                })
                .catch(error => console.error('Error loading stats:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('sport_id');
            select.addEventListener('change', function() {
                loadStats(this.value);
            });
        });
    </script>
@endsection

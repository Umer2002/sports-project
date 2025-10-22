@extends('layouts.admin')

@section('title')
Assign Reward
@stop

@section('header_styles')
<link href="{{ asset('vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('vendors/iCheck/css/all.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" type="text/css" />
@stop

@section('content')

<section class="content-header">
    <h1>Assign Reward</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="#">Rewards</a>
        </li>
        <li class="active">Assign Reward</li>
    </ol>
</section>

<section class="content ps-3 pe-3">
    <div class="row">
        <div class="col-md-12 col-lg-6 col-sm-12 col-12">
            <!-- BEGIN CREATE REWARD FORM-->
            <div class="my-3">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="livicon" data-name="gift" data-size="16" data-loop="true" data-c="#fff" data-hc="white" id="livicon-45" style="width: 16px; height: 16px;"></i>
                        Assign Reward
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.rewards.assign') }}" method="POST">
                            @csrf

                            <!-- Player Selection -->
                            <div class="form-group mb-3">
                                <label for="player_id">Player</label>
                                <select class="form-control" name="player_id" id="player_id" required>
                                    <option value="">Select a player</option>
                                    @foreach($players as $player)
                                    <option value="{{ $player->id }}" {{ old('player_id') == $player->id ? 'selected' : '' }}>
                                        {{ $player->first_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('player_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reward Selection -->
                            <div class="form-group mb-3">
                                <label for="reward_id">Reward</label>
                                <select class="form-control" name="reward_id" id="reward_id" required>
                                    <option value="">Select a reward</option>
                                    @foreach($rewards as $reward)
                                    <option value="{{ $reward->id }}" {{ old('reward_id') == $reward->id ? 'selected' : '' }}>
                                        {{ $reward->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('reward_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Issued By Selection -->
                            <div class="form-group mb-3">
                                <label for="issued_by">Issued By</label>
                                <select class="form-control" name="issued_by" id="issued_by" required>
                                    <option value="">Select Issued By</option>
                                    @foreach($adminsAndClubs as $user)
                                    <option value="{{ $user->id }}" {{ old('issued_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->first_name }} ({{ $user->role_name }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('issued_by')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reward Status -->
                            <div class="form-group mb-3">
                                <label for="status">Reward Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                </select>
                                @error('status')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group mb-3 text-end">
                                <button type="submit" class="btn btn-primary">Assign Reward</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- END CREATE REWARD FORM-->
        </div>
    </div>
</section>

@stop

@section('footer_scripts')
<script src="{{ asset('vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('vendors/iCheck/js/icheck.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr
        flatpickr("#dob", {
            dateFormat: "Y-m-d",
            enableTime: false,
            locale: "en",
        });
    });
</script>
@stop

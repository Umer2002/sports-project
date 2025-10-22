@extends('layouts.referee-dashboard')

@section('title', 'Expertise Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star"></i> Manage Your Expertise Levels
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Select your expertise levels to see qualified games</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('referee.expertise.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12">
                                <h5 class="mb-3">Select Your Expertise Levels:</h5>
                                <div class="row">
                                    @foreach($allExpertises as $expertise)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="expertise_ids[]"
                                                           value="{{ $expertise->id }}"
                                                           id="expertise_{{ $expertise->id }}"
                                                           {{ in_array($expertise->id, $refereeExpertises) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="expertise_{{ $expertise->id }}">
                                                        <strong>{{ $expertise->expertise_level }}</strong>
                                                    </label>
                                                </div>
                                                @if($expertise->description)
                                                    <p class="text-muted small mt-2">{{ $expertise->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Expertise Levels
                                </button>
                                <a href="{{ route('referee.expertise.available-games') }}" class="btn btn-success ml-2">
                                    <i class="fas fa-eye"></i> View Qualified Games
                                </a>
                                <a href="{{ route('referee.expertise.all-games') }}" class="btn btn-info ml-2">
                                    <i class="fas fa-list"></i> View All Games
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Expertise Summary -->
    @if(count($refereeExpertises) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-check-circle text-success"></i> Your Current Expertise Levels
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($allExpertises as $expertise)
                            @if(in_array($expertise->id, $refereeExpertises))
                            <div class="col-md-3 mb-2">
                                <span class="badge badge-success badge-lg">
                                    <i class="fas fa-star"></i> {{ $expertise->expertise_level }}
                                </span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

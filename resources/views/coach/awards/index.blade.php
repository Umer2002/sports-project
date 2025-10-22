@extends('layouts.coach-dashboard')

@section('title', 'Awards Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('coach-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Awards</li>
                    </ol>
                </div>
                <h4 class="page-title">Awards Management</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Available Awards</h5>
                        <div>
                            <a href="{{ route('coach.awards.assign') }}" class="btn btn-primary composer-primary">
                                <i class="fas fa-plus"></i> Assign Award
                            </a>
                            <a href="{{ route('coach.awards.log') }}" class="btn btn-outline-secondary composer-primary">
                                <i class="fas fa-history"></i> View Log
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($rewards->count() > 0)
                        <div class="row">
                            @foreach($rewards as $reward)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 award-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                @if($reward->image)
                                                    <div class="award-icon me-3" style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                                        <img src="{{ asset('images/' . $reward->image) }}" alt="{{ $reward->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                @else
                                                    <div class="award-icon me-3" style="width: 50px; height: 50px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                                        <i class="fas fa-trophy"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1">{{ $reward->name }}</h6>
                                                    <small class="text-muted">{{ ucfirst($reward->type) }} Badge</small>
                                                </div>
                                            </div>

                                            @if($reward->achievement)
                                                <p class="card-text small text-muted mb-3">{{ Str::limit($reward->achievement, 100) }}</p>
                                            @endif

                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Type:</small>
                                                    <small class="text-dark">{{ ucfirst($reward->type) }}</small>
                                                </div>
                                                @if($reward->achievement)
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">Achievement:</small>
                                                        <small class="text-dark">{{ Str::limit($reward->achievement, 50) }}</small>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-3">
                                                <a href="{{ route('coach.awards.assign') }}?award={{ $reward->id }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus"></i> Assign to Players
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-trophy text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h5 class="text-muted">No Awards Available</h5>
                            <p class="text-muted">There are currently no awards available for assignment.</p>
                            <a href="{{ route('coach.awards.assign') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Award Assignment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.award-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

.award-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.award-icon {
    background: linear-gradient(45deg, #007bff, #0056b3);
}
</style>
@endsection

@extends('layouts.club-dashboard')

@section('title', 'Club Profile')
@section('page_title', 'Club Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title">Club Profile</h4>
                        <p class="card-subtitle">View your club information</p>
                    </div>
                    <a href="{{ route('club.profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Club Logo and Basic Info -->
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                @if($club->logo)
                                    <img src="{{ Storage::url($club->logo) }}" alt="{{ $club->name }}" 
                                         class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                         style="width: 150px; height: 150px; background: #4299e1; color: white; font-size: 2rem; font-weight: bold;">
                                        {{ substr($club->name, 0, 2) }}
                                    </div>
                                @endif
                                <h3 class="mt-3">{{ $club->name }}</h3>
                                @if($club->sport)
                                    <span class="badge bg-primary">{{ $club->sport->name }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Club Details -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Contact Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $club->email }}</td>
                                        </tr>
                                        @if($club->phone)
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $club->phone }}</td>
                                        </tr>
                                        @endif
                                        @if($club->address)
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $club->address }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Registered:</strong></td>
                                            <td>{{ $club->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5>Club Statistics</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Players:</strong></td>
                                            <td>{{ $club->players()->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Coaches:</strong></td>
                                            <td>{{ $club->coaches()->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Teams:</strong></td>
                                            <td>{{ $club->teams()->count() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($club->is_registered)
                                                    <span class="badge bg-success">Registered</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($club->bio)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>About</h5>
                                    <p class="text-muted">{{ $club->bio }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Social Links -->
                            @if($club->social_links && count(array_filter($club->social_links)) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Social Media</h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if(isset($club->social_links['facebook']) && $club->social_links['facebook'])
                                            <a href="{{ $club->social_links['facebook'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fab fa-facebook-f"></i> Facebook
                                            </a>
                                        @endif
                                        @if(isset($club->social_links['instagram']) && $club->social_links['instagram'])
                                            <a href="{{ $club->social_links['instagram'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                <i class="fab fa-instagram"></i> Instagram
                                            </a>
                                        @endif
                                        @if(isset($club->social_links['twitter']) && $club->social_links['twitter'])
                                            <a href="{{ $club->social_links['twitter'] }}" target="_blank" class="btn btn-outline-info btn-sm">
                                                <i class="fab fa-twitter"></i> Twitter
                                            </a>
                                        @endif
                                        @if(isset($club->social_links['youtube']) && $club->social_links['youtube'])
                                            <a href="{{ $club->social_links['youtube'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                <i class="fab fa-youtube"></i> YouTube
                                            </a>
                                        @endif
                                        @if(isset($club->social_links['linkedin']) && $club->social_links['linkedin'])
                                            <a href="{{ $club->social_links['linkedin'] }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                <i class="fab fa-linkedin-in"></i> LinkedIn
                                            </a>
                                        @endif
                                        @if(isset($club->social_links['tiktok']) && $club->social_links['tiktok'])
                                            <a href="{{ $club->social_links['tiktok'] }}" target="_blank" class="btn btn-outline-dark btn-sm">
                                                <i class="fab fa-tiktok"></i> TikTok
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($club->paypal_link)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Support</h5>
                                    <a href="{{ $club->paypal_link }}" target="_blank" class="btn btn-outline-success">
                                        <i class="fab fa-paypal"></i> Support via PayPal
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

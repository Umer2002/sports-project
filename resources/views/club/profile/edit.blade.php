@extends('layouts.club-dashboard')

@section('title', 'Edit Club Profile')
@section('page_title', 'Edit Club Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Club Profile Settings</h4>
                    <p class="card-subtitle">Update your club information and settings</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('club.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Club Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $club->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $club->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $club->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="sport_id" class="form-label">Sport *</label>
                                    <select class="form-select @error('sport_id') is-invalid @enderror" 
                                            id="sport_id" name="sport_id" required>
                                        <option value="">Select Sport</option>
                                        @foreach($sports as $id => $name)
                                            <option value="{{ $id }}" {{ old('sport_id', $club->sport_id) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sport_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $club->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" name="bio" rows="4" placeholder="Tell us about your club...">{{ old('bio', $club->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Logo and Social Links -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Logo & Social Links</h5>
                                
                                <div class="mb-3">
                                    <label for="logo" class="form-label">Club Logo</label>
                                    <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($club->logo)
                                        <div class="mt-2">
                                            <img src="{{ Storage::url($club->logo) }}" alt="Current Logo" 
                                                 class="img-thumbnail" style="max-width: 150px;">
                                            <p class="text-muted small mt-1">Current logo</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="paypal_link" class="form-label">PayPal Link</label>
                                    <input type="url" class="form-control @error('paypal_link') is-invalid @enderror" 
                                           id="paypal_link" name="paypal_link" 
                                           value="{{ old('paypal_link', $club->paypal_link) }}" 
                                           placeholder="https://paypal.me/yourclub">
                                    @error('paypal_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h6 class="mt-4 mb-3">Social Media Links</h6>
                                
                                <div class="mb-3">
                                    <label for="social_facebook" class="form-label">Facebook</label>
                                    <input type="url" class="form-control @error('social_links.facebook') is-invalid @enderror" 
                                           id="social_facebook" name="social_links[facebook]" 
                                           value="{{ old('social_links.facebook', $club->social_links['facebook'] ?? '') }}" 
                                           placeholder="https://facebook.com/yourclub">
                                    @error('social_links.facebook')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="social_instagram" class="form-label">Instagram</label>
                                    <input type="url" class="form-control @error('social_links.instagram') is-invalid @enderror" 
                                           id="social_instagram" name="social_links[instagram]" 
                                           value="{{ old('social_links.instagram', $club->social_links['instagram'] ?? '') }}" 
                                           placeholder="https://instagram.com/yourclub">
                                    @error('social_links.instagram')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="social_twitter" class="form-label">Twitter</label>
                                    <input type="url" class="form-control @error('social_links.twitter') is-invalid @enderror" 
                                           id="social_twitter" name="social_links[twitter]" 
                                           value="{{ old('social_links.twitter', $club->social_links['twitter'] ?? '') }}" 
                                           placeholder="https://twitter.com/yourclub">
                                    @error('social_links.twitter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="social_youtube" class="form-label">YouTube</label>
                                    <input type="url" class="form-control @error('social_links.youtube') is-invalid @enderror" 
                                           id="social_youtube" name="social_links[youtube]" 
                                           value="{{ old('social_links.youtube', $club->social_links['youtube'] ?? '') }}" 
                                           placeholder="https://youtube.com/yourclub">
                                    @error('social_links.youtube')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="social_linkedin" class="form-label">LinkedIn</label>
                                    <input type="url" class="form-control @error('social_links.linkedin') is-invalid @enderror" 
                                           id="social_linkedin" name="social_links[linkedin]" 
                                           value="{{ old('social_links.linkedin', $club->social_links['linkedin'] ?? '') }}" 
                                           placeholder="https://linkedin.com/company/yourclub">
                                    @error('social_links.linkedin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="social_tiktok" class="form-label">TikTok</label>
                                    <input type="url" class="form-control @error('social_links.tiktok') is-invalid @enderror" 
                                           id="social_tiktok" name="social_links[tiktok]" 
                                           value="{{ old('social_links.tiktok', $club->social_links['tiktok'] ?? '') }}" 
                                           placeholder="https://tiktok.com/@yourclub">
                                    @error('social_links.tiktok')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('club-dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', isset($club) ? 'Edit Club' : 'Create Club')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>{{ isset($club) ? 'Edit Club' : 'Create Club' }}</h2>
                    <ul class="header-dropdown">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="#">Add</a></li>
                                <li><a href="#">Edit</a></li>
                                <li><a href="#">Delete</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <form method="POST"
                        action="{{ isset($club) ? route('admin.clubs.update', $club) : route('admin.clubs.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        @if (isset($club))
                            @method('PUT')
                        @endif

                        <h2 class="card-inside-title">Club Name</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $club->name ?? '') }}" required placeholder="Enter club name">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Email</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $club->email ?? '') }}" required placeholder="Email address">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Logo</h2>
                        <div class="form-group">
                            <div class="file-field input-field">
                                <div class="btn">
                                    <span>Choose Logo</span>
                                    <input type="file" name="logo">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload logo">
                                </div>
                            </div>
                            @if (isset($club) && $club->logo)
                                <img src="{{ asset('storage/' . $club->logo) }}" width="40" class="mt-2">
                            @endif
                        </div>

                        <h2 class="card-inside-title">Address</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <textarea name="address" class="form-control no-resize" rows="2" placeholder="Club address">{{ old('address', $club->address ?? '') }}</textarea>
                            </div>
                        </div>

                        <h2 class="card-inside-title">Sport</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <select name="sport_id" class="form-control show-tick" data-live-search="true" required>
                                    <option value="">-- Please select sport --</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}" {{ old('sport_id', $club->sport_id ?? '') == $sport->id ? 'selected' : '' }}>{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h2 class="card-inside-title">Phone</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $club->phone ?? '') }}" placeholder="Phone number">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Bio</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <textarea name="bio" class="form-control no-resize" rows="3" placeholder="Short club bio">{{ old('bio', $club->bio ?? '') }}</textarea>
                            </div>
                        </div>

                        <h2 class="card-inside-title">PayPal Link</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="url" name="paypal_link" class="form-control"
                                    value="{{ old('paypal_link', $club->paypal_link ?? '') }}"
                                    placeholder="https://paypal.me/your-link">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Joining URL</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="url" name="joining_url" class="form-control"
                                    value="{{ old('joining_url', $club->joining_url ?? '') }}"
                                    placeholder="https://yourclub.com/join">
                            </div>
                        </div>

                        <h2 class="card-inside-title">Social Media Links</h2>
                        @php
                            $socials = ['facebook', 'twitter', 'instagram', 'youtube', 'linkedin'];
                            $existingLinks = [];
                            //isset($club) ? json_decode($club->social_links, true) : [];
                            if (isset($club)) {
                                $existingLinks = is_string($club->social_links)
                                    ? json_decode($club->social_links, true)
                                    : (is_array($club->social_links)
                                        ? $club->social_links
                                        : []);
                            }
                        @endphp
                        @foreach ($socials as $platform)
                            <div class="form-group">
                                <label>{{ ucfirst($platform) }}</label>
                                <div class="form-line">
                                    <input type="url" name="social_links[{{ $platform }}]" class="form-control"
                                        value="{{ old("social_links.$platform", $existingLinks[$platform] ?? '') }}"
                                        placeholder="https://{{ $platform }}.com/yourpage">
                                </div>
                            </div>
                        @endforeach

                        <h2 class="card-inside-title">Registration Status</h2>
                        <div class="form-check m-l-10 mb-4">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_registered" class="form-check-input" value="1"
                                    {{ old('is_registered', $club->is_registered ?? false) ? 'checked' : '' }}>
                                <span class="form-check-sign"><span class="check"></span></span>
                                Is Registered
                            </label>
                        </div>

                        <button type="submit"
                            class="btn btn-success waves-effect">{{ isset($club) ? 'Update' : 'Create' }} Club</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

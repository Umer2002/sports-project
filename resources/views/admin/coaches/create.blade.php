@extends('layouts.admin')

@section('title', isset($coach) ? 'Edit Coach' : 'Create Coach')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>{{ isset($coach) ? 'Edit Coach' : 'Create Coach' }}</h2>
                <ul class="header-dropdown">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#">View</a></li>
                            <li><a href="#">Edit</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ isset($coach) ? route('admin.coaches.update', $coach) : route('admin.coaches.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($coach)) @method('PUT') @endif

                    <h2 class="card-inside-title">Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $coach->first_name ?? '') }}" placeholder="First Name" required>
                        </div>
                        <div class="form-line mt-2">
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $coach->last_name ?? '') }}" placeholder="Last Name" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Email & Phone</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="email" name="email" class="form-control" value="{{ old('email', $coach->email ?? '') }}" required>
                        </div>
                        <div class="form-line mt-2">
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $coach->phone ?? '') }}" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Gender & Age</h2>
                    <div class="form-group">
                        <select name="gender" class="form-control show-tick">
                            <option value="Male" {{ old('gender', $coach->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $coach->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        <div class="form-line mt-2">
                            <input type="number" name="age" class="form-control" value="{{ old('age', $coach->age ?? '') }}" min="18" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">City & Country</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="city" class="form-control" value="{{ old('city', $coach->city ?? '') }}" required>
                        </div>
                        <div class="form-line mt-2">
                            <input type="text" name="country_id" class="form-control" value="{{ old('country_id', $coach->country_id ?? '') }}" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Sport</h2>
                    <div class="form-group">
                        <select name="sport_id" class="form-control show-tick" required>
                            @foreach($sports as $id => $name)
                                <option value="{{ $id }}" {{ old('sport_id', $coach->sport_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h2 class="card-inside-title">Photo</h2>
                    <div class="form-group">
                        <input type="file" name="photo" class="form-control">
                        @if(isset($coach) && $coach->photo)
                            <img src="{{ asset('storage/' . $coach->photo) }}" class="mt-2" width="50">
                        @endif
                    </div>

                    <h2 class="card-inside-title">Bio</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <textarea name="bio" class="form-control no-resize" rows="3">{{ old('bio', $coach->bio ?? '') }}</textarea>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Social Links</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="social_links[]" class="form-control" placeholder="Social Link 1">
                        </div>
                        <div class="form-line mt-2">
                            <input type="text" name="social_links[]" class="form-control" placeholder="Social Link 2">
                        </div>
                        <!-- Add more as needed -->
                    </div>

                    <button type="submit" class="btn btn-success waves-effect">{{ isset($coach) ? 'Update' : 'Create' }} Coach</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

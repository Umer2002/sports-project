@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="row clearfix justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12">
        <div class="card">
            <div class="header bg-primary text-white">
                <h2>Add New User</h2>
            </div>
            <div class="body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf

                    <h2 class="card-inside-title">First Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" placeholder="Enter First Name" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Last Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Enter Last Name" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Email</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter Email Address" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Phone</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Enter Phone Number">
                        </div>
                    </div>

                    <h2 class="card-inside-title">Password</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Confirm Password</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Role</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role') == $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="activate" id="activate" class="form-check-input" {{ old('activate') ? 'checked' : '' }}>
                        <label class="form-check-label" for="activate">Activate User</label>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary waves-effect me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary waves-effect">Save User</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
@endsection

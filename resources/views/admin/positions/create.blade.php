@extends('layouts.admin')

@section('title', 'Create Position')

@section('content')
<div class="row clearfix">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="header">
                <h2>Create Position</h2>
                <ul class="header-dropdown">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#">View All</a></li>
                            <li><a href="#">Edit</a></li>
                            <li><a href="#">Delete</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="body">
                <form action="{{ route('admin.positions.store') }}" method="POST">
                    @csrf

                    <h2 class="card-inside-title">Position Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="position_name" class="form-control" required placeholder="Enter position name">
                        </div>
                    </div>

                    <h2 class="card-inside-title">Position Value</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="position_value" class="form-control" required placeholder="Enter position value">
                        </div>
                    </div>

                    <h2 class="card-inside-title">Sport</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="sports_id" class="form-control" required>
                                <option value="">Select Sport</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h2 class="card-inside-title">Is Active?</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <select name="is_active" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success waves-effect">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

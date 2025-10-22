@extends('layouts.admin')

@section('title', isset($stat) ? 'Edit Stat' : 'Create Stat')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h2>{{ isset($stat) ? 'Edit Stat' : 'Create Stat' }}</h2>
                <ul class="header-dropdown">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="#" onClick="return false;">Add</a></li>
                            <li><a href="#" onClick="return false;">Edit</a></li>
                            <li><a href="#" onClick="return false;">Delete</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="body">
                <form action="{{ route('admin.stats.store') }}" method="POST">
                    @csrf

                    <h2 class="card-inside-title">Stat Name</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter stat name" value="{{ old('name', $stat->name ?? '') }}">
                        </div>
                    </div>

                    <h2 class="card-inside-title">Sport</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <select class="form-control show-tick" id="sports_id" name="sports_id" required>
                                <option value="">Select Sport</option>
                                @foreach ($sports as $sport)
                                    <option value="{{ $sport->id }}" {{ (old('sports_id', $stat->sports_id ?? '') == $sport->id) ? 'selected' : '' }}>{{ $sport->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success m-t-15 waves-effect">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Edit Gender')

@section('content')
<section class="content-header">
    <h1>Edit Gender</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.genders.index') }}">Genders</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-warning text-dark">
                <h4 class="card-title my-2">Update Gender</h4>
            </div>
            <div class="card-body">
                @include('admin.genders._form', ['gender' => $gender])
            </div>
        </div>
    </div>
</div>
@endsection

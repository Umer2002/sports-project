@extends('layouts.admin')

@section('title', 'Create Gender')

@section('content')
<section class="content-header">
    <h1>Create Gender</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.genders.index') }}">Genders</a></li>
        <li class="active">Create</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title my-2">New Gender</h4>
            </div>
            <div class="card-body">
                @include('admin.genders._form', ['gender' => null])
            </div>
        </div>
    </div>
</div>
@endsection

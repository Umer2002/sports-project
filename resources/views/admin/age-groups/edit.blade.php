@extends('layouts.admin')

@section('title', 'Edit Age Group')

@section('content')
<section class="content-header">
    <h1>Edit Age Group</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.age_groups.index') }}">Age Groups</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-warning text-dark">
                <h4 class="card-title my-2">Update Age Group</h4>
            </div>
            <div class="card-body">
                @include('admin.age-groups._form', ['ageGroup' => $ageGroup])
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Create Classification Group')

@section('content')
<section class="content-header">
    <h1>Create Classification Group</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.sport_classification_groups.index') }}">Classification Groups</a></li>
        <li class="active">Create</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title my-2">New Classification Group</h4>
            </div>
            <div class="card-body">
                @include('admin.sport-classification-groups._form', ['group' => null])
            </div>
        </div>
    </div>
</div>
@endsection

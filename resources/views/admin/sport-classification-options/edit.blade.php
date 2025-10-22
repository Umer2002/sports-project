@extends('layouts.admin')

@section('title', 'Edit Classification Option')

@section('content')
<section class="content-header">
    <h1>Edit Classification Option</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.sport_classification_options.index') }}">Classification Options</a></li>
        <li class="active">Edit</li>
    </ol>
</section>

<div class="row">
    <div class="col-12">
        <div class="card bg-dark text-white">
            <div class="card-header bg-warning text-dark">
                <h4 class="card-title my-2">Update Classification Option</h4>
            </div>
            <div class="card-body">
                @include('admin.sport-classification-options._form', ['option' => $option, 'groups' => $groups])
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Add Tournament Format')
@include('partials.alerts')
@section('content')
<section class="content-header">
    <h1>Add Tournament Format</h1>
</section>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title my-2">New Format</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tournamentformats.store') }}" method="POST">
                        @csrf

                        @include('admin.tournament_formats.partials.form')

                        <div class="text-end">
                            <a href="{{ route('admin.tournamentformats.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

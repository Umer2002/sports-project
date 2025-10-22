@extends('layouts.admin')

@section('title', 'Edit Tournament Format')

@section('content')
<section class="content-header">
    <h1>Edit Tournament Format</h1>
</section>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title my-2">Update Format</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('admin.tournamentformats.update', $tournamentformat) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('admin.tournament_formats.partials.form', ['tournamentFormat' => $tournamentformat])

                        <div class="text-end">
                            <a href="{{ route('admin.tournamentformats.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

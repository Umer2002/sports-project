@extends('layouts.admin')

@section('title', 'Edit Schedule')

@section('content')
<section class="content-header">
    <h1>Edit Schedule</h1>
</section>

<section class="content ps-3 pe-3">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card bg-dark text-white">
                <div class="card-header bg-primary">
                    <h4 class="card-title">Update Schedule</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}">
                        @csrf
                        @method('PUT')

                        @include('admin.schedules.partials.form', ['schedule' => $schedule])

                        <div class="text-end">
                            <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

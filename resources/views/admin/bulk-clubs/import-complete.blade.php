@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Import Completed</h1>
    <p>The club login credentials have been generated.</p>
    <a href="{{ route('bulk-clubs.export') }}" class="btn btn-success">Download CSV</a>
</div>
@endsection

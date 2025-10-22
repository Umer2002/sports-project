@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Import Clubs</h1>
    <form action="{{ route('bulk-clubs.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="csv_file" class="form-label">CSV File</label>
            <input type="file" name="csv_file" id="csv_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Create Event')

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Create Event</h2>
            </div>
            <div class="body">
                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label>Event Title</label>
                        <input type="text" name="eventname" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control summernote" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Event Date</label>
                        <input type="date" name="eventdate" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Event Time</label>
                        <input type="time" name="eventtime" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="eventlocation" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Privacy</label>
                        <select name="privacy" class="form-control">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                            <option value="friends">Friends</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cover Photo</label>
                        <input type="file" name="coverphoto" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Create Event</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(function () {
        $('.summernote').summernote({ height: 200 });
    });
</script>
@endsection

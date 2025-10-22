@extends('layouts.admin')
@section('title', 'Add New Ad')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Add New Ad</h4>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Type</label>
                <select name="type" id="ad-type" class="form-control" required>
                    <option value="image">Image</option>
                    <option value="video">Video</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label id="media-label">Media Upload</label>
                <input type="file" name="media" id="media-input" class="form-control" accept="image/*" required>
                <small class="form-text text-muted" id="media-help">Accepted images: JPG, JPEG, PNG, GIF, WEBP (max 2MB).</small>
            </div>
            <div class="form-group mb-3">
                <label>Link (optional)</label>
                <input type="text" name="link" class="form-control">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" class="" id="active" checked>
                <label class="form-check-label" for="active">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Create Ad</button>
            <a href="{{ route('admin.ads.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@push('scripts')
<style>
    [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
    opacity: 1;
}
</style>
<script>
    $(function() {
        const $type = $('#ad-type');
        const $input = $('#media-input');
        const $label = $('#media-label');
        const $help = $('#media-help');

        function toggleMediaField() {
            if ($type.val() === 'image') {
                $label.text('Image Upload');
                $input.attr('accept', 'image/*');
                $help.text('Accepted images: JPG, JPEG, PNG, GIF, WEBP (max 2MB).');
            } else {
                $label.text('Video Upload');
                $input.attr('accept', 'video/*');
                $help.text('Accepted videos: MP4, MOV, WMV, AVI, MKV, WEBM, M4V (max 50MB).');
            }
        }

        $type.on('change', toggleMediaField);
        toggleMediaField();
    });
</script>
@endpush
@endsection 

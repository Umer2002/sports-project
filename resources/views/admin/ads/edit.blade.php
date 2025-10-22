@extends('layouts.admin')
@section('title', 'Edit Ad')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Edit Ad</h4>
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
        <form action="{{ route('admin.ads.update', $ad) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $ad->title) }}" required>
            </div>
            <div class="form-group mb-3">
                <label>Type</label>
                <select name="type" id="ad-type" class="form-control" required>
                    <option value="image" {{ $ad->type == 'image' ? 'selected' : '' }}>Image</option>
                    <option value="video" {{ $ad->type == 'video' ? 'selected' : '' }}>Video</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label id="media-label">Media Upload</label>
                <input type="file" name="media" id="media-input" class="form-control" accept="{{ $ad->type === 'video' ? 'video/*' : 'image/*' }}">
                <small class="form-text text-muted" id="media-help">
                    {{ $ad->type === 'video' ? 'Accepted videos: MP4, MOV, WMV, AVI, MKV, WEBM, M4V (max 50MB).' : 'Accepted images: JPG, JPEG, PNG, GIF, WEBP (max 2MB).' }}
                </small>
                @php
                    $mediaExists = $ad->media && file_exists(public_path('storage/' . $ad->media));
                @endphp
                @if($mediaExists)
                    <div class="mt-3" id="media-preview">
                        @if($ad->type === 'image')
                            <img src="{{ asset('storage/' . $ad->media) }}" alt="Current Image" width="180" class="rounded border">
                        @else
                            @php
                                $ext = strtolower(pathinfo($ad->media, PATHINFO_EXTENSION));
                                $mimeSuffix = $ext === 'mov' ? 'quicktime' : $ext;
                            @endphp
                            <video width="240" controls class="rounded border">
                                <source src="{{ asset('storage/' . $ad->media) }}" type="video/{{ $mimeSuffix }}">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    </div>
                @else
                    <small class="text-muted d-block mt-2">No media available. Upload a new file to replace.</small>
                @endif
            </div>
            <div class="form-group mb-3">
                <label>Link (optional)</label>
                <input type="text" name="link" class="form-control" value="{{ old('link', $ad->link) }}">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="active" class="" id="active" {{ $ad->active ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>
            <button type="submit" class="btn btn-primary">Update Ad</button>
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

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Create Promotion</h2>
        <a href="{{ route('volunteer.promotions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('volunteer.promotions.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Video (optional)</label>
                    <input type="file" name="video" class="form-control" accept="video/*">
                    <small class="text-muted">Max 50MB. Or provide a YouTube URL below.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">YouTube URL (optional)</label>
                    <input type="url" name="youtube_url" class="form-control" placeholder="https://youtube.com/watch?v=...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Share Text</label>
                    <input type="text" name="share_text" class="form-control" maxlength="280" placeholder="Copy for social posts">
                </div>
                <button class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>
</div>
@endsection

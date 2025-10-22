@extends('layouts.coach-dashboard')

@section('title', $blog->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Blog Post Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-blog me-2"></i> {{ $blog->title }}
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('coach.blog.edit', $blog) }}" class="btn btn-light">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <a href="{{ route('coach.blog.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <!-- Featured Image -->
                            @if($blog->image)
                                <div class="mb-4">
                                    <img src="{{ asset('uploads/blog/' . $blog->image) }}" 
                                         alt="{{ $blog->title }}" 
                                         class="img-fluid rounded">
                                </div>
                            @endif

                            <!-- Blog Content -->
                            <div class="blog-content">
                                {!! $blog->content !!}
                            </div>

                            <!-- Tags -->
                            @if($blog->tags)
                                <div class="mt-4">
                                    <h6>Tags:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach(explode(',', $blog->tags) as $tag)
                                            <span class="badge bg-secondary">{{ trim($tag) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comments Section -->
                    @if($comments->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-comments me-2"></i> Comments ({{ $comments->count() }})
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($comments as $comment)
                                    <div class="comment-item border-bottom pb-3 mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                @if($comment->user && $comment->user->profile_photo_path)
                                                    <img src="{{ Storage::url($comment->user->profile_photo_path) }}" 
                                                         alt="{{ $comment->user->name }}" 
                                                         class="rounded-circle" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1">{{ $comment->user->name ?? 'Anonymous' }}</h6>
                                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                                <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Blog Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Post Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-primary mb-0">{{ $blog->views ?? 0 }}</h4>
                                        <small class="text-muted">Views</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="text-success mb-0">{{ $blog->comments->count() }}</h4>
                                        <small class="text-muted">Comments</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Category:</strong>
                                <span class="badge bg-info">{{ $blog->category->title ?? 'Uncategorized' }}</span>
                            </div>

                            <div class="mb-3">
                                <strong>Status:</strong>
                                @if($blog->is_public)
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-warning">Private</span>
                                @endif
                            </div>

                            <div class="mb-3">
                                <strong>Created:</strong>
                                <br>
                                <small class="text-muted">{{ $blog->created_at->format('M d, Y \a\t g:i A') }}</small>
                            </div>

                            @if($blog->updated_at != $blog->created_at)
                                <div class="mb-3">
                                    <strong>Last Updated:</strong>
                                    <br>
                                    <small class="text-muted">{{ $blog->updated_at->format('M d, Y \a\t g:i A') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('coach.blog.edit', $blog) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Edit Post
                                </a>
                                <a href="{{ route('coach.blog.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-list me-1"></i> All Posts
                                </a>
                                <a href="{{ route('coach.blog.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> New Post
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.blog-content {
    line-height: 1.6;
}

.blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4, .blog-content h5, .blog-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.blog-content p {
    margin-bottom: 1rem;
}

.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.blog-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
}

.blog-content ul, .blog-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.blog-content li {
    margin-bottom: 0.5rem;
}
</style>
@endsection

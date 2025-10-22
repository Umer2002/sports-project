@extends('layouts.coach-dashboard')

@section('title', 'Edit Blog Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Edit Blog Post
                    </h4>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('coach.blog.update', $blog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-8">
                                <!-- Blog Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" value="{{ old('title', $blog->title) }}" 
                                           class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>

                                <!-- Blog Content -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea name="content" id="content" 
                                              class="form-control summernote @error('content') is-invalid @enderror" 
                                              required>{{ old('content', $blog->content) }}</textarea>
                                    @error('content') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-4">
                                <!-- Blog Category -->
                                <div class="mb-3">
                                    <label for="blog_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select name="blog_category_id" id="blog_category_id" 
                                            class="form-select @error('blog_category_id') is-invalid @enderror" required>
                                        <option value="">Select Category</option>
                                        @foreach($blogcategory as $key => $category)
                                            <option value="{{ $key }}" {{ old('blog_category_id', $blog->blog_category_id) == $key ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('blog_category_id') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>

                                <!-- Tags -->
                                <div class="mb-3">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" name="tags" id="tags" value="{{ old('tags', $blog->tags) }}" 
                                           class="form-control" 
                                           placeholder="Enter tags separated by commas">
                                    <small class="form-text text-muted">Separate tags with commas (e.g., coaching, training, tips)</small>
                                </div>

                                <!-- Target Sports -->
                                <div class="mb-3">
                                    <label class="form-label">Target Audience - Sport</label>
                                    <select name="target_audience_sport[]" class="form-select sport-select" multiple>
                                        <option value="all">All Sports</option>
                                        @foreach($sports as $key => $sport)
                                            <option value="{{ $key }}" {{ collect(old('target_audience_sport'))->contains($key) ? 'selected' : '' }}>
                                                {{ $sport }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Current Featured Image -->
                                @if($blog->image)
                                    <div class="mb-3">
                                        <label class="form-label">Current Featured Image</label>
                                        <div class="text-center">
                                            <img src="{{ asset('uploads/blog/' . $blog->image) }}" 
                                                 alt="{{ $blog->title }}" 
                                                 class="img-fluid rounded" 
                                                 style="max-height: 200px;">
                                            <p class="text-muted mt-2">Current image</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Featured Image -->
                                <div class="mb-3">
                                    <label for="image" class="form-label">Change Featured Image</label>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail">
                                            <img src="{{ asset('images/authors/no_avatar.jpg') }}" alt="placeholder" />
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                        <div>
                                            <span class="btn btn-outline-primary btn-file">
                                                <input type="file" name="image" id="image" accept="image/*" />
                                            </span>
                                            <a href="#" class="btn btn-outline-danger fileinput-exists" data-bs-dismiss="fileinput">Remove</a>
                                        </div>
                                    </div>
                                    @error('image') 
                                        <div class="text-danger">{{ $message }}</div> 
                                    @enderror
                                </div>

                                <!-- Visibility -->
                                <div class="mb-3">
                                    <label for="is_public" class="form-label">Visibility</label>
                                    <select class="form-select" id="is_public" name="is_public">
                                        <option value="0" {{ old('is_public', $blog->is_public) == '0' ? 'selected' : '' }}>Private</option>
                                        <option value="1" {{ old('is_public', $blog->is_public) == '1' ? 'selected' : '' }}>Public</option>
                                    </select>
                                    <small class="form-text text-muted">Public posts are visible to all users</small>
                                </div>

                                <!-- Blog Stats -->
                                <div class="mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Post Statistics</h6>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <h5 class="mb-0 text-primary">{{ $blog->views ?? 0 }}</h5>
                                                        <small class="text-muted">Views</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <h5 class="mb-0 text-success">{{ $blog->comments->count() }}</h5>
                                                    <small class="text-muted">Comments</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('coach.blog.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Update Post
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(function () {
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['codeview', 'fullscreen']]
            ]
        });

        $('.sport-select').select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select sports'
        });

        $('.sport-select').on('change', function () {
            if ($(this).val()?.includes('all')) {
                const options = $(this).find('option:not([value="all"])').map(function () {
                    return this.value;
                }).get();
                $(this).val(options).trigger('change');
            }
        });
    });
</script>
@endsection

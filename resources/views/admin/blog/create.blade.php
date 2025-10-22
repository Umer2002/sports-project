@extends('layouts.admin')

@section('title', __('blog/title.add-blog'))


@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fa fa-edit me-2"></i> {{ __('blog/title.add-blog') }}</h4>
    </div>
    @include('partials.alerts')
    <div class="card-body">
        <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Blog Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" required>
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Blog Content -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control summernote">{{ old('content') }}</textarea>
                        @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Blog Category -->
                    <div class="mb-3">
                        <label for="blog_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="blog_category_id" id="blog_category_id" class="form-select select2" required>
                            <option value="">Select Category</option>
                            @foreach($blogcategory as $key => $category)
                                <option value="{{ $key }}" {{ old('blog_category_id') == $key ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                        @error('blog_category_id') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags</label>
                        <input type="text" name="tags" id="tags" value="{{ old('tags') }}" class="form-control" data-role="tagsinput">
                    </div>

                    <!-- Target Sports -->
                    <div class="mb-3">
                        <label class="form-label">Target Audience - Sport</label>
                        <select name="target_audience_sport[]" class="form-select select2 sport-select" multiple>
                            <option value="all">All Sports</option>
                            @foreach($sports as $key => $sport)
                                <option value="{{ $key }}" {{ collect(old('target_audience_sport'))->contains($key) ? 'selected' : '' }}>{{ $sport }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Clubs -->
                    <div class="mb-3">
                        <label class="form-label">Target Audience - Club</label>
                        <select name="target_audience_club[]" class="form-select select2 club-select" multiple>
                            <option value="all">All Clubs</option>
                            @foreach($clubs as $key => $club)
                                <option value="{{ $key }}" {{ collect(old('target_audience_club'))->contains($key) ? 'selected' : '' }}>{{ $club }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Featured Image</label>
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
                        @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="is_public" class="form-label">Visibility</label>
                        <select class="form-select" id="is_public" name="is_public">
                          <option value="0">Private</option>
                          <option value="1">Public</option>
                        </select>
                      </div>
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.blog.index') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-success">Publish</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(function () {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['codeview']]
            ]
        });

        $('.select2').select2({
            width: '100%',
            allowClear: true,
            placeholder: 'Select options'
        });

        $('.sport-select').on('change', function () {
            if ($(this).val()?.includes('all')) {
                const options = $(this).find('option:not([value="all"])').map(function () {
                    return this.value;
                }).get();
                $(this).val(options).trigger('change');
            }
        });

        $('.club-select').on('change', function () {
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

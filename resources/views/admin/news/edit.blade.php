@extends('layouts.admin')
@section('title', 'Edit News')

@section('header_styles')


@endsection

@section('content')
<div class="page-breadcrumb d-flex align-items-center justify-content-between">
    <h1 class="fs-5 mb-0">Edit News</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-home"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit News</li>
        </ol>
    </nav>
</div>

<div class="card rounded-2xl shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3 px-4">
        <h4 class="mb-0 fs-6">Update News</h4>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger rounded-2xl">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ url('admin/news/' . $news->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-semibold">News Title</label>
                        <input type="text" class="form-control form-control-lg" name="title" id="title"
                            value="{{ old('title', $news->title) }}" placeholder="Enter title">
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-semibold">Content</label>
                        <textarea name="content" class="form-control summernote" rows="8">{{ old('content', $news->content) }}</textarea>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mb-4">
                        <label for="category" class="form-label fw-semibold">News Category</label>
                        <select name="category" id="category" class="form-select select2">
                            <option value="">Select category</option>
                            <option value="popular" {{ old('category', $news->category) == 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="hotnews" {{ old('category', $news->category) == 'hotnews' ? 'selected' : '' }}>Hot News</option>
                            <option value="world" {{ old('category', $news->category) == 'world' ? 'selected' : '' }}>World News</option>
                            <option value="lifestyle" {{ old('category', $news->category) == 'lifestyle' ? 'selected' : '' }}>Life Style</option>
                            <option value="business" {{ old('category', $news->category) == 'business' ? 'selected' : '' }}>Business</option>
                            <option value="sports" {{ old('category', $news->category) == 'sports' ? 'selected' : '' }}>Sports</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Featured Image</label>
                        @if ($news->image)
                            <div class="mb-3">
                                <img src="{{ asset('uploads/news/' . $news->image) }}" class="img-fluid rounded-2xl" alt="Current image">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control">
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-success btn-label">
                            <i class="ti ti-edit label-icon"></i> Update
                        </button>
                        <a href="{{ url('admin/news') }}" class="btn btn-secondary btn-label">
                            <i class="ti ti-arrow-left label-icon"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer_scripts')
<!-- Summernote CSS -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(document).ready(function () {
        $('.summernote').summernote({ height: 200 });
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });
    });
</script>
@endsection

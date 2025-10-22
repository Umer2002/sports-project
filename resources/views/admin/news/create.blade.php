@extends('layouts.admin')
@section('title', 'Add News')

@section('header_styles')

@endsection

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Add News</h2>
            </div>

            <div class="body">
                @if($errors->any())
                    <div class="alert alert-danger rounded-2xl">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ url('admin/news') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <h2 class="card-inside-title">News Title</h2>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{{ old('title') }}" placeholder="Enter title" required>
                        </div>
                    </div>

                    {{-- Content --}}
                    <h2 class="card-inside-title">Content</h2>
                    <div class="form-group">
                        <textarea name="content" class="form-control summernote" rows="6">{{ old('content') }}</textarea>
                    </div>

                    {{-- Category --}}
                    <h2 class="card-inside-title">Category</h2>
                    <div class="form-group">
                        <select name="category" id="category" class="form-control select2" required>
                            <option value="">Select category</option>
                            @foreach(['popular', 'hotnews', 'world', 'lifestyle', 'business', 'sports'] as $cat)
                                <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Featured Image --}}
                    <h2 class="card-inside-title">Featured Image</h2>
                    <div class="form-group">
                        <input type="file" name="image" class="form-control">
                    </div>

                    {{-- Submit Actions --}}
                    <div class="form-group d-flex justify-content-between mt-4">
                        <a href="{{ url('admin/news/create') }}" class="btn btn-secondary btn-label">
                            <i class="ti ti-trash label-icon"></i> Discard
                        </a>
                        <button type="submit" class="btn btn-success btn-label">
                            <i class="ti ti-send label-icon"></i> Publish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<!-- Summernote CSS -->
{{-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> --}}
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
<script>
    $(function () {
        $('.summernote').summernote({ height: 200 });

        $('.select2').select2({
            width: '100%',
            placeholder: 'Select a category',
            allowClear: true
        });
    });
</script>
@endsection

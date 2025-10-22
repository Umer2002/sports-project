@extends('layouts.admin')

@section('title', __('blog/title.edit'))

@section('header_styles')
<link href="{{ asset('vendors/summernote/css/summernote-bs4.css') }}" rel="stylesheet" />
<link href="{{ asset('vendors/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('vendors/bootstrap-tagsinput/css/bootstrap-tagsinput.css') }}" rel="stylesheet" />
<link href="{{ asset('vendors/jasny-bootstrap/css/jasny-bootstrap.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header">
                <h2>{{ __('blog/title.edit') }}</h2>
            </div>
            <div class="body">
                <form action="{{ route('admin.blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data"
                    class="form-horizontal">
                    @csrf
                    @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        {{-- Title --}}
                        <h2 class="card-inside-title">@lang('blog/form.ph-title')</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="title" class="form-control input-lg"
                                    placeholder="@lang('blog/form.ph-title')"
                                    value="{{ old('title', $blog->title) }}">
                            </div>
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        </div>

                        {{-- Content --}}
                        <h2 class="card-inside-title">@lang('blog/form.ph-content')</h2>
                        <div class="form-group">
                            <textarea name="content" class="form-control summernote" placeholder="@lang('blog/form.ph-content')">{{ old('content', $blog->content) }}</textarea>
                            <span class="text-danger">{{ $errors->first('content') }}</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        {{-- Blog Category --}}
                        <h2 class="card-inside-title">@lang('blog/form.ll-postcategory')</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <select name="blog_category_id" class="form-control select2"
                                    data-placeholder="@lang('blog/form.select-category')">
                                    <option value="" disabled selected hidden>@lang('blog/form.select-category')</option>
                                    @foreach ($blogcategory as $categoryId => $categoryName)
                                        <option value="{{ $categoryId }}"
                                            {{ (string) old('blog_category_id', $blog->blog_category_id) === (string) $categoryId ? 'selected' : '' }}>
                                            {{ $categoryName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="text-danger">{{ $errors->first('blog_category_id') }}</span>
                        </div>

                        {{-- Tags --}}
                        <h2 class="card-inside-title">@lang('blog/form.tags')</h2>
                        <div class="form-group">
                            <div class="form-line">
                                <input type="text" name="tags" class="form-control" data-role="tagsinput"
                                    value="{{ old('tags', $blog->tagList) }}">
                            </div>
                        </div>

                        {{-- Featured Image --}}
                        <h2 class="card-inside-title">@lang('blog/form.lb-featured-img')</h2>
                        <div class="form-group">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-new thumbnail" style="max-width: 200px;">
                                    @php
                                        $mediaFile = $blog->image;
                                        $mediaUrl = $mediaFile ? asset('uploads/blog/' . $mediaFile) : null;
                                        $mediaExtension = $mediaFile ? strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION)) : null;
                                        $videoExtensions = ['mp4','mov','qt','avi','wmv','mkv','webm','m4v'];
                                        $isVideo = $mediaExtension && in_array($mediaExtension, $videoExtensions, true);
                                    @endphp
                                    @if ($mediaUrl)
                                        @if ($isVideo)
                                            <video controls preload="metadata" style="width:100%;max-height:180px;display:block;">
                                                <source src="{{ $mediaUrl }}">
                                                Your browser does not support the video tag.
                                            </video>
                                        @else
                                            <img src="{{ $mediaUrl }}" alt="Current Media" class="img-fluid" />
                                        @endif
                                    @else
                                        <img src="{{ asset('images/authors/no_avatar.jpg') }}" alt="No Media" class="img-fluid" />
                                    @endif
                                </div>
                                <div class="fileinput-preview fileinput-exists thumbnail"></div>
                                <div>
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new">Select media</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="image" id="pic" accept="image/*,video/*" />
                                    </span>
                                    <a href="#" class="btn btn-danger fileinput-exists" data-bs-dismiss="fileinput">Remove</a>
                                </div>
                                <span class="text-danger">{{ $errors->first('image') }}</span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-success">@lang('blog/form.update')</button>
                            <a href="{{ route('admin.blog.index') }}" class="btn btn-danger">@lang('blog/form.cancel')</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/summernote/js/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('vendors/select2/js/select2.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}"></script>
<script src="{{ asset('vendors/jasny-bootstrap/js/jasny-bootstrap.js') }}"></script>
<script src="{{ asset('js/pages/add_newblog.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.summernote').summernote({ height: 200 });
        $('.select2').select2({ width: '100%' });
    });
</script>
@endsection

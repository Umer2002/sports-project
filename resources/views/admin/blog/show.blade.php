@extends('layouts.admin')

{{-- Page title --}}
@section('title')
    @lang('blog/title.blogdetail')
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/pages/blog.css') }}" />
@stop


{{-- Page content --}}
@section('content')
    <section class="content-header">
        <!--section starts-->
        <h1>{!! $blog->title !!}</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}"> <i class="livicon" data-name="home" data-size="14" data-c="#000"
                        data-loop="true"></i>
                    @lang('general.dashboard')
                </a>
            </li>
            <li> @lang('blog/title.blog')</li>
            <li class="active">@lang('blog/title.blogdetail')</li>
        </ol>
    </section>
    <!--section ends-->
    <section class="content ps-3 pe-3">
        <!--main content-->
        <div class="row">
            <div class="col-sm-11 col-md-12 col-full-width-right">
                @php
                    $mediaFile = $blog->image;
                    $mediaUrl = $mediaFile ? url('uploads/blog/' . $mediaFile) : null;
                    $mediaExtension = $mediaFile ? strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION)) : null;
                    $videoExtensions = ['mp4','mov','qt','avi','wmv','mkv','webm','m4v'];
                    $isVideo = $mediaExtension && in_array($mediaExtension, $videoExtensions, true);
                @endphp
                <div class="blog-detail-image mrg_btm15">
                    @if ($mediaUrl)
                        @if ($isVideo)
                            <video controls preload="metadata" style="width:100%;max-height:480px;display:block;">
                                <source src="{{ $mediaUrl }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $mediaUrl }}" class="img-responsive" alt="Image">
                        @endif
                    @else
                        <img data-src="holder.js/791x380/#6cc66c:#fff" class="img-responsive" alt="Image">
                    @endif
                </div>
                <!-- /.blog-detail-image -->
                <div class="the-box no-border blog-detail-content">
                    <p>
                        <span class="label label-danger square">{!! $blog->created_at !!}</span>
                    </p>
                    <p class="text-justify">
                        {!! $blog->content !!}
                    </p>

                    <p><strong>Tags:</strong> {!! $blog->tagList !!}</p>
                    <hr>
                    <p>
                        <span class="label label-success square">@lang('blog/title.comments')</span>
                    </p>
                    @if (!empty($comments))
                        <ul class="media-list media-sm media-dotted recent-post">
                            @foreach ($comments as $comment)
                                <li class="media">
                                    <div class="media-body">
                                        <h4 class="media-heading">
                                            <a href="{!! $comment->website !!}">{!! $comment->name !!}</a>
                                        </h4>
                                        <p>
                                            {!! $comment->comment !!}
                                        </p>
                                        <p class="text-danger">
                                            <small> {!! $comment->created_at !!}</small>
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <hr>
                    <p>
                        <span class="label label-info square">@lang('blog/title.leavecomment')</span>
                    </p>
                    <form action="{{ url('admin/blog/' . $blog->id . '/storecomment') }}" method="POST" class="bf">
                        @csrf

                        <div class=" {{ $errors->has('name') ? 'has-error' : '' }}">
                            <input type="text" name="name" class="form-control input-lg" required
                                placeholder="@lang('blog/form.ph-name')" value="{{ old('name') }}">
                            <span class="form-text">{{ $errors->first('name', ':message') }}</span>
                        </div>
                        <div class=" {{ $errors->has('email') ? 'has-error' : '' }}">
                            <input type="email" name="email" class="form-control input-lg" required
                                placeholder="@lang('blog/form.ph-email')" value="{{ old('email') }}">
                            <span class="form-text">{{ $errors->first('email', ':message') }}</span>
                        </div>
                        <div class=" {{ $errors->has('website') ? 'has-error' : '' }}">
                            <input type="text" name="website" class="form-control input-lg"
                                placeholder="@lang('blog/form.ph-website')" value="{{ old('website') }}">
                            <span class="form-text">{{ $errors->first('website', ':message') }}</span>
                        </div>
                        <div class=" {{ $errors->has('comment') ? 'has-error' : '' }}">
                            <textarea name="comment" class="form-control input-lg no-resize" required id="comment"
                                style="height: 200px" placeholder="@lang('blog/form.ph-comment')">{{ old('comment') }}</textarea>
                            <span class="form-text">{{ $errors->first('comment', ':message') }}</span>
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-success btn-md"><i class="fa fa-comment"></i>
                                @lang('blog/form.send-comment')
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /the.box .no-border -->
            </div>
            <!-- /.col-sm-9 -->
        </div>
        <!--main content ends-->
    </section>
@stop
@section('footer_scripts')
    <script>
        $("img").addClass("img-responsive");
    </script>
@stop

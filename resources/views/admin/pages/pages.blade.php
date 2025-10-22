@extends('layouts.admin')
{{-- Page title --}}
@section('title')
{{ get_phrase('Pages') }}
@parent
@stop

{{-- Page level styles --}}
@section('header_styles')
<link rel="stylesheet" href="{{ asset('css/Pages.css') }}" />
<link rel="stylesheet" href="/public/assets/frontend/frontend/plyr/plyr.css">

@stop

{{-- Page content --}}
@section('content')
<section class="content-header">
    <h1>{{ get_phrase('Pages') }}</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                {{ get_phrase('Dashboard') }}
            </a>
        </li>
        <li><a href="#">{{ get_phrase('Pages') }}</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content ps-3 pe-3">
    <div class="row">
        <div class="page-content ng_page_tab">
            <div class="page-tab bg-white   p-3 pb-1">
                <div class="d-flex pagetab-head  justify-content-between">
                    <h3 class="h5"><span><i class="fa fa-flag"></i></span> {{get_phrase('Pages')}}</h3>
                    <a href="javascript:void(0)" onclick="showCustomModal('{{route('admin.load_modal_content', ['view_path' => 'admin.pages.create_page'])}}', '{{get_phrase('Create Page')}}');" data-bs-toggle="modal"
                        data-bs-target="#createPage" class="btn common_btn"> <i class="fa fa-plus-circle"></i>{{get_phrase('Create Page')}}</a>
                </div>
                <ul class="nav ct-tab mt-1" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="mypage-tab" data-bs-toggle="tab"
                            data-bs-target="#mypage" type="button" role="tab" aria-controls="mypage"
                            aria-selected="true">{{ get_phrase('My Pages') }} </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="suggest-page-tab" data-bs-toggle="tab"
                            data-bs-target="#suggest-page" type="button" role="tab"
                            aria-controls="suggest-page" aria-selected="false"> {{ get_phrase('Suggested Pages') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="linked-page-tab" data-bs-toggle="tab"
                            data-bs-target="#linked-page" type="button" role="tab"
                            aria-controls="linked-page" aria-selected="false">{{ get_phrase('Liked Pages') }}</button>
                    </li>
                </ul>
            </div>
            <div class="tab-content bg-white p-3 rounded mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="mypage" role="tabpanel"
                    aria-labelledby="mypage-tab">
                    @include('admin.pages.single-page')
                </div><!--  Tab Pane End -->

                <div class="tab-pane fade" id="suggest-page" role="tabpanel"
                    aria-labelledby="suggest-page-tab">

                    @include('admin.pages.suggested')

                </div><!--  Tab Pane End -->
                <div class="tab-pane fade" id="linked-page" role="tabpanel"
                    aria-labelledby="linked-page-tab">

                    @include('admin.pages.liked-page')

                </div><!--  Tab Pane End -->
            </div> <!-- Tab Content End -->
        </div> <!-- Page Content End -->
    </div>
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
<script>
    // Custom Scripts for Sidebar toggling and other functionalities can go here
</script>
@stop

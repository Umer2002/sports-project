@extends('layouts.admin')
@section('title', 'View News')

@section('content')
<section class="content-header">
    <h1>News Details</h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="livicon" data-name="home" data-size="16" data-color="#000"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.news.index') }}">News</a></li>
        <li class="active">View News</li>
    </ol>
</section>

<div class="row">
    <div class="col-lg-8">
        <div class="card rounded-2xl shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $news->title }}</h4>
            </div>
            <div class="card-body">
                <p class="mb-3">
                    <span class="badge bg-info">{{ ucfirst($news->category) }}</span>
                </p>

                @if ($news->image)
                    <div class="mb-4">
                        <img src="{{ asset('uploads/news/' . $news->image) }}" alt="News Image" class="img-fluid rounded-2xl">
                    </div>
                @endif

                <div class="mb-4">
                    {!! $news->content !!}
                </div>

                <p class="text-muted">
                    <strong>Created At:</strong> {{ $news->created_at->format('d M Y, h:i A') }}<br>
                    <strong>Updated At:</strong> {{ $news->updated_at->format('d M Y, h:i A') }}
                </p>

                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-label mt-3">
                    <i class="ti ti-arrow-left label-icon"></i> Back to News List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@extends($layout ?? 'layouts.player-new')

@section('title', $videoData['title'] ? $videoData['title'] . ' · Video' : 'Video Detail')
@section('page-title', 'Explore Video')

@section('content')
<div class="container-fluid py-3">
    <div
        id="video-detail-root"
        data-video='@json($videoData, JSON_UNESCAPED_UNICODE)'
        data-related='@json($relatedVideosData, JSON_UNESCAPED_UNICODE)'
        data-like-url="{{ $endpoints['like'] }}"
        data-unlike-url="{{ $endpoints['unlike'] }}"
        data-comment-url="{{ $endpoints['comment'] }}"
        data-comments-url="{{ $endpoints['comments'] }}"
        data-share-url="{{ $endpoints['share'] }}"
        data-back-url="{{ $endpoints['back'] }}"
        data-show-base-url="{{ url('/player/videos/explore') }}"
    ></div>
    <noscript>
        <div class="alert alert-warning mt-3">Enable JavaScript to view and interact with this video.</div>
    </noscript>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/react/video-detail.jsx'])
@endpush

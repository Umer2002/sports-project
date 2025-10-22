@extends('layouts.app')
@section('title', $blog->title)

@section('content')
<div class="container py-5">

    <div class="row">
        <div class="col-lg-8">
            <article class="p-4 rounded-4 bg-light dark:bg-dark text-dark dark:text-light shadow-lg">
                <div class="mb-4 ratio ratio-16x9 bg-secondary-subtle dark:bg-gray-800 d-flex align-items-center justify-content-center text-muted rounded-3">
                    @if($blog->image)
                        <img src="{{ asset('storage/'.$blog->image) }}" alt="{{ $blog->title }}" class="img-fluid rounded-3 w-100 h-100 object-fit-cover">
                    @else
                        <span>Replace with your cover image</span>
                    @endif
                </div>

                <h2 class="fw-bold text-primary-emphasis mb-3 fst-italic">
                    {{ $blog->title }}
                </h2>

                <p class="small text-muted mb-3">
                    By {{ $blog->user->name ?? 'Anonymous' }} •
                    {{ $blog->created_at->format('M d, Y') }} •
                    {{ str_word_count(strip_tags($blog->content)) / 200 | round(0) }} min read
                </p>

                <div class="blog-content fs-5 lh-lg">
                    {!! nl2br(e($blog->content)) !!}
                </div>
            </article>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <aside class="p-4 rounded-4 bg-light dark:bg-dark shadow-lg">
                <h4 class="fw-semibold mb-3 text-primary">Related Articles</h4>

                @forelse($related as $r)
                    <div class="d-flex mb-4 align-items-center p-2 rounded-3 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="flex-shrink-0 me-3" style="width:70px;height:70px;">
                            @if($r->image)
                                <img src="{{ asset('storage/'.$r->image) }}" class="rounded-3 w-100 h-100 object-fit-cover" alt="{{ $r->title }}">
                            @else
                                <div class="bg-secondary w-100 h-100 rounded-3 d-flex align-items-center justify-content-center text-light small">No Image</div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">{{ Str::limit($r->title, 40) }}</h6>
                            <p class="text-muted small mb-2">By {{ $r->user->name ?? 'Guest' }} • {{ $r->created_at->diffForHumans() }}</p>
                            <a href="{{ route('blogs.show', $r->slug) }}" class="btn btn-sm btn-primary">
                                Read Article
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No related articles available.</p>
                @endforelse
            </aside>
        </div>
    </div>

</div>
@endsection

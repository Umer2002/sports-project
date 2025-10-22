@php
    use Illuminate\Support\Str;
    $context = $blogContext ?? request()->query('context');
    $resolvedLayout = $blogLayout ?? (($context === 'club' && optional(auth()->user())->hasRole('club')) ? 'layouts.club-dashboard' : 'layouts.player-new');
    $isClubLayout = $resolvedLayout === 'layouts.club-dashboard';
    $blogRouteQuery = $context ? ['context' => $context] : [];
@endphp

@extends($resolvedLayout)

@section('title', $blog->title ? $blog->title . ' • Story' : 'Story Detail')
@section('page-title', $isClubLayout ? 'Blog Detail' : 'Team Story')
@if ($isClubLayout)
    @section('page_title', 'Blog Detail')
@endif

@push('styles')
<style>
    .story-detail-page {
        display: block;
    }

    .story-author {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .story-author-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.55), rgba(14, 165, 233, 0.4));
        color: #0f172a;
        font-weight: 700;
        display: grid;
        place-items: center;
    }

    .story-author-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .story-body-content {
        font-size: 1rem;
        line-height: 1.72;
        color: color-mix(in srgb, var(--bs-body-color) 92%, transparent);
    }

    .story-body-content h2,
    .story-body-content h3,
    .story-body-content h4 {
        margin-top: 28px;
        margin-bottom: 16px;
    }

    .story-body-content img {
        max-width: 100%;
        border-radius: 12px;
    }

    .story-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: color-mix(in srgb, var(--bs-info) 18%, transparent);
        color: var(--bs-info-text-emphasis, var(--bs-info));
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .story-metrics {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .story-comments-list {
        display: grid;
        gap: 16px;
    }

    .story-comment {
        border-radius: 14px;
        border: 1px solid var(--bs-border-color);
        padding: 14px 16px;
        background: var(--bg-card);
    }

    .story-comment-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-weight: 600;
    }

    .story-comment-meta {
        font-size: 0.8rem;
        color: var(--bs-secondary-color);
    }

    .story-comment-body {
        margin: 6px 0 0;
        color: color-mix(in srgb, var(--bs-body-color) 90%, transparent);
        line-height: 1.55;
    }

    .story-comment-form textarea {
        resize: vertical;
    }

    @media (max-width: 991.98px) {
        .story-author {
            flex-direction: row;
        }
    }

    html[data-theme='dark'] .story-body-content,
    [data-bs-theme='dark'] .story-body-content {
        color: rgba(226, 232, 240, 0.88);
    }

    html[data-theme='dark'] .story-comment,
    [data-bs-theme='dark'] .story-comment {
        background: color-mix(in srgb, #0f172a 85%, transparent);
        border-color: color-mix(in srgb, var(--bs-border-color) 65%, transparent);
    }

    html[data-theme='dark'] .story-comment-body,
    [data-bs-theme='dark'] .story-comment-body {
        color: rgba(226, 232, 240, 0.92);
    }
</style>
@endpush

@section('content')
@php
    $mediaFile = $blog->image;
    $mediaUrl = $mediaFile ? asset('uploads/blog/' . $mediaFile) : null;
    $mediaExtension = $mediaFile ? strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION)) : null;
    $videoExtensions = ['mp4','mov','qt','avi','wmv','mkv','webm','m4v'];
    $isVideo = $mediaExtension && in_array($mediaExtension, $videoExtensions, true);
    $teamName = data_get($blog, 'user.player.team.name') ?? data_get($blog, 'user.player.teams.0.name');
@endphp

<div class="container-fluid py-3 story-detail-page">
    <div class="mb-3 d-flex align-items-center gap-2">
        <a href="{{ route('player.blogs.index', $blogRouteQuery) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Feed
        </a>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="ratio ratio-16x9">
                    @if($mediaUrl)
                        @if($isVideo)
                            <video src="{{ $mediaUrl }}" controls playsinline class="w-100 h-100" style="object-fit: cover;"></video>
                        @else
                            <img src="{{ $mediaUrl }}" alt="Media for {{ $blog->title }}" class="w-100 h-100" style="object-fit: cover;">
                        @endif
                    @else
                        <img src="{{ asset('assets/player-dashboard/banners/screen-banner.png') }}" alt="Team story placeholder" class="w-100 h-100" style="object-fit: cover;">
                    @endif
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div class="story-author">
                            <div class="story-author-avatar">
                                @if(!empty($blog->user?->photo))
                                    <img src="{{ asset('storage/players/' . $blog->user->photo) }}" alt="{{ $blog->user->name }}">
                                @else
                                    {{ Str::of($blog->user->name ?? 'A')->substr(0, 2)->upper() }}
                                @endif
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $blog->title ?? 'Team Story' }}</h4>
                                <div class="small text-muted">
                                    {{ optional($blog->created_at)->format('M j, Y \a\t g:i A') ?? 'Recently shared' }}
                                    @if($teamName)
                                        · {{ $teamName }}
                                    @endif
                                    @if(!$blog->is_public)
                                        · <i class="fa fa-lock"></i> Club Only
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 story-metrics video-action-buttons">
                            <div class="badge video-action-badge">
                                <span data-like-count>{{ $blog->likes_count }}</span> Likes
                            </div>
                            <div class="badge video-action-badge">
                                <span data-comment-count>{{ $blog->comments_count }}</span> Comments
                            </div>
                            <button
                                type="button"
                                class="btn btn-sm video-action-btn {{ $blog->is_liked ? 'video-action-btn--active' : 'video-action-btn--outline' }}"
                                data-like-button
                                data-like-url="{{ route('player.blogs.like', $blog->id) }}"
                                data-liked="{{ $blog->is_liked ? '1' : '0' }}">
                                <i class="fa fa-thumbs-up me-1"></i>{{ $blog->is_liked ? 'Liked' : 'Like' }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm video-action-btn video-action-btn--filled"
                                data-share-button
                                data-share-url="{{ route('player.blogs.show', array_merge(['post' => $blog->id], $blogRouteQuery)) }}"
                                data-share-title="{{ $blog->title ?: 'Play2Earn Update' }}">
                                <i class="fa fa-share-nodes me-1"></i> Share
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="story-chip">
                            <i class="fa fa-hashtag"></i> Team Feed
                        </span>
                        @if($teamName)
                            <span class="story-chip">
                                <i class="fa fa-users"></i> {{ $teamName }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-3 story-body-content ck-content">
                        {!! $blog->content !!}
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Discussion</h5>
                    <span class="text-muted small"><span data-comment-count>{{ $blog->comments_count }}</span> comments</span>
                </div>
                <div
                    class="card-body"
                    data-comments-panel
                    data-comments-url="{{ route('player.blogs.comments.index', $blog->id) }}"
                    data-comment-submit-url="{{ route('player.blogs.comments.store', $blog->id) }}"
                    data-loaded="true">
                    <div class="story-comments-list mb-3" data-comments-list>
                        @forelse ($commentFeed as $comment)
                            <div class="story-comment">
                                <div class="story-comment-header">
                                    <span>{{ $comment['name'] ?? 'Teammate' }}</span>
                                    <span class="story-comment-meta" @if(!empty($comment['created_at_exact'])) title="{{ $comment['created_at_exact'] }}" @endif>{{ $comment['created_at'] ?? '' }}</span>
                                </div>
                                <p class="story-comment-body">{{ $comment['comment'] ?? '' }}</p>
                            </div>
                        @empty
                            <p class="text-muted story-comments-empty">Be the first to comment.</p>
                        @endforelse
                    </div>
                    <form class="story-comment-form" data-comment-form>
                        <div class="mb-2">
                            <textarea name="comment" data-comment-input class="form-control" rows="3" placeholder="Share your thoughts..." maxlength="500" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn video-action-btn video-action-btn--filled" data-comment-submit>Post Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-2">Need to update?</h5>
                    <p class="text-muted mb-3">Made a typo or have more to share? Head back to the editor to update your post any time.</p>
                    <a href="{{ route('player.blogs.create', $blogRouteQuery) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-pen me-1"></i> Publish another update
                    </a>
                </div>
            </div>

            @if($recentPosts->isNotEmpty())
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Recent stories</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($recentPosts as $recent)
                                <a href="{{ route('player.blogs.show', $recent->id) }}" class="list-group-item list-group-item-action">
                                    <div class="fw-semibold">{{ Str::limit($recent->title, 60) }}</div>
                                    <div class="small text-muted">{{ optional($recent->created_at)->diffForHumans() ?? '' }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
            const likeButton = document.querySelector('[data-like-button]');
            const likeCountEls = document.querySelectorAll('[data-like-count]');
            const commentCountEls = document.querySelectorAll('[data-comment-count]');
            const shareButton = document.querySelector('[data-share-button]');
            const commentsPanel = document.querySelector('[data-comments-panel]');
            const commentList = commentsPanel?.querySelector('[data-comments-list]');
            const commentForm = commentsPanel?.querySelector('[data-comment-form]');

            const buildHeaders = (isJson = false) => {
                const headers = { 'Accept': 'application/json' };
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }
                if (isJson) {
                    headers['Content-Type'] = 'application/json';
                }
                return headers;
            };

            const updateLikeUI = (state) => {
                const liked = Boolean(state?.liked);
                const total = typeof state?.likes_count === 'number' ? state.likes_count : null;
                if (likeButton) {
                    likeButton.dataset.liked = liked ? '1' : '0';
                    likeButton.classList.toggle('video-action-btn--outline', !liked);
                    likeButton.classList.toggle('video-action-btn--active', liked);
                    likeButton.innerHTML = `${liked ? '<i class="fa fa-thumbs-up me-1"></i> Liked' : '<i class="fa fa-thumbs-up me-1"></i> Like'}`;
                }
                if (total !== null) {
                    likeCountEls.forEach((el) => {
                        el.textContent = total;
                    });
                }
            };

            const updateCommentCount = (total) => {
                if (typeof total !== 'number') {
                    return;
                }
                commentCountEls.forEach((el) => {
                    el.textContent = total;
                });
            };

            const buildCommentElement = (comment) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'story-comment';

                const header = document.createElement('div');
                header.className = 'story-comment-header';

                const author = document.createElement('span');
                author.textContent = comment?.name || 'Teammate';
                header.appendChild(author);

                const meta = document.createElement('span');
                meta.className = 'story-comment-meta';
                if (comment?.created_at) {
                    meta.textContent = comment.created_at;
                }
                if (comment?.created_at_exact) {
                    meta.title = comment.created_at_exact;
                }
                header.appendChild(meta);

                const body = document.createElement('p');
                body.className = 'story-comment-body';
                body.textContent = comment?.comment || '';

                wrapper.appendChild(header);
                wrapper.appendChild(body);

                return wrapper;
            };

            const prependComment = (comment) => {
                if (!commentList) {
                    return;
                }
                if (commentList.firstElementChild?.classList.contains('story-comments-empty')) {
                    commentList.innerHTML = '';
                }
                commentList.prepend(buildCommentElement(comment));
            };

            likeButton?.addEventListener('click', async () => {
                const url = likeButton.dataset.likeUrl;
                if (!url || likeButton.disabled) {
                    return;
                }

                likeButton.disabled = true;
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: buildHeaders(true),
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data?.message || 'Unable to update like right now.');
                    }

                    const payload = await response.json();
                    updateLikeUI(payload);
                } catch (error) {
                    console.error('Like toggle failed', error);
                    alert(error.message || 'Unable to update like right now.');
                } finally {
                    likeButton.disabled = false;
                }
            });

            shareButton?.addEventListener('click', async () => {
                const shareUrl = shareButton.dataset.shareUrl;
                const shareTitle = shareButton.dataset.shareTitle || document.title;
                if (!shareUrl) {
                    return;
                }

                try {
                    if (navigator.share) {
                        await navigator.share({ url: shareUrl, title: shareTitle });
                        return;
                    }

                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(shareUrl);
                        alert('Link copied to clipboard.');
                        return;
                    }

                    const fallback = prompt('Copy this link to share', shareUrl);
                    if (fallback !== null) {
                        alert('Link ready! Share it with your teammates.');
                    }
                } catch (error) {
                    if (error?.name === 'AbortError') {
                        return;
                    }
                    console.error('Share failed', error);
                    alert('Unable to share right now. Copy the link manually.');
                }
            });

            commentForm?.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (!commentsPanel) {
                    return;
                }

                const input = commentForm.querySelector('[data-comment-input]');
                const message = input?.value?.trim() ?? '';
                if (!message) {
                    return;
                }

                const submitButton = commentForm.querySelector('[data-comment-submit]');
                const originalLabel = submitButton?.textContent ?? 'Post Comment';
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Posting…';
                }

                const submitUrl = commentsPanel.dataset.commentSubmitUrl;
                if (!submitUrl) {
                    alert('Unable to add comment right now.');
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalLabel;
                    }
                    return;
                }

                try {
                    const response = await fetch(submitUrl, {
                        method: 'POST',
                        headers: buildHeaders(true),
                        body: JSON.stringify({ comment: message }),
                    });

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        const errorMessage = payload?.errors?.comment?.[0] || payload?.message || 'Unable to add comment.';
                        throw new Error(errorMessage);
                    }

                    const payload = await response.json();
                    prependComment(payload.comment);
                    if (input) {
                        input.value = '';
                    }
                    if (typeof payload.comments_count !== 'undefined') {
                        updateCommentCount(payload.comments_count);
                    }
                } catch (error) {
                    console.error('Comment submit failed', error);
                    alert(error.message || 'Unable to add comment right now.');
                } finally {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalLabel;
                    }
                }
            });
        });
    </script>
@endpush

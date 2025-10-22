@php
    use Illuminate\Support\Str;
    $context = $blogContext ?? request()->query('context');
    $resolvedLayout = $blogLayout ?? (($context === 'club' && optional(auth()->user())->hasRole('club')) ? 'layouts.club-dashboard' : 'layouts.player-new');
    $isClubLayout = $resolvedLayout === 'layouts.club-dashboard';
    $blogRouteQuery = $context ? ['context' => $context] : [];
@endphp

@extends($resolvedLayout)

@section('title', $isClubLayout ? 'Club Blogs' : 'Team Feed')

@if ($isClubLayout)
    @section('page_title', 'Blogs')
@endif

@section('header_styles')
    <style>
        .blog-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 28px 32px;
            border-radius: 24px;
            background: linear-gradient(
                150deg,
                color-mix(in srgb, var(--bs-primary) 24%, transparent) 0%,
                color-mix(in srgb, var(--bs-info) 18%, transparent) 55%,
                color-mix(in srgb, var(--bs-primary) 22%, var(--bs-secondary-bg)) 100%
            );
            color: var(--bs-emphasis-color);
        }

        .blog-hero h1 {
            margin: 0;
            font-size: clamp(1.8rem, 1vw + 1.4rem, 2.3rem);
            font-weight: 600;
            color: var(--bs-emphasis-color);
        }

        .blog-hero p {
            margin: 6px 0 0;
            color: var(--bs-secondary-color);
        }

        [data-bs-theme='dark'] .blog-hero {
            background: linear-gradient(
                150deg,
                color-mix(in srgb, var(--bs-primary) 26%, transparent) 0%,
                color-mix(in srgb, var(--bs-info) 24%, transparent) 55%,
                color-mix(in srgb, var(--bs-primary) 30%, var(--bg-secondary)) 100%
            );
        }

        .blog-layout {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(260px, 1fr);
            gap: 28px;
            margin-top: 28px;
        }

        .composer-card,
        .post-card,
        .blog-sidebar {
            background: var(--bs-card-bg);
            border-radius: 22px;
            border: 1px solid var(--bs-border-color-translucent);
            box-shadow: 0 24px 40px -28px var(--shadow);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .composer-card {
            padding: 28px 30px;
            display: grid;
            gap: 18px;
            cursor: pointer;
        }

        .composer-card:focus-within,
        .composer-card:hover {
            border-color: color-mix(in srgb, var(--bs-primary) 40%, var(--bs-border-color));
            box-shadow: 0 26px 48px -32px color-mix(in srgb, var(--bs-info) 32%, transparent);
        }

        .composer-heading {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .composer-bubble {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(140deg, #2dd4ff 0%, #6366f1 55%, #8b5cf6 100%);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.3rem;
            box-shadow: 0 18px 32px -18px rgba(99, 102, 241, 0.45);
        }

        .composer-heading strong {
            font-size: 1.08rem;
            color: var(--bs-emphasis-color);
        }

        .composer-heading p {
            margin: 4px 0 0;
            color: var(--bs-secondary-color);
        }

        .composer-field,
        .composer-tags-input {
            border-radius: 18px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent);
            background: color-mix(in srgb, var(--bg-secondary) 85%, transparent);
            min-height: 74px;
            padding: 18px;
            text-align: left;
            color: var(--bs-body-color);
            font-size: 1rem;
        }

        .composer-field {
            display: block;
            width: 100%;
        }

        .composer-field[role="button"] {
            border: none;
            background: color-mix(in srgb, var(--bg-secondary) 92%, transparent);
            box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--bs-border-color) 70%, transparent);
            cursor: pointer;
        }

        .composer-input {
            min-height: auto;
        }

        .composer-attachment {
            margin-bottom: 18px;
        }

        .composer-attachment-inner {
            position: relative;
            border-radius: 18px;
            padding: 16px;
            background: color-mix(in srgb, var(--bs-info) 16%, transparent);
            border: 1px dashed color-mix(in srgb, var(--bs-primary) 60%, transparent);
        }

        .composer-attachment-media img,
        .composer-attachment-media video {
            width: 100%;
            border-radius: 14px;
            max-height: 260px;
            object-fit: cover;
        }

        .composer-attachment-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            border: none;
            border-radius: 999px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--bs-body-color) 12%, #111827 88%);
            color: #fff;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 10px 25px -16px rgba(15, 23, 42, 0.35);
        }

        .composer-attachment-remove:hover {
            filter: brightness(1.1);
        }

        .composer-tags-input {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .composer-tags-input span {
            color: var(--bs-secondary-color);
            font-size: 0.9rem;
        }

        .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.4);
            color: rgba(15, 23, 42, 0.8);
            font-weight: 600;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(129, 140, 248, 0.16);
            color: rgba(79, 70, 229, 0.95);
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .tag-chip button {
            border: none;
            background: transparent;
            color: rgba(100, 116, 139, 0.7);
            cursor: pointer;
        }

        html[data-theme='dark'] .composer-field,
        html[data-theme='dark'] .composer-tags-input,
        [data-bs-theme='dark'] .composer-field,
        [data-bs-theme='dark'] .composer-tags-input {
            background: rgba(30, 41, 59, 0.8);
            color: rgba(226, 232, 240, 0.9);
            border-color: rgba(71, 85, 105, 0.4);
        }

        html[data-theme='dark'] .composer-attachment-inner,
        [data-bs-theme='dark'] .composer-attachment-inner {
            background: rgba(30, 41, 59, 0.82);
            border-color: rgba(99, 102, 241, 0.35);
        }

        html[data-theme='dark'] .tag-chip,
        [data-bs-theme='dark'] .tag-chip {
            background: rgba(15, 23, 42, 0.9);
            border-color: rgba(71, 85, 105, 0.55);
            color: rgba(226, 232, 240, 0.92);
        }

        html[data-theme='dark'] .chip,
        [data-bs-theme='dark'] .chip {
            background: rgba(99, 102, 241, 0.28);
            color: rgba(226, 232, 240, 0.85);
        }

        .composer-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
        }

        .composer-visibility {
            display: inline-flex;
            border-radius: 18px;
            background: rgba(226, 232, 240, 0.65);
            padding: 4px;
        }

        .composer-visibility button {
            border: none;
            border-radius: 14px;
            padding: 8px 18px;
            font-weight: 600;
            color: rgba(71, 85, 105, 0.9);
            background: transparent;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .composer-visibility button.active {
            background: linear-gradient(130deg, #2dd4ff, #6366f1);
            color: #fff;
            box-shadow: 0 10px 24px -12px rgba(99, 102, 241, 0.4);
        }

        html[data-theme='dark'] .composer-visibility,
        [data-bs-theme='dark'] .composer-visibility {
            background: rgba(30, 41, 59, 0.75);
        }

        .composer-action-buttons {
            display: inline-flex;
            gap: 12px;
        }

        .composer-action-buttons button {
            border-radius: 16px;
            border: none;
            font-weight: 600;
            padding: 10px 20px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .composer-actions {
            display: inline-flex;
            gap: 12px;
            align-items: center;
        }

        .composer-primary,
        .composer-secondary {
            border-radius: 16px;
            border: none;
            padding: 10px 22px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .composer-primary,
        .composer-submit {
            background: linear-gradient(130deg, #38bdf8, #6366f1);
            color: #fff;
            box-shadow: 0 18px 35px -22px rgba(99, 102, 241, 0.6);
        }

        .composer-secondary,
        .composer-upload {
            background: rgba(226, 232, 240, 0.9);
            color: rgba(71, 85, 105, 0.85);
        }

        .composer-action-buttons button:hover {
            transform: translateY(-1px);
        }

        .composer-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 16px;
            gap: 16px;
        }

        .composer-attachment-hint {
            font-size: 0.85rem;
            color: rgba(100, 116, 139, 0.75);
        }

        html[data-theme='dark'] .composer-secondary,
        html[data-theme='dark'] .composer-upload,
        [data-bs-theme='dark'] .composer-secondary,
        [data-bs-theme='dark'] .composer-upload {
            background: rgba(71, 85, 105, 0.7);
            color: rgba(226, 232, 240, 0.88);
        }

        .post-card {
            margin-top: 20px;
            padding: 22px 26px;
        }

        .post-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .post-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .post-avatar {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.18);
            display: grid;
            place-items: center;
            font-weight: 600;
        }

        .post-meta small {
            display: block;
            color: rgba(100, 116, 139, 0.7);
        }

        .post-body {
            margin: 16px 0;
            color: rgba(15, 23, 42, 0.85);
        }

        .post-title {
            font-weight: 600;
            margin-bottom: 6px;
            color: rgba(15, 23, 42, 0.9);
        }

        .post-media {
            width: 100%;
            border-radius: 16px;
            margin: 12px 0 16px;
            overflow: hidden;
        }

        .post-media img,
        .post-media video {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 16px;
            object-fit: cover;
        }

        .post-media video {
            max-height: 420px;
            background: #000;
        }

        .post-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 0.9rem;
            color: rgba(100, 116, 139, 0.9);
        }

        .post-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            background: none;
            color: inherit;
            padding: 0;
            font: inherit;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .post-action:hover,
        .post-action:focus-visible {
            color: rgba(37, 99, 235, 0.85);
        }

        .post-action:focus-visible {
            outline: 2px solid rgba(37, 99, 235, 0.35);
            outline-offset: 2px;
        }

        .post-action[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .post-action.is-liked {
            color: rgba(37, 99, 235, 0.95);
            font-weight: 600;
        }

        .post-action-count {
            font-weight: 600;
        }

        .post-engagement {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid rgba(226, 232, 240, 0.7);
            display: grid;
            gap: 16px;
        }

        .post-comments-list {
            display: grid;
            gap: 12px;
        }

        .post-comments-empty,
        .post-comments-error {
            margin: 0;
            font-size: 0.9rem;
            color: rgba(100, 116, 139, 0.85);
        }

        .post-comment {
            display: grid;
            gap: 6px;
            border-radius: 14px;
            padding: 12px 14px;
            background: rgba(248, 250, 252, 0.92);
        }

        .post-comment-header {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            font-weight: 600;
            color: rgba(15, 23, 42, 0.85);
        }

        .post-comment-body {
            margin: 0;
            color: rgba(15, 23, 42, 0.78);
        }

        .post-comment-meta {
            font-size: 0.78rem;
            color: rgba(100, 116, 139, 0.7);
        }

        .post-comment-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .post-comment-form input {
            flex: 1;
            border-radius: 999px;
            border: 1px solid rgba(203, 213, 225, 0.9);
            padding: 10px 16px;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.95);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .post-comment-form input:focus {
            border-color: rgba(59, 130, 246, 0.7);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        .post-comment-form button {
            border: none;
            border-radius: 999px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            background: linear-gradient(125deg, #60a5fa, #6366f1);
            color: #fff;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .post-comment-form button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 34px -24px rgba(79, 70, 229, 0.7);
        }

        .post-comment-form button[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .blog-sidebar {
            padding: 22px 24px;
            display: grid;
            gap: 18px;
            height: fit-content;
        }

        .sidebar-widget h3 {
            margin: 0 0 12px;
            font-size: 1rem;
            color: rgba(15, 23, 42, 0.75);
        }

        .sidebar-item {
            display: block;
            padding: 14px 16px;
            border-radius: 16px;
            background: rgba(226, 232, 240, 0.55);
            color: rgba(15, 23, 42, 0.75);
            text-decoration: none;
            margin-bottom: 10px;
        }

        .sidebar-item:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        html[data-theme='dark'] .post-comment,
        [data-bs-theme='dark'] .post-comment {
            background: rgba(30, 41, 59, 0.82);
        }

        html[data-theme='dark'] .post-comment-body,
        [data-bs-theme='dark'] .post-comment-body {
            color: rgba(226, 232, 240, 0.86);
        }

        html[data-theme='dark'] .post-comment-header,
        [data-bs-theme='dark'] .post-comment-header {
            color: rgba(226, 232, 240, 0.96);
        }

        html[data-theme='dark'] .post-comment-meta,
        html[data-theme='dark'] .post-comments-empty,
        html[data-theme='dark'] .post-comments-error,
        [data-bs-theme='dark'] .post-comment-meta,
        [data-bs-theme='dark'] .post-comments-empty,
        [data-bs-theme='dark'] .post-comments-error {
            color: rgba(148, 163, 184, 0.78);
        }

        html[data-theme='dark'] .post-comment-form input,
        [data-bs-theme='dark'] .post-comment-form input {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(71, 85, 105, 0.7);
            color: rgba(226, 232, 240, 0.95);
        }

        html[data-theme='dark'] .post-comment-form input::placeholder,
        [data-bs-theme='dark'] .post-comment-form input::placeholder {
            color: rgba(148, 163, 184, 0.7);
        }

        html[data-theme='dark'] .post-action,
        [data-bs-theme='dark'] .post-action {
            color: rgba(148, 163, 184, 0.85);
        }

        html[data-theme='dark'] .post-action.is-liked,
        [data-bs-theme='dark'] .post-action.is-liked {
            color: rgba(96, 165, 250, 0.92);
        }

        html[data-theme='dark'] .post-engagement,
        [data-bs-theme='dark'] .post-engagement {
            border-top-color: rgba(51, 65, 85, 0.82);
        }

        html[data-theme='dark'] .post-body,
        html[data-theme='dark'] .post-title,
        html[data-theme='dark'] .sidebar-widget h3,
        html[data-theme='dark'] .sidebar-item,
        [data-bs-theme='dark'] .post-body,
        [data-bs-theme='dark'] .post-title,
        [data-bs-theme='dark'] .sidebar-widget h3,
        [data-bs-theme='dark'] .sidebar-item {
            color: rgba(226, 232, 240, 0.85);
        }

        html[data-theme='dark'] .sidebar-item,
        [data-bs-theme='dark'] .sidebar-item {
            background: rgba(30, 41, 59, 0.75);
        }

        .empty-feed {
            padding: 40px 24px;
            text-align: center;
            border-radius: 20px;
            border: 1px dashed rgba(148, 163, 184, 0.35);
            background: rgba(248, 250, 252, 0.8);
            color: rgba(148, 163, 184, 0.85);
            margin-top: 20px;
        }

        html[data-theme='dark'] .empty-feed,
        [data-bs-theme='dark'] .empty-feed {
            background: rgba(15, 23, 42, 0.65);
            color: rgba(203, 213, 225, 0.85);
        }

        @media (max-width: 1100px) {
            .blog-layout {
                grid-template-columns: 1fr;
            }

            .blog-sidebar {
                order: -1;
            }
        }

        @media (max-width: 640px) {
            .composer-card,
            .post-card {
                padding: 18px;
            }

            .composer-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .composer-action-buttons {
                justify-content: flex-end;
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $playerModel = auth()->user()?->player;
        $primaryTeamName = optional($playerModel?->team)->name ?? optional($playerModel?->teams?->first())->name;
        $playerTeamSlug = $primaryTeamName ? Str::slug($primaryTeamName) : '';
        $defaultComposerTags = ['#tournament', '#highlights'];
    @endphp

    <div class="blog-hero">
        <div>
            <h1>Play 2 Earn — Posts</h1>
            <p>Share updates, photos, or videos with your club and the wider community.</p>
        </div>
        <div class="feed-filters">
            <button class="feed-filter active" type="button" data-feed-filter="all">All Posts</button>
            <button class="feed-filter" type="button" data-feed-filter="club" @if(!$playerTeamSlug) disabled @endif>My Club</button>
        </div>
    </div>

    <div class="blog-layout">
        <div>
            <div class="composer-card" data-player-team="{{ $playerTeamSlug }}">
                <div class="composer-heading">
                    <span class="composer-bubble" aria-hidden="true">✦</span>
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Create a post</strong>
                            <div class="composer-action-buttons">
                                <button type="button" class="composer-upload" data-open-blog-modal>Upload</button>
                                <button type="button" class="composer-submit" data-submit-inline-blog>Publish</button>
                            </div>
                        </div>
                        <p>Share updates, photos, or videos with your club or the public.</p>
                    </div>
                </div>
                <form id="inlineBlogForm" action="{{ route('player.blogs.save') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="visibility" id="composerVisibility" value="1">
                    <input type="hidden" name="tags" id="composerTagsField" value="{{ implode(',', $defaultComposerTags) }}">
                    <div class="mb-3">
                        <input type="text" class="composer-field composer-input" id="composerTitle" name="title" placeholder="Give your post a title" maxlength="191" required>
                    </div>
                    <div class="mb-3">
                        <textarea id="composerContent" class="composer-field composer-textarea" name="content"
                            placeholder="What's on your mind?" rows="4" required></textarea>
                    </div>
                    <input type="file" class="d-none" id="composerAttachment" name="feature_image" accept="image/*,video/*">
                    <div id="composerAttachmentPreview" class="composer-attachment d-none">
                        <div class="composer-attachment-inner">
                            <div class="composer-attachment-media" id="composerAttachmentPreviewContent"></div>
                            <button type="button" class="composer-attachment-remove" id="composerAttachmentRemove" aria-label="Remove attachment">&times;</button>
                        </div>
                    </div>
                </form>
                <div class="composer-tags-input" aria-label="Add tags">
                    <span>Add tags (press Enter)</span>
                    <div class="composer-tags" data-tag-list data-initial-tags='@json($defaultComposerTags)'></div>
                    <input type="text" id="composerTagInput" placeholder="#AddTag" autocomplete="off">
                </div>
                <div class="composer-controls">
                    <div class="composer-visibility" role="group" aria-label="Visibility toggle">
                        <button type="button" class="active" data-visibility="1">Public</button>
                        <button type="button" data-visibility="0">Club Only</button>
                    </div>
                    <span class="composer-attachment-hint">Need to attach media? Use Upload.</span>
                </div>
            </div>

            <div class="feed-stream" data-feed-stream>
                <div class="feed-grid" id="playerFeedGrid">
                    @forelse ($blogs as $blog)
                        @php
                            $user = $blog->user;
                            $teamName = data_get($blog, 'user.player.team.name') ?? data_get($blog, 'user.player.teams.0.name');
                            $teamSlug = $teamName ? Str::slug($teamName) : '';
                            $mediaFile = $blog->image;
                            $mediaUrl = $mediaFile
                                ? asset('uploads/blog/' . $mediaFile)
                                : null;
                            $mediaExtension = $mediaFile ? strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION)) : null;
                            $videoExtensions = ['mp4','mov','qt','avi','wmv','mkv','webm','m4v'];
                            $isVideo = $mediaExtension && in_array($mediaExtension, $videoExtensions, true);
                            $createdAtExact = optional($blog->created_at)->format('M jS, Y g:i A');
                            $createdAtHuman = optional($blog->created_at)->diffForHumans() ?? 'Just now';
                        @endphp
                        <article class="post-card" data-team="{{ $teamSlug }}">
                            <div class="post-header">
                                <div class="post-author">
                                    <div class="post-avatar">
                                        @if (!empty($user?->photo))
                                            <img src="{{ asset('storage/players/' . $user->photo) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 999px;">
                                        @else
                                            {{ Str::of($user->name ?? 'A')->substr(0, 2)->upper() }}
                                        @endif
                                    </div>
                                    <div class="post-meta">
                                        <strong>{{ $user->name ?? 'Teammate' }}</strong>
                                        <small @if ($createdAtExact) title="{{ $createdAtExact }}" @endif>{{ $createdAtHuman }}@if ($teamName) · {{ $teamName }}@endif</small>
                                    </div>
                                </div>
                                <span class="chip">Highlight</span>
                            </div>
                            <div class="post-body">
                                @if ($blog->title)
                                    <div class="post-title">{{ $blog->title }}</div>
                                @endif
                                <p>{{ Str::limit(strip_tags($blog->content), 160) }}</p>
                            </div>
                            @if ($mediaUrl)
                                <div class="post-media">
                                    @if ($isVideo)
                                        <video controls preload="metadata">
                                            <source src="{{ $mediaUrl }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src="{{ $mediaUrl }}" alt="{{ $blog->title }} media" loading="lazy">
                                    @endif
                                </div>
                            @endif
                            <div class="post-actions">
                                <button
                                    type="button"
                                    class="post-action {{ ($blog->is_liked ?? false) ? 'is-liked' : '' }}"
                                    data-like-button
                                    data-blog-id="{{ $blog->id }}"
                                    data-like-url="{{ route('player.blogs.like', $blog->id) }}"
                                    data-liked="{{ ($blog->is_liked ?? false) ? '1' : '0' }}">
                                    <span>Like</span>
                                    <span class="post-action-count" data-like-count>{{ $blog->likes_count ?? 0 }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="post-action"
                                    data-comment-toggle
                                    data-blog-id="{{ $blog->id }}"
                                    aria-controls="post-comments-{{ $blog->id }}"
                                    aria-expanded="false">
                                    <span>Comment</span>
                                    <span class="post-action-count" data-comment-count>{{ $blog->comments_count ?? 0 }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="post-action"
                                    data-share-button
                                    data-share-url="{{ route('player.blogs.show', array_merge(['post' => $blog->id], $blogRouteQuery)) }}"
                                    data-share-title="{{ $blog->title ?: 'Play2Earn Update' }}">
                                    <span>Share</span>
                                </button>
                            </div>
                            <div
                                class="post-engagement"
                                id="post-comments-{{ $blog->id }}"
                                data-comments-panel
                                data-blog-id="{{ $blog->id }}"
                                data-comments-url="{{ route('player.blogs.comments.index', $blog->id) }}"
                                data-comment-submit-url="{{ route('player.blogs.comments.store', $blog->id) }}"
                                hidden>
                                <div class="post-comments-list" data-comments-list>
                                    <p class="post-comments-empty">Be the first to comment.</p>
                                </div>
                                <form class="post-comment-form" data-comment-form>
                                    <input type="text" data-comment-input name="comment" placeholder="Add a comment…" maxlength="500" autocomplete="off" required>
                                    <button type="submit" data-comment-submit>Post</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-feed">
                            <h3>No posts yet</h3>
                            <p>Your team hasn’t shared anything yet. Be the first to kick things off with an update!</p>
                            <button class="composer-primary" type="button" data-open-blog-modal style="margin-top: 12px;">Create the first post</button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <aside class="blog-sidebar">
            <div class="sidebar-widget">
                <h3>Recent</h3>
                @foreach($blogs->take(5) as $recent)
                    <a class="sidebar-item" href="{{ route('player.blogs.show', array_merge(['post' => $recent->id], $blogRouteQuery)) }}">
                        Last video: {{ Str::limit($recent->title, 32) }}
                    </a>
                @endforeach
            </div>
            <div class="sidebar-widget">
                <h3>Ideas</h3>
                <a class="sidebar-item" href="#">New training plan released</a>
                <a class="sidebar-item" href="#">Tryouts open for U15</a>
                <a class="sidebar-item" href="#">Coach webinar tomorrow</a>
                <a class="sidebar-item" href="#">Sponsor spotlight: FitPro</a>
            </div>
        </aside>
    </div>
@endsection

@include('players.partials.blog-create-modal', ['defaultTags' => $defaultComposerTags])

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inlineForm = document.getElementById('inlineBlogForm');
            const publishBtn = document.querySelector('[data-submit-inline-blog]');
            const uploadTriggers = document.querySelectorAll('[data-open-blog-modal]');
            const visibilityButtons = document.querySelectorAll('.composer-visibility button');
            const visibilityField = document.getElementById('composerVisibility');
            const fileInput = document.getElementById('composerAttachment');
            const previewWrapper = document.getElementById('composerAttachmentPreview');
            const previewContent = document.getElementById('composerAttachmentPreviewContent');
            const removeAttachmentBtn = document.getElementById('composerAttachmentRemove');
            const modalEl = document.getElementById('playerBlogModal');
            const dropzone = modalEl?.querySelector('[data-upload-dropzone]');
            const browseButton = modalEl?.querySelector('[data-upload-browse]');
            const modalFilePreview = modalEl?.querySelector('[data-upload-file-preview]');
            const modalFileEmpty = modalEl?.querySelector('[data-upload-file-empty]');
            const modalFileName = modalEl?.querySelector('[data-upload-file-name]');
            const modalFileDetails = modalEl?.querySelector('[data-upload-file-details]');
            const modalConfirmBtn = modalEl?.querySelector('[data-upload-confirm]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
            const likeButtons = document.querySelectorAll('[data-like-button]');
            const commentToggleButtons = document.querySelectorAll('[data-comment-toggle]');
            const commentPanels = document.querySelectorAll('[data-comments-panel]');
            const shareButtons = document.querySelectorAll('[data-share-button]');

            const getModalInstance = () => {
                if (!modalEl || !window.bootstrap?.Modal) {
                    return null;
                }
                return window.bootstrap.Modal.getOrCreateInstance(modalEl);
            };

            let committedAttachmentFile = null;
            let pendingFile = null;
            let pendingFileList = null;

            const setVisibility = (value) => {
                if (!visibilityField) {
                    return;
                }
                visibilityField.value = value;
                visibilityButtons.forEach((button) => {
                    button.classList.toggle('active', button.dataset.visibility === value);
                });
            };

            visibilityButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const nextValue = button.dataset.visibility ?? '1';
                    setVisibility(nextValue);
                });
            });

            setVisibility(visibilityField?.value ?? '1');

            const canPreviewMedia = typeof window.URL !== 'undefined' && typeof URL.createObjectURL === 'function';
            let previewObjectUrl = null;
            const supportsDataTransfer = typeof DataTransfer === 'function';

            function formatFileSize(bytes) {
                if (typeof bytes !== 'number' || Number.isNaN(bytes)) {
                    return '';
                }
                const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                let value = bytes;
                let unitIndex = 0;
                while (value >= 1024 && unitIndex < units.length - 1) {
                    value /= 1024;
                    unitIndex += 1;
                }
                const display = unitIndex === 0 || value >= 10 ? Math.round(value) : Math.round(value * 10) / 10;
                return `${display} ${units[unitIndex]}`;
            }

            function describeFileType(file) {
                const type = file?.type ?? '';
                if (type.startsWith('video/')) {
                    return 'Video';
                }
                if (type.startsWith('image/')) {
                    return 'Image';
                }
                if (type.startsWith('audio/')) {
                    return 'Audio';
                }
                if (type) {
                    const parts = type.split('/');
                    return (parts.pop() || 'File').toUpperCase();
                }
                const name = file?.name ?? '';
                const ext = name.includes('.') ? name.split('.').pop() : '';
                return ext ? ext.toUpperCase() : 'File';
            }

            function updateModalFilePreview(file, enableConfirm = true) {
                if (!modalFilePreview || !modalFileEmpty) {
                    return;
                }

                if (!file) {
                    modalFilePreview.hidden = true;
                    modalFileEmpty.hidden = false;
                    if (modalConfirmBtn) {
                        modalConfirmBtn.disabled = true;
                    }
                    return;
                }

                if (modalFileName) {
                    modalFileName.textContent = file.name || 'Selected file';
                }

                if (modalFileDetails) {
                    const sizeLabel = typeof file.size === 'number' ? formatFileSize(file.size) : '';
                    const descriptor = describeFileType(file);
                    modalFileDetails.textContent = sizeLabel ? `${sizeLabel} • ${descriptor}` : descriptor;
                }

                modalFileEmpty.hidden = true;
                modalFilePreview.hidden = false;

                if (modalConfirmBtn) {
                    modalConfirmBtn.disabled = !enableConfirm;
                }
            }

            function resetPendingSelection() {
                pendingFile = null;
                pendingFileList = null;
                updateModalFilePreview(null);
            }

            function captureFileListCopy(source) {
                if (!source || !source.length) {
                    return null;
                }
                if (supportsDataTransfer) {
                    const dataTransfer = new DataTransfer();
                    Array.from(source).forEach((file) => dataTransfer.items.add(file));
                    return dataTransfer.files;
                }
                return source;
            }

            function restoreCommittedAttachment() {
                if (!fileInput) {
                    return;
                }
                if (!committedAttachmentFile) {
                    fileInput.value = '';
                    return;
                }
                if (supportsDataTransfer) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(committedAttachmentFile);
                    fileInput.files = dataTransfer.files;
                }
            }

            function buildHeaders(isJson = false) {
                const headers = { 'Accept': 'application/json' };
                if (csrfToken) {
                    headers['X-CSRF-TOKEN'] = csrfToken;
                }
                if (isJson) {
                    headers['Content-Type'] = 'application/json';
                }
                return headers;
            }

            function updateLikeButton(button, payload) {
                if (!button) {
                    return;
                }
                const liked = Boolean(payload?.liked);
                button.dataset.liked = liked ? '1' : '0';
                button.classList.toggle('is-liked', liked);
                const countNode = button.querySelector('[data-like-count]');
                if (countNode && typeof payload?.likes_count !== 'undefined') {
                    countNode.textContent = payload.likes_count;
                }
            }

            function updateCommentCount(button, count) {
                if (!button) {
                    return;
                }
                const total = typeof count === 'number' ? count : 0;
                const badge = button.querySelector('[data-comment-count]');
                if (badge) {
                    badge.textContent = total;
                }
            }

            function buildCommentElement(comment) {
                const wrapper = document.createElement('div');
                wrapper.className = 'post-comment';

                const header = document.createElement('div');
                header.className = 'post-comment-header';

                const author = document.createElement('span');
                author.textContent = comment?.name || 'Teammate';
                header.appendChild(author);

                const meta = document.createElement('span');
                meta.className = 'post-comment-meta';
                if (comment?.created_at) {
                    meta.textContent = comment.created_at;
                }
                if (comment?.created_at_exact) {
                    meta.title = comment.created_at_exact;
                }
                header.appendChild(meta);

                const body = document.createElement('p');
                body.className = 'post-comment-body';
                body.textContent = comment?.comment || '';

                wrapper.appendChild(header);
                wrapper.appendChild(body);

                return wrapper;
            }

            function renderComments(list, comments) {
                if (!list) {
                    return;
                }
                list.innerHTML = '';
                if (!Array.isArray(comments) || comments.length === 0) {
                    const empty = document.createElement('p');
                    empty.className = 'post-comments-empty';
                    empty.textContent = 'Be the first to comment.';
                    list.appendChild(empty);
                    return;
                }
                comments.forEach((comment) => {
                    list.appendChild(buildCommentElement(comment));
                });
            }

            function prependComment(list, comment) {
                if (!list) {
                    return;
                }
                if (list.firstElementChild?.classList.contains('post-comments-empty')) {
                    list.innerHTML = '';
                }
                list.prepend(buildCommentElement(comment));
            }

            async function toggleLike(button) {
                if (!button || !button.dataset.likeUrl || button.hasAttribute('data-pending')) {
                    return;
                }

                button.dataset.pending = 'true';
                button.disabled = true;

                try {
                    const response = await fetch(button.dataset.likeUrl, {
                        method: 'POST',
                        headers: buildHeaders(true),
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        const message = data?.message || 'Unable to update like right now.';
                        throw new Error(message);
                    }

                    const payload = await response.json();
                    updateLikeButton(button, payload);
                } catch (error) {
                    console.error('Like toggle failed', error);
                    alert(error.message || 'Unable to update like right now.');
                } finally {
                    button.disabled = false;
                    delete button.dataset.pending;
                }
            }

            async function loadComments(panel, commentButton) {
                if (!panel || panel.dataset.loading === 'true') {
                    return;
                }

                const url = panel.dataset.commentsUrl;
                if (!url) {
                    return;
                }

                panel.dataset.loading = 'true';
                const list = panel.querySelector('[data-comments-list]');
                if (list) {
                    list.innerHTML = '<p class="post-comments-empty">Loading comments…</p>';
                }

                try {
                    const response = await fetch(url, { headers: buildHeaders() });
                    if (!response.ok) {
                        throw new Error('Unable to load comments right now.');
                    }

                    const payload = await response.json();
                    if (list) {
                        renderComments(list, payload.comments || []);
                    }
                    if (commentButton && typeof payload.comments_count !== 'undefined') {
                        updateCommentCount(commentButton, payload.comments_count);
                    }
                    panel.dataset.loaded = 'true';
                } catch (error) {
                    console.error('Comment load failed', error);
                    if (list) {
                        list.innerHTML = '<p class="post-comments-error">Unable to load comments right now.</p>';
                    }
                } finally {
                    delete panel.dataset.loading;
                }
            }

            function toggleCommentPanel(button) {
                const blogId = button?.dataset.blogId;
                if (!blogId) {
                    return;
                }
                const panel = document.querySelector(`[data-comments-panel][data-blog-id="${blogId}"]`);
                if (!panel) {
                    return;
                }

                const willShow = panel.hasAttribute('hidden');

                document.querySelectorAll('[data-comments-panel]').forEach((candidate) => {
                    if (candidate !== panel) {
                        candidate.setAttribute('hidden', '');
                        const trigger = document.querySelector(`[data-comment-toggle][aria-controls="${candidate.id}"]`);
                        trigger?.setAttribute('aria-expanded', 'false');
                    }
                });

                if (willShow) {
                    panel.removeAttribute('hidden');
                    button.setAttribute('aria-expanded', 'true');
                    if (panel.dataset.loaded !== 'true') {
                        loadComments(panel, button);
                    }
                    panel.querySelector('[data-comment-input]')?.focus({ preventScroll: true });
                } else {
                    panel.setAttribute('hidden', '');
                    button.setAttribute('aria-expanded', 'false');
                }
            }

            async function handleCommentSubmit(event, panel) {
                event.preventDefault();
                if (!panel) {
                    return;
                }

                const form = event.currentTarget;
                const input = panel.querySelector('[data-comment-input]');
                const message = input?.value?.trim() ?? '';
                if (!message) {
                    return;
                }

                const submitButton = form.querySelector('[data-comment-submit]');
                const originalLabel = submitButton?.textContent ?? 'Post';
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Posting…';
                }

                const submitUrl = panel.dataset.commentSubmitUrl;
                if (!submitUrl) {
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.textContent = originalLabel;
                    }
                    alert('Unable to add comment right now.');
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
                    const list = panel.querySelector('[data-comments-list]');
                    prependComment(list, payload.comment);
                    panel.dataset.loaded = 'true';
                    if (input) {
                        input.value = '';
                    }

                    const blogId = panel.dataset.blogId;
                    const commentButton = document.querySelector(`[data-comment-toggle][data-blog-id="${blogId}"]`);
                    if (commentButton && typeof payload.comments_count !== 'undefined') {
                        updateCommentCount(commentButton, payload.comments_count);
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
            }

            async function sharePost(button) {
                if (!button) {
                    return;
                }

                const shareUrl = button.dataset.shareUrl;
                const shareTitle = button.dataset.shareTitle || document.title;
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
            }

            likeButtons.forEach((button) => {
                button.addEventListener('click', () => toggleLike(button));
            });

            commentToggleButtons.forEach((button) => {
                button.addEventListener('click', () => toggleCommentPanel(button));
            });

            commentPanels.forEach((panel) => {
                const form = panel.querySelector('[data-comment-form]');
                form?.addEventListener('submit', (event) => handleCommentSubmit(event, panel));
            });

            shareButtons.forEach((button) => {
                button.addEventListener('click', () => sharePost(button));
            });

            const resetPreview = () => {
                if (previewObjectUrl && canPreviewMedia) {
                    URL.revokeObjectURL(previewObjectUrl);
                    previewObjectUrl = null;
                }
                if (previewContent) {
                    previewContent.innerHTML = '';
                }
                previewWrapper?.classList.add('d-none');
            };

            const renderPreview = (file) => {
                if (!file || !previewContent) {
                    resetPreview();
                    return;
                }

                resetPreview();

                const mime = file.type || '';
                if (canPreviewMedia) {
                    previewObjectUrl = URL.createObjectURL(file);
                }

                let element;
                if (mime.startsWith('video/') && previewObjectUrl) {
                    element = document.createElement('video');
                    element.src = previewObjectUrl;
                    element.controls = true;
                    element.playsInline = true;
                } else if (mime.startsWith('image/') && previewObjectUrl) {
                    element = document.createElement('img');
                    element.src = previewObjectUrl;
                    element.alt = 'Attachment preview';
                } else {
                    element = document.createElement('p');
                    element.className = 'mb-0 small';
                    element.textContent = `Attached file: ${file.name || 'Selected file'}`;
                }

                previewContent.appendChild(element);
                previewWrapper?.classList.remove('d-none');
            };

            function setAttachment(file, originalFileList = null) {
                if (!fileInput) {
                    return;
                }

                if (!file) {
                    fileInput.value = '';
                    committedAttachmentFile = null;
                    resetPreview();
                    updateModalFilePreview(null);
                    return;
                }

                if (supportsDataTransfer) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                } else if (originalFileList) {
                    try {
                        fileInput.files = originalFileList;
                    } catch (error) {
                        console.warn('Unable to assign files to input', error);
                    }
                }

                committedAttachmentFile = file;
                renderPreview(file);
            }

            removeAttachmentBtn?.addEventListener('click', () => {
                setAttachment(null);
                resetPendingSelection();
            });

            fileInput?.addEventListener('change', () => {
                if (!fileInput.files || fileInput.files.length === 0) {
                    if (!pendingFile) {
                        updateModalFilePreview(null);
                    }
                    return;
                }

                const selectedFile = fileInput.files[0];
                pendingFile = selectedFile;
                pendingFileList = captureFileListCopy(fileInput.files);
                updateModalFilePreview(selectedFile);

                if (supportsDataTransfer && fileInput.value) {
                    fileInput.value = '';
                    restoreCommittedAttachment();
                }
            });

            uploadTriggers.forEach((button) => {
                button.addEventListener('click', () => {
                    const instance = getModalInstance();
                    instance?.show();
                });
            });

            const handleBrowse = (event) => {
                event.preventDefault();
                fileInput?.click();
            };

            browseButton?.addEventListener('click', handleBrowse);

            if (dropzone) {
                const highlight = (state) => dropzone.classList.toggle('is-dragover', state);

                ['dragenter', 'dragover'].forEach((eventName) => {
                    dropzone.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        highlight(true);
                    });
                });

                ['dragleave', 'drop'].forEach((eventName) => {
                    dropzone.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        event.stopPropagation();
                        if (eventName === 'drop' && event.dataTransfer?.files?.length) {
                            const fileList = event.dataTransfer.files;
                            const file = fileList[0];
                            pendingFile = file;
                            pendingFileList = captureFileListCopy(fileList);
                            updateModalFilePreview(file);
                        }
                        highlight(false);
                    });
                });

                dropzone.addEventListener('click', () => fileInput?.click());

                dropzone.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        fileInput?.click();
                    }
                });
            }

            modalConfirmBtn?.addEventListener('click', () => {
                if (!pendingFile) {
                    return;
                }
                setAttachment(pendingFile, pendingFileList);
                pendingFile = null;
                pendingFileList = null;
                getModalInstance()?.hide();
            });

            modalEl?.addEventListener('show.bs.modal', () => {
                if (pendingFile) {
                    updateModalFilePreview(pendingFile);
                    return;
                }
                if (committedAttachmentFile) {
                    updateModalFilePreview(committedAttachmentFile, false);
                } else {
                    updateModalFilePreview(null);
                }
            });

            modalEl?.addEventListener('hidden.bs.modal', () => {
                dropzone?.classList.remove('is-dragover');
                resetPendingSelection();
                restoreCommittedAttachment();
            });

            const setPublishing = (state) => {
                if (!publishBtn) {
                    return;
                }
                publishBtn.disabled = state;
                publishBtn.textContent = state ? 'Publishing…' : 'Publish';
            };

            const resetComposer = () => {
                inlineForm?.reset();
                resetPreview();
                setVisibility('1');
                committedAttachmentFile = null;
                resetPendingSelection();
                restoreCommittedAttachment();
            };

            publishBtn?.addEventListener('click', async () => {
                if (!inlineForm) {
                    return;
                }

                if (!inlineForm.reportValidity()) {
                    return;
                }

                setPublishing(true);

                const formData = new FormData(inlineForm);

                try {
                    const response = await fetch(@json(route('player.blogs.save')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({ message: 'Unable to save post.' }));
                        const firstError = data?.errors ? Object.values(data.errors).flat()[0] : null;
                        throw new Error(firstError || data.message || 'Unable to save post.');
                    }

                    const payload = await response.json();
                    resetComposer();
                    alert(payload.message || 'Post published successfully!');
                    window.location.reload();
                } catch (error) {
                    console.error('Blog creation failed', error);
                    alert(error.message || 'Unable to save post.');
                } finally {
                    setPublishing(false);
                }
            });
        });
    </script>
@endpush

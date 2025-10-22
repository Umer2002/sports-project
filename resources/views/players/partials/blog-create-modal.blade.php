@php
    $attachMediaDefaultTags = $defaultTags ?? ['#training', '#u15'];
@endphp

<div class="modal fade attach-media-modal" id="playerBlogModal" tabindex="-1" aria-labelledby="playerBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content attach-media-card">
            <button type="button" class="btn-close attach-media-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="attach-media-body">
                <header class="attach-media-header">
                    <h2 id="playerBlogModalLabel">Upload Media</h2>
                    <p>Add photos or videos to your post.</p>
                </header>

                <section class="attach-media-dropzone" data-upload-dropzone tabindex="0" role="button" aria-label="Drag and drop files here or click to browse">
                    <div class="attach-media-drop-inner">
                        <span class="attach-media-dropheadline">Drag &amp; Drop files here</span>
                        <span class="attach-media-droptext">or <button type="button" data-upload-browse>click to browse</button></span>
                    </div>
                </section>

                <div class="attach-media-tags">
                    <span class="attach-media-label">Tags</span>
                    <div class="attach-media-taglist">
                        @foreach ($attachMediaDefaultTags as $tag)
                            <span class="attach-media-tag">
                                <span>{{ $tag }}</span>
                                <span class="attach-media-tag-remove" aria-hidden="true">&times;</span>
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="attach-media-file-wrapper">
                    <div class="attach-media-file attach-media-file-empty" data-upload-file-empty>
                        <span>No file selected yet.</span>
                    </div>
                    <div class="attach-media-file" data-upload-file-preview hidden>
                        <div class="attach-media-file-icon" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 3a2 2 0 00-2 2v14c0 1.1.9 2 2 2h10a2 2 0 002-2V8.83a2 2 0 00-.59-1.41l-3.83-3.83A2 2 0 0013.17 3H7z" fill="#dbeafe"/>
                                <path d="M15 3.5v3a1.5 1.5 0 001.5 1.5h3" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M9 14h6" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M9 17h4" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="attach-media-file-meta">
                            <strong data-upload-file-name>example_video.mp4</strong>
                            <span data-upload-file-details>120&nbsp;MB • Video</span>
                        </div>
                    </div>
                </div>

                <div class="attach-media-footer">
                    <button type="button" class="attach-media-btn attach-media-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="attach-media-btn attach-media-btn-primary" data-upload-confirm disabled>Upload</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .attach-media-modal .modal-dialog {
        max-width: 640px;
    }

    .attach-media-card {
        position: relative;
        border: none;
        border-radius: 28px;
        background: #111827;
        color: #f8fafc;
        padding: 36px 40px;
        box-shadow: 0 28px 70px -40px rgba(15, 23, 42, 0.85);
    }

    .attach-media-close {
        position: absolute;
        top: 18px;
        right: 18px;
        filter: invert(1);
        opacity: 0.45;
        transition: opacity 0.2s ease;
    }

    .attach-media-close:hover {
        opacity: 0.85;
    }

    .attach-media-body {
        display: grid;
        gap: 28px;
    }

    .attach-media-header h2 {
        margin: 0 0 6px;
        font-size: 1.6rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .attach-media-header p {
        margin: 0;
        color: rgba(226, 232, 240, 0.78);
        font-size: 0.95rem;
    }

    .attach-media-dropzone {
        border-radius: 26px;
        border: 2px dashed rgba(148, 163, 184, 0.55);
        background: rgba(248, 250, 252, 0.95);
        min-height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #1f2937;
    }

    .attach-media-dropzone:focus-visible {
        outline: 3px solid rgba(96, 165, 250, 0.6);
        outline-offset: 4px;
    }

    .attach-media-dropzone.is-dragover {
        border-color: rgba(37, 99, 235, 0.9);
        box-shadow: 0 26px 60px -32px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
    }

    .attach-media-drop-inner {
        display: grid;
        gap: 8px;
        padding: 32px;
    }

    .attach-media-dropheadline {
        font-size: 1.15rem;
        font-weight: 600;
    }

    .attach-media-droptext {
        font-size: 0.95rem;
        color: #475569;
    }

    .attach-media-droptext button {
        border: none;
        background: none;
        color: #2563eb;
        font-weight: 600;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    .attach-media-droptext button:hover {
        text-decoration: underline;
    }

    .attach-media-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: rgba(226, 232, 240, 0.88);
        margin-bottom: 12px;
    }

    .attach-media-taglist {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .attach-media-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid rgba(203, 213, 225, 0.85);
        border-radius: 999px;
        padding: 6px 14px;
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .attach-media-tag-remove {
        font-size: 1rem;
        line-height: 1;
        color: #94a3b8;
    }

    .attach-media-file-wrapper {
        display: grid;
        gap: 12px;
    }

    .attach-media-file {
        display: flex;
        align-items: center;
        gap: 14px;
        border-radius: 18px;
        border: 1px solid rgba(203, 213, 225, 0.6);
        background: rgba(248, 250, 252, 0.95);
        padding: 14px 18px;
        color: #111827;
    }

    .attach-media-file-empty {
        justify-content: center;
        font-size: 0.95rem;
        color: #94a3b8;
        font-weight: 500;
        border-style: dashed;
        background: rgba(15, 23, 42, 0.12);
    }

    .attach-media-file-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        background: rgba(59, 130, 246, 0.16);
        border-radius: 14px;
    }

    .attach-media-file-meta strong {
        display: block;
        font-weight: 700;
        font-size: 0.95rem;
        color: #1f2937;
    }

    .attach-media-file-meta span {
        display: block;
        font-size: 0.85rem;
        color: #475569;
    }

    .attach-media-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .attach-media-btn {
        border: none;
        border-radius: 999px;
        padding: 11px 28px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
    }

    .attach-media-btn-secondary {
        background: rgba(148, 163, 184, 0.22);
        color: rgba(226, 232, 240, 0.92);
    }

    .attach-media-btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 36px -28px rgba(148, 163, 184, 0.65);
    }

    .attach-media-btn-primary {
        background: linear-gradient(135deg, #60a5fa, #6366f1);
        color: #ffffff;
        box-shadow: 0 22px 42px -24px rgba(79, 70, 229, 0.8);
    }

    .attach-media-btn-primary:hover {
        transform: translateY(-1px);
    }

    .attach-media-btn-primary:disabled {
        opacity: 0.45;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    html[data-theme='dark'] .attach-media-card,
    [data-bs-theme='dark'] .attach-media-card {
        background: #0b1220;
        box-shadow: 0 32px 70px -40px rgba(15, 23, 42, 0.9);
    }

    html[data-theme='dark'] .attach-media-dropzone,
    [data-bs-theme='dark'] .attach-media-dropzone {
        background: rgba(17, 24, 39, 0.72);
        color: rgba(226, 232, 240, 0.85);
        border-color: rgba(96, 165, 250, 0.35);
    }

    html[data-theme='dark'] .attach-media-droptext,
    [data-bs-theme='dark'] .attach-media-droptext {
        color: rgba(148, 163, 184, 0.85);
    }

    html[data-theme='dark'] .attach-media-file,
    [data-bs-theme='dark'] .attach-media-file {
        background: rgba(17, 24, 39, 0.82);
        color: rgba(226, 232, 240, 0.95);
        border-color: rgba(59, 130, 246, 0.2);
    }

    html[data-theme='dark'] .attach-media-file-meta strong,
    [data-bs-theme='dark'] .attach-media-file-meta strong {
        color: rgba(226, 232, 240, 0.95);
    }

    html[data-theme='dark'] .attach-media-file-meta span,
    [data-bs-theme='dark'] .attach-media-file-meta span {
        color: rgba(148, 163, 184, 0.85);
    }

    @media (max-width: 640px) {
        .attach-media-card {
            padding: 28px 22px;
        }

        .attach-media-dropzone {
            min-height: 200px;
        }

        .attach-media-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .attach-media-btn {
            width: 100%;
        }
    }
</style>

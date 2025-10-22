@php
    $composerId = $composerId ?? ('video-composer-' . uniqid());
    $displayName = auth()->user()->name ?? 'You';
    $allowLive = $allowLive ?? false;
    $uploadAction = $uploadAction ?? route('player.videos.upload');
@endphp
<div class="same-card p-3 video-composer" data-video-composer="{{ $composerId }}" >
    <form action="{{ $uploadAction }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column gap-3" data-composer-form>
        @csrf
        <input type="hidden" name="title" data-composer-title>
        <input type="hidden" name="category" data-composer-category>
        <input type="file" name="video" accept="video/*" class="d-none" data-composer-file>

        <div class="d-flex gap-3">
            <div class="flex-shrink-0">
                <div class="avatar rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-weight: 600;">
                    {{ strtoupper(substr($displayName, 0, 1)) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <textarea class="form-control border-0 shadow-sm" name="description" rows="3" placeholder="Share a highlight, drill, or update with your team..." data-composer-caption></textarea>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button type="button" class="btn btn-light border" data-composer-pick>
                    <i class="fa fa-video me-2 text-primary"></i> Add Video
                </button>
                @if($allowLive)
                    <button type="button" class="btn btn-outline-danger" data-composer-go-live>
                        <i class="fa fa-wifi me-2"></i> Go Live
                    </button>
                @endif
                <div class="small text-muted" data-selected-file>No video selected</div>
                <div class="badge bg-light text-dark" data-selected-type hidden></div>
            </div>
            <button type="submit" class="btn btn-primary" data-composer-submit>Share</button>
        </div>
    </form>

    <div class="modal fade" id="{{ $composerId }}-type" tabindex="-1" aria-hidden="true" data-composer-modal>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose a video type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        <label class="list-group-item d-flex align-items-start gap-3">
                            <input class="form-check-input mt-1" type="radio" name="video_type_option_{{ $composerId }}" value="challenge" data-option-label="Challenge" required>
                            <div>
                                <div class="fw-semibold">Challenge</div>
                                <div class="small text-muted">Issue a challenge to teammates or the community.</div>
                            </div>
                        </label>
                        <label class="list-group-item d-flex align-items-start gap-3">
                            <input class="form-check-input mt-1" type="radio" name="video_type_option_{{ $composerId }}" value="skill" data-option-label="Skill">
                            <div>
                                <div class="fw-semibold">Skill</div>
                                <div class="small text-muted">Showcase drills, footwork, or technique tips.</div>
                            </div>
                        </label>
                        @if($allowLive)
                            <label class="list-group-item d-flex align-items-start gap-3">
                                <input class="form-check-input mt-1" type="radio" name="video_type_option_{{ $composerId }}" value="live" data-option-label="Go Live">
                                <div>
                                    <div class="fw-semibold text-danger">Go Live (Coach)</div>
                                    <div class="small text-muted">Host a live session to connect with your athletes in real-time.</div>
                                </div>
                            </label>
                        @endif
                    </div>
                    <div class="text-danger small mt-2 d-none" data-option-error>Please choose a video type to continue.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" data-confirm-type>Continue</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="{{ $composerId }}-live" tabindex="-1" aria-hidden="true" data-live-modal>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Go Live Capture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ratio ratio-16x9 mb-3 bg-dark rounded" style="overflow:hidden;">
                        <video data-live-preview autoplay muted playsinline style="width:100%;height:100%;object-fit:cover;"></video>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div>
                            <span class="badge bg-secondary" data-live-status>Camera idle</span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary" data-live-start>Start Camera</button>
                            <button type="button" class="btn btn-outline-danger" data-live-stop disabled>Stop Recording</button>
                            <button type="button" class="btn btn-primary" data-live-use disabled>Use Recording</button>
                        </div>
                    </div>
                    <div class="small text-muted mt-3">
                        When you stop recording, the captured video will attach automatically and be ready to share.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

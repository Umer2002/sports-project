@push('styles')
<style>
    .help-chat-modal .chat-input-box {
        position: relative;
    }
    .help-chat-attachment-inline {
        margin-top: 12px;
        padding: 10px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .help-chat-attachment-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .help-chat-thumb {
        position: relative;
        width: 64px;
        height: 64px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.18);
        flex: 0 0 auto;
    }
    .help-chat-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .help-chat-thumb button {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: none;
        background: rgba(10, 12, 28, 0.85);
        color: #fff;
        font-size: 0.7rem;
        line-height: 1;
        cursor: pointer;
    }
    .help-chat-modal .chat-left {
        position: relative;
    }
    .upload-box.is-dragover {
        border: 2px solid #38bdf8;
        background: rgba(56, 189, 248, 0.08);
    }
    .help-chat-wizard {
        position: absolute;
        inset: 0;
        background: rgba(6, 10, 25, 0.92);
        backdrop-filter: blur(4px);
        z-index: 40;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }
    .help-chat-wizard.hidden {
        display: none;
    }
    .help-chat-wizard .wizard-card {
        width: 100%;
        max-width: 480px;
        background: rgba(17, 20, 39, 0.96);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 28px 26px;
        box-shadow: 0 24px 48px rgba(5, 8, 24, 0.45);
        color: #f1f5ff;
    }
    .help-chat-wizard .wizard-title {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 6px;
    }
    .help-chat-wizard .wizard-lead {
        color: rgba(241, 245, 255, 0.82);
        margin-bottom: 18px;
        line-height: 1.45;
    }
    .help-chat-wizard h5 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .help-chat-wizard p {
        margin-bottom: 18px;
        color: rgba(241, 245, 255, 0.78);
    }
    .help-chat-wizard .wizard-progress {
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(241, 245, 255, 0.55);
        margin-bottom: 12px;
    }
    .help-chat-wizard .wizard-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 18px;
    }
    .help-chat-wizard .wizard-options .wizard-option {
        width: 100%;
    }
    .help-chat-wizard .wizard-option {
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.04);
        border-radius: 12px;
        padding: 12px 16px;
        text-align: left;
        color: #f8fafc;
        font-weight: 600;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        cursor: pointer;
        transition: border 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }
    .help-chat-wizard .wizard-option__number {
        font-weight: 700;
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.18);
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .help-chat-wizard .wizard-option__text {
        display: flex;
        flex-direction: column;
        gap: 4px;
        line-height: 1.35;
    }
    .help-chat-wizard .wizard-option__hint {
        font-weight: 500;
        color: rgba(241, 245, 255, 0.65);
        font-size: 0.85rem;
    }
    .help-chat-wizard .wizard-option:hover,
    .help-chat-wizard .wizard-option:focus {
        border-color: #38bdf8;
        background: rgba(56, 189, 248, 0.12);
        transform: translateY(-1px);
    }
    .help-chat-wizard .wizard-option.is-selected {
        border-color: #38bdf8;
        background: rgba(56, 189, 248, 0.16);
        box-shadow: inset 0 0 0 1px rgba(56, 189, 248, 0.25);
    }
    .help-chat-wizard .wizard-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        gap: 12px;
    }
    .help-chat-wizard .wizard-back {
        border: none;
        background: transparent;
        color: rgba(241, 245, 255, 0.7);
        font-size: 0.9rem;
    }
    .help-chat-wizard .wizard-back:hover {
        color: #38bdf8;
    }
    .help-chat-wizard .wizard-start {
        padding: 12px 18px;
        border-radius: 999px;
        border: none;
        background: linear-gradient(135deg, #38bdf8, #6366f1);
        color: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s ease;
    }
    .help-chat-wizard .wizard-start:hover {
        transform: translateY(-1px);
    }
    .help-chat-wizard .wizard-summary {
        background: rgba(8, 11, 28, 0.65);
        border: 1px solid rgba(56, 189, 248, 0.15);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 18px;
    }
    .help-chat-wizard .wizard-summary-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        margin-bottom: 8px;
        color: rgba(241, 245, 255, 0.8);
    }
    .help-chat-wizard .wizard-summary-item span {
        font-weight: 600;
        color: #f8fafc;
    }
    .help-chat-wizard .wizard-section-title {
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(241, 245, 255, 0.6);
        margin-top: 18px;
        margin-bottom: 6px;
    }
    .help-chat-wizard .wizard-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 18px;
    }
    .help-chat-wizard .wizard-list li + li {
        margin-top: 10px;
    }
    .help-chat-wizard .wizard-list__label {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-weight: 600;
        color: rgba(241, 245, 255, 0.86);
        margin-bottom: 6px;
    }
    .help-chat-wizard .wizard-list__label span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.2);
        color: #38bdf8;
        font-weight: 700;
    }
    .help-chat-wizard .wizard-section {
        padding: 14px 16px;
        border-radius: 12px;
        background: rgba(10, 14, 32, 0.64);
        border: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 14px;
    }
    .help-chat-wizard .wizard-section:first-of-type .wizard-section-title {
        margin-top: 0;
    }
    .help-chat-wizard .wizard-textarea {
        width: 100%;
        min-height: 96px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.06);
        color: #f8fafc;
        padding: 12px 14px;
        font-size: 0.95rem;
        resize: vertical;
    }
    .help-chat-wizard .wizard-textarea:focus {
        outline: none;
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.35);
    }
    .help-chat-wizard .wizard-stage-note {
        font-size: 0.82rem;
        color: rgba(241, 245, 255, 0.64);
        margin-bottom: 14px;
    }
    .help-chat-wizard .wizard-next {
        border: none;
        padding: 10px 18px;
        border-radius: 999px;
        background: linear-gradient(135deg, #38bdf8, #6366f1);
        color: #fff;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform 0.2s ease;
    }
    .help-chat-wizard .wizard-next:hover {
        transform: translateY(-1px);
    }
    .help-chat-wizard .wizard-skip {
        border: none;
        background: transparent;
        color: rgba(241, 245, 255, 0.75);
        font-weight: 600;
    }
    .help-chat-wizard .wizard-skip:hover {
        color: #38bdf8;
    }
    .help-chat-emoji-panel {
        position: absolute;
        right: 0;
        bottom: 70px;
        z-index: 25;
        background: #111427;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 10px;
        min-width: 260px;
        box-shadow: 0 12px 32px rgba(7, 10, 26, 0.45);
    }
    .help-chat-emoji-panel h6 {
        font-size: 0.82rem;
        margin-bottom: 8px;
        color: #f0f3ff;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .help-chat-emoji-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }
    .help-chat-emoji {
        background: rgba(255, 255, 255, 0.08);
        border: none;
        border-radius: 8px;
        font-size: 1.2rem;
        line-height: 1;
        padding: 6px 0;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .help-chat-emoji:hover {
        background: rgba(255, 255, 255, 0.18);
    }
    .help-chat-feedback {
        margin-top: 8px;
        font-size: 0.82rem;
    }
</style>
@endpush

<div class="modal fade help-chat-modal" id="playerHelpChatModal" tabindex="-1" aria-labelledby="playerHelpChatLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-one">
            <div class="modal-header modal-header-one">
                <div class="modal-headding modal-headding-two">
                    <div class="leftmodal-header">
                        <div class="chat-box-icon">
                            <img src="{{ asset('assets/player-dashboard/images/chat-icon.svg') }}" alt="Chat icon">
                        </div>
                        <div class="modal-text-headding">
                            <h1 class="modal-title fs-5" id="playerHelpChatLabel">Live Help Chat</h1>
                            <p>CONNECTED TO SUPPORT</p>
                        </div>
                    </div>
                    <div class="leftmodal-header">
                        <button type="button" class="chat-box-icon start-audio-chat" id="playerHelpChatAudio">
                            <i class="fa-solid fa-microphone"></i>
                            <span>Start Audio Chat</span>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="chat-container">
                    <div class="chat-left panel-box panel-box-a chat-left-card">
                        <div class="help-chat-wizard" id="playerHelpChatWizard" role="dialog" aria-modal="true">
                            <div class="wizard-card" role="document">
                                <h4 class="wizard-title">Play2Earn Smart Assistant</h4>
                                <p class="wizard-lead">Tell me who you are and what you need today so I can point you to the right dashboard tools before we start chatting.</p>
                                <div class="wizard-progress" data-wizard-progress>Quick setup · Step <span id="playerHelpChatWizardStep">1</span> of 4</div>

                                <div class="wizard-step" data-wizard-step="1">
                                    <h5>Who are you using Play2Earn as?</h5>
                                    <p>Choose the option that best matches the work you are doing.</p>
                                    <div class="wizard-options">
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="player" data-wizard-label="Player">
                                            <span class="wizard-option__number">1</span>
                                            <span class="wizard-option__text">
                                                <span>Player</span>
                                                <span class="wizard-option__hint">Manage your profile, videos, rewards, and clubs.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="club" data-wizard-label="Club / Academy">
                                            <span class="wizard-option__number">2</span>
                                            <span class="wizard-option__text">
                                                <span>Club / Academy</span>
                                                <span class="wizard-option__hint">Run teams, tournaments, rosters, and staff.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="coach" data-wizard-label="Referee / Coach / Official">
                                            <span class="wizard-option__number">3</span>
                                            <span class="wizard-option__text">
                                                <span>Referee / Coach / Official</span>
                                                <span class="wizard-option__hint">Handle assignments, certifications, and match reports.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="organizer" data-wizard-label="Tournament Organizer">
                                            <span class="wizard-option__number">4</span>
                                            <span class="wizard-option__text">
                                                <span>Tournament Organizer</span>
                                                <span class="wizard-option__hint">Schedule brackets, venues, and standings.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="guest" data-wizard-label="Guest / Fan / Parent">
                                            <span class="wizard-option__number">5</span>
                                            <span class="wizard-option__text">
                                                <span>Guest / Fan / Parent</span>
                                                <span class="wizard-option__hint">Follow athletes, track rewards, and share updates.</span>
                                            </span>
                                        </button>
                                        <button type="button" class="wizard-option" data-wizard-key="role" data-wizard-value="other" data-wizard-label="Other / Not listed">
                                            <span class="wizard-option__number">6</span>
                                            <span class="wizard-option__text">
                                                <span>Other / Not listed</span>
                                                <span class="wizard-option__hint">Let me know your situation and I’ll tailor guidance.</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>

                                <div class="wizard-step d-none" data-wizard-step="2">
                                    <h5>What do you want help with today?</h5>
                                    <p>Pick the topic that matches your request. You can fine-tune it in the chat afterward.</p>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Player support</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="player_profile" data-wizard-label="Registering or completing my profile" data-wizard-group="player">Registering or completing my profile</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="player_videos" data-wizard-label="Uploading training or competition videos" data-wizard-group="player">Uploading training or competition videos</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="player_join" data-wizard-label="Joining a club or tournament" data-wizard-group="player">Joining a club or tournament</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="player_rewards" data-wizard-label="Tracking rewards or rankings" data-wizard-group="player">Tracking rewards or rankings</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Club / academy help</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="club_tournaments" data-wizard-label="Creating or managing tournaments" data-wizard-group="club">Creating or managing tournaments</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="club_roster" data-wizard-label="Adding players, teams, or matches" data-wizard-group="club">Adding players, teams, or matches</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="club_registrations" data-wizard-label="Approving player registrations" data-wizard-group="club">Approving player registrations</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="club_staff" data-wizard-label="Managing staff or schedules" data-wizard-group="club">Managing staff or schedules</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Referee / coach support</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="coach_matches" data-wizard-label="Viewing assigned matches" data-wizard-group="coach">Viewing assigned matches</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="coach_certifications" data-wizard-label="Updating certifications or availability" data-wizard-group="coach">Updating certifications or availability</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="coach_reports" data-wizard-label="Submitting post-match reports" data-wizard-group="coach">Submitting post-match reports</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Tournament organizer support</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="organizer_schedule" data-wizard-label="Scheduling matches or brackets" data-wizard-group="organizer">Scheduling matches or brackets</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="organizer_venue" data-wizard-label="Managing venue logistics" data-wizard-group="organizer">Managing venue logistics</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="organizer_results" data-wizard-label="Publishing results or live standings" data-wizard-group="organizer">Publishing results or live standings</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Rewards & payments</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="rewards_claim" data-wizard-label="Claiming rewards or sponsorships" data-wizard-group="rewards">Claiming rewards or sponsorships</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="rewards_tokens" data-wizard-label="Understanding the token system" data-wizard-group="rewards">Understanding the Play2Earn token system</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Technical support</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="technical_login" data-wizard-label="Login or password issues" data-wizard-group="technical">Login or password issues</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="technical_upload" data-wizard-label="Upload errors" data-wizard-group="technical">Upload errors</button>
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="technical_bug" data-wizard-label="Page not loading or system bug" data-wizard-group="technical">Page not loading or system bug</button>
                                        </div>
                                    </div>

                                    <div class="wizard-section">
                                        <div class="wizard-section-title">Other requests</div>
                                        <div class="wizard-options">
                                            <button type="button" class="wizard-option" data-wizard-key="intent" data-wizard-value="general_other" data-wizard-label="Something else / general question" data-wizard-group="general">Something else / general question</button>
                                        </div>
                                    </div>

                                    <div class="wizard-actions">
                                        <button type="button" class="wizard-back" data-wizard-back>&larr; Back</button>
                                    </div>
                                </div>

                                <div class="wizard-step d-none" data-wizard-step="3">
                                    <h5>Where are you working right now?</h5>
                                    <p class="wizard-stage-note">Optional, but it makes my guidance smarter. Example: "Club Dashboard &rarr; Tournaments &rarr; Create Tournament"</p>
                                    <textarea class="wizard-textarea" data-wizard-stage-input maxlength="240" placeholder="Add the screen or workflow you're on (or leave blank if you're not sure)"></textarea>
                                    <div class="wizard-actions">
                                        <button type="button" class="wizard-back" data-wizard-back>&larr; Back</button>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="wizard-skip" data-wizard-skip>Skip for now</button>
                                            <button type="button" class="wizard-next" data-wizard-next>Continue</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="wizard-step d-none" data-wizard-step="4">
                                    <h5>Great, let's get started</h5>
                                    <p>I'll use these answers to tailor the assistant for you.</p>
                                    <div class="wizard-summary">
                                        <div class="wizard-summary-item">
                                            <span>Role</span>
                                            <strong data-summary="role">—</strong>
                                        </div>
                                        <div class="wizard-summary-item">
                                            <span>Focus area</span>
                                            <strong data-summary="intent">—</strong>
                                        </div>
                                        <div class="wizard-summary-item">
                                            <span>Current screen</span>
                                            <strong data-summary="stage">Not specified yet</strong>
                                        </div>
                                    </div>
                                    <div class="wizard-actions">
                                        <button type="button" class="wizard-back" data-wizard-back>&larr; Back</button>
                                        <button type="button" class="wizard-start" data-wizard-start>
                                            Start Help Chat
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-body d-flex flex-column pt-0" id="playerHelpChatMessages" aria-live="polite" aria-busy="false">
                            <!-- Messages injected via JS -->
                        </div>
                    </div>

                    <div class="chat-right panel-box panel-box-a chat-right-card">
                        <div class="right-section">
                            <h6>SESSION</h6>
                            <div class="agent-text">Agent: Play2Earn Support • Online</div>
                            <div class="agent-text">Virtual Assistant powered by ChatGPT</div>
                        </div>

                        <div class="right-section">
                            <h6>QUICK ACTIONS</h6>
                            <button type="button" class="quick-btn quick-audio" data-help-prompt="Can we start an audio chat to troubleshoot?">Start Audio Chat</button>
                            <button type="button" class="quick-btn quick-screen" data-help-prompt="How do I share my screen with support?">Share Screen</button>
                            <button type="button" class="quick-btn" data-help-prompt="Can you walk me through troubleshooting the team chat?">Troubleshoot Team Chat</button>
                        </div>

                        <div class="right-section" id="playerHelpChatSuggestionsSection">
                            <h6>SUGGESTED LINKS</h6>
                            <div id="playerHelpChatSuggestions" class="d-grid gap-2"></div>
                            <p class="text-muted small mb-0 d-none" id="playerHelpChatSuggestionsEmpty">No quick links yet—ask anything to get tailored shortcuts.</p>
                        </div>

                        <div class="right-section">
                            <h6>ATTACHMENTS</h6>
                            <div class="upload-box" id="playerHelpChatDropzone">
                                <span>Drag &amp; drop or click to upload</span>
                                <button type="button" class="btn-upload">Upload</button>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="playerHelpChatDiagnostics">
                                <label class="form-check-label" for="playerHelpChatDiagnostics">
                                    Include diagnostics with this chat
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chat-footer">
                    <div class="chat-input-box">
                        <div class="write-message-main">
                            <div class="write-message-input">
                                <input type="text" id="playerHelpChatInput" placeholder="Write a message..." autocomplete="off">
                            </div>
                            <div class="camera-buttons">
                                <button type="button" class="help-chat-action" id="playerHelpChatAttach" aria-label="Attach a file">
                                    <img src="{{ asset('assets/player-dashboard/images/pin-icon.svg') }}" alt="Attach">
                                </button>
                                <button type="button" class="help-chat-action" id="playerHelpChatCamera" aria-label="Capture an image">
                                    <img src="{{ asset('assets/player-dashboard/images/camera.svg') }}" alt="Camera">
                                </button>
                                <input type="file" id="playerHelpChatFileInput" class="visually-hidden" accept="image/*" multiple>
                            </div>
                        </div>
                        <div class="help-chat-attachment-inline d-none" id="playerHelpChatAttachmentRow">
                            <div class="help-chat-attachment-list" id="playerHelpChatAttachmentPreview"></div>
                        </div>
                        <p class="help-chat-feedback visually-hidden" id="playerHelpChatFeedback" aria-live="polite"></p>
                        <div id="playerHelpChatEmojiPanel" class="help-chat-emoji-panel d-none" role="dialog" aria-label="Emoji picker">
                            <h6>Quick Emoji</h6>
                            <div class="help-chat-emoji-grid" id="playerHelpChatEmojiGrid"></div>
                        </div>
                        <div class="audio-three-btn">
                            <button type="button" class="share-btn" id="playerHelpChatSend" aria-label="Send message">
                                <img src="{{ asset('assets/player-dashboard/images/share-icon.svg') }}" alt="Send" class="send-icon">
                                <span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="share-btn audio-btn" id="playerHelpChatVoice" aria-label="Send an audio note">
                                <img src="{{ asset('assets/player-dashboard/images/audio-icon.svg') }}" alt="Audio">
                            </button>
                            <button type="button" class="share-btn emoji-btn" id="playerHelpChatEmoji" aria-label="Insert emoji">
                                <img src="{{ asset('assets/player-dashboard/images/emoji-icon.svg') }}" alt="Emoji">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="playerHelpChatTemplateAgent">
        <div class="msg-two">
            <div class="user-name">
                <h2>AG</h2>
            </div>
            <div class="msg-rignt">
                <div class="msg msg-agent mb-0">
                    <p></p>
                </div>
                <div class="msg-time">09:18</div>
            </div>
        </div>
    </template>

    <template id="playerHelpChatTemplateUser">
        <div class="joining-team-text">
            <div class="msg text-msg-two msg-user"></div>
            <div class="msg-time text-end">09:20</div>
        </div>
    </template>

    <template id="playerHelpChatTemplateTyping">
        <div class="msg-two typing-indicator">
            <div class="user-name">
                <h2>AG</h2>
            </div>
            <div class="msg-rignt">
                <div class="msg msg-agent typing-dots"><span></span><span></span><span></span></div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('playerHelpChatModal');
    if (!modal || modal.dataset.initialized === '1') {
        return;
    }

    modal.dataset.initialized = '1';

    const messagesEl = modal.querySelector('#playerHelpChatMessages');
    const inputEl = modal.querySelector('#playerHelpChatInput');
    const sendBtn = modal.querySelector('#playerHelpChatSend');
    const sendIcon = sendBtn ? sendBtn.querySelector('.send-icon') : null;
    const sendSpinner = sendBtn ? sendBtn.querySelector('.spinner-border') : null;
    const diagnosticsCheckbox = modal.querySelector('#playerHelpChatDiagnostics');
    const attachBtn = modal.querySelector('#playerHelpChatAttach');
    const cameraBtn = modal.querySelector('#playerHelpChatCamera');
    const attachmentInput = modal.querySelector('#playerHelpChatFileInput');
    const attachmentRow = modal.querySelector('#playerHelpChatAttachmentRow');
    const attachmentPreview = modal.querySelector('#playerHelpChatAttachmentPreview');
    const emojiBtn = modal.querySelector('#playerHelpChatEmoji');
    const emojiPanel = modal.querySelector('#playerHelpChatEmojiPanel');
    const feedbackEl = modal.querySelector('#playerHelpChatFeedback');
    const emojiGrid = modal.querySelector('#playerHelpChatEmojiGrid');
    const dropzone = modal.querySelector('#playerHelpChatDropzone');
    const uploadBtn = dropzone ? dropzone.querySelector('.btn-upload') : null;
    const suggestionsList = modal.querySelector('#playerHelpChatSuggestions');
    const suggestionsEmpty = modal.querySelector('#playerHelpChatSuggestionsEmpty');
    const wizard = modal.querySelector('#playerHelpChatWizard');
    const wizardSteps = wizard ? Array.from(wizard.querySelectorAll('[data-wizard-step]')) : [];
    const wizardStepIndicator = wizard ? wizard.querySelector('#playerHelpChatWizardStep') : null;
    const wizardStageInput = wizard ? wizard.querySelector('[data-wizard-stage-input]') : null;
    const wizardStepTotal = wizardSteps.length || 4;
    const wizardSummaryEls = wizard ? {
        role: wizard.querySelector('[data-summary="role"]'),
        intent: wizard.querySelector('[data-summary="intent"]'),
        stage: wizard.querySelector('[data-summary="stage"]'),
    } : {};
    const templates = {
        agent: modal.querySelector('#playerHelpChatTemplateAgent'),
        user: modal.querySelector('#playerHelpChatTemplateUser'),
        typing: modal.querySelector('#playerHelpChatTemplateTyping')
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const sendUrl = @json(route('player.help-chat.send'));
    @php
        $resolveRoute = function (?string $routeName, ?string $fallback = null) {
            if (! $routeName) {
                return $fallback;
            }

            try {
                return route($routeName);
            } catch (\Throwable $exception) {
                return $fallback;
            }
        };

        $helpChatDefaultSuggestions = array_values(array_filter([
            ['label' => 'Player Dashboard', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ['label' => 'Browse Clubs', 'url' => $resolveRoute('clubs.search', url('/clubs'))],
            ['label' => 'Find Tournaments', 'url' => $resolveRoute('tournaments.search', url('/tournaments'))],
            ['label' => 'Edit Availability', 'url' => $resolveRoute('player.availability.index', url('/player/availability'))],
            ['label' => 'Invite a Club', 'url' => $resolveRoute('player.invite.club.create', url('/player/invite-club'))],
        ], fn ($item) => ! empty($item['url'])));

        $wizardIntentSuggestions = [
            'player_profile' => [
                ['label' => 'Open My Account wizard', 'url' => $resolveRoute('player.profile', url('/player/profile'))],
                ['label' => 'Review registration checklist', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ],
            'player_videos' => [
                ['label' => 'Go to Videos workspace', 'url' => $resolveRoute('player.videos.index', url('/player/videos'))],
                ['label' => 'Explore community videos', 'url' => $resolveRoute('player.videos.explore', url('/player/videos/explore'))],
            ],
            'player_join' => [
                ['label' => 'Browse clubs', 'url' => $resolveRoute('clubs.search', url('/clubs'))],
                ['label' => 'Find tournaments to join', 'url' => $resolveRoute('tournaments.search', url('/tournaments'))],
            ],
            'player_rewards' => [
                ['label' => 'Track rewards & payouts', 'url' => $resolveRoute('player.payouts', url('/player/payouts'))],
                ['label' => 'View player dashboard insights', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ],
            'club_tournaments' => [
                ['label' => 'Open club dashboard', 'url' => $resolveRoute('club-dashboard', url('/club-dashboard'))],
                ['label' => 'Launch team wizard', 'url' => $resolveRoute('club.teams.wizard.step1', url('/club/teams/wizard/step1'))],
            ],
            'club_roster' => [
                ['label' => 'Manage club teams', 'url' => $resolveRoute('club-dashboard', url('/club-dashboard'))],
                ['label' => 'Add players to a roster', 'url' => url('/club/teams')],
            ],
            'club_registrations' => [
                ['label' => 'Review player registrations', 'url' => $resolveRoute('club-dashboard', url('/club-dashboard'))],
            ],
            'club_staff' => [
                ['label' => 'Manage staff & schedules', 'url' => $resolveRoute('club-dashboard', url('/club-dashboard'))],
            ],
            'coach_matches' => [
                ['label' => 'Coach dashboard', 'url' => $resolveRoute('coach.dashboard', url('/coach/dashboard'))],
            ],
            'coach_certifications' => [
                ['label' => 'Update availability', 'url' => $resolveRoute('coach.dashboard', url('/coach/dashboard'))],
            ],
            'coach_reports' => [
                ['label' => 'Submit match reports', 'url' => $resolveRoute('coach.dashboard', url('/coach/dashboard'))],
            ],
            'organizer_schedule' => [
                ['label' => 'Tournament scheduling tools', 'url' => $resolveRoute('tournaments.search', url('/tournaments'))],
            ],
            'organizer_venue' => [
                ['label' => 'Manage venues', 'url' => $resolveRoute('tournaments.search', url('/tournaments'))],
            ],
            'organizer_results' => [
                ['label' => 'Publish standings', 'url' => $resolveRoute('tournaments.search', url('/tournaments'))],
            ],
            'rewards_claim' => [
                ['label' => 'Claim rewards & payouts', 'url' => $resolveRoute('player.payouts', url('/player/payouts'))],
            ],
            'rewards_tokens' => [
                ['label' => 'Learn about tokens', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ],
            'technical_login' => [
                ['label' => 'Reset your password', 'url' => $resolveRoute('password.request', url('/forgot-password'))],
                ['label' => 'Return to login', 'url' => $resolveRoute('login', url('/login'))],
            ],
            'technical_upload' => [
                ['label' => 'Video upload center', 'url' => $resolveRoute('player.videos.index', url('/player/videos'))],
            ],
            'technical_bug' => [
                ['label' => 'Reload your dashboard', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ],
            'general_other' => [
                ['label' => 'Open Player Dashboard', 'url' => $resolveRoute('player.dashboard', url('/player/dashboard'))],
            ],
        ];

        $wizardIntentSuggestions = array_map(function ($items) {
            return array_values(array_filter($items, fn ($item) => ! empty($item['url'])));
        }, $wizardIntentSuggestions);
    @endphp
    const defaultSuggestions = @json($helpChatDefaultSuggestions);
    const wizardIntentSuggestions = @json($wizardIntentSuggestions);
    let wizardStepIndex = 1;
    const wizardContext = {
        role: null,
        role_label: null,
        intent: null,
        intent_label: null,
        intent_group: null,
        stage: '',
    };
    const wizardLabels = {
        role: '',
        intent: '',
        stage: 'Not specified yet',
    };
    let wizardCompleted = !wizard;
    const intentVisibilityMap = {
        player: ['player', 'rewards', 'technical', 'general'],
        club: ['club', 'player', 'rewards', 'technical', 'general'],
        coach: ['coach', 'rewards', 'technical', 'general'],
        organizer: ['organizer', 'rewards', 'technical', 'general'],
        guest: ['player', 'rewards', 'general', 'technical'],
        other: ['general', 'technical', 'rewards'],
    };

    if (!messagesEl || !inputEl || !sendBtn || !templates.agent || !templates.user) {
        console.warn('Help chat modal missing required elements.');
        return;
    }

    let conversationHistory = [];
    let attachments = [];
    const MAX_ATTACHMENTS = 5;
    const defaultGreeting = "Hey there! I'm your Play2Earn assistant. Tell me what you want to tackle on the Player Dashboard, or tap Start Audio Chat if you'd rather talk.";
    const MIN_TYPING_DELAY = 2000;
    const FEEDBACK_TIMEOUT = 4500;
    let feedbackTimer = null;
    const getTimestamp = () => {
        if (window.performance && typeof window.performance.now === 'function') {
            return window.performance.now();
        }
        return Date.now();
    };
    const waitForMinimumTyping = (startedAt) => {
        const elapsed = getTimestamp() - startedAt;
        const remaining = Math.max(MIN_TYPING_DELAY - elapsed, 0);
        if (remaining <= 0) {
            return Promise.resolve();
        }
        return new Promise((resolve) => setTimeout(resolve, remaining));
    };

    const closeEmojiPanel = () => {
        if (emojiPanel) {
            emojiPanel.classList.add('d-none');
            emojiBtn?.setAttribute('aria-expanded', 'false');
        }
    };

    const openEmojiPanel = () => {
        if (emojiPanel) {
            emojiPanel.classList.remove('d-none');
            emojiBtn?.setAttribute('aria-expanded', 'true');
        }
    };

    const toggleEmojiPopover = () => {
        if (!emojiPanel) {
            return;
        }
        const willOpen = emojiPanel.classList.contains('d-none');
        if (willOpen) {
            openEmojiPanel();
        } else {
            closeEmojiPanel();
        }
    };

    const revokeAttachmentUrls = () => {
        attachments.forEach((item) => {
            if (item.url) {
                URL.revokeObjectURL(item.url);
            }
        });
    };

    const showFeedback = (message, tone = 'info') => {
        if (!feedbackEl) {
            return;
        }

        feedbackEl.textContent = message;
        feedbackEl.classList.remove('visually-hidden', 'text-danger', 'text-success', 'text-info');

        switch (tone) {
            case 'error':
                feedbackEl.classList.add('text-danger');
                break;
            case 'success':
                feedbackEl.classList.add('text-success');
                break;
            default:
                feedbackEl.classList.add('text-info');
        }

        if (feedbackTimer) {
            clearTimeout(feedbackTimer);
        }

        feedbackTimer = setTimeout(() => {
            feedbackEl.classList.add('visually-hidden');
            feedbackEl.classList.remove('text-danger', 'text-success', 'text-info');
            feedbackEl.textContent = '';
        }, FEEDBACK_TIMEOUT);
    };

    const renderSuggestions = (items) => {
        if (!suggestionsList) {
            return;
        }

        const usable = Array.isArray(items) ? items.filter((entry) => entry && entry.label && entry.url) : [];
        const catalogue = usable.length ? usable : defaultSuggestions;

        suggestionsList.innerHTML = '';

        if (!catalogue.length) {
            suggestionsEmpty?.classList.remove('d-none');
            return;
        }

        suggestionsEmpty?.classList.add('d-none');

        catalogue.slice(0, 6).forEach((entry) => {
            const link = document.createElement('a');
            link.className = 'link-btn';
            link.href = entry.url;
            link.target = '_blank';
            link.rel = 'noopener';
            link.textContent = entry.label;
            suggestionsList.appendChild(link);
        });
    };

    const setChatInputEnabled = (enabled) => {
        if (!inputEl || !sendBtn) {
            return;
        }

        if (enabled) {
            inputEl.removeAttribute('disabled');
            sendBtn.disabled = false;
        } else {
            inputEl.setAttribute('disabled', 'disabled');
            sendBtn.disabled = true;
        }
    };

    const goToWizardStep = (step) => {
        if (!wizard || !wizardSteps.length) {
            return;
        }

        wizardStepIndex = Math.max(1, Math.min(step, wizardStepTotal));

        wizardSteps.forEach((stepEl) => {
            const isActive = Number(stepEl.dataset.wizardStep) === wizardStepIndex;
            stepEl.classList.toggle('d-none', !isActive);
        });

        if (wizardStageInput && wizardStepIndex === 3) {
            wizardStageInput.value = wizardContext.stage || '';
            setTimeout(() => wizardStageInput.focus(), 60);
        }

        if (wizardStepIndicator) {
            wizardStepIndicator.textContent = String(wizardStepIndex);
        }
    };

    const updateWizardSummary = () => {
        if (!wizard) {
            return;
        }

        if (wizardSummaryEls.role) {
            wizardSummaryEls.role.textContent = wizardLabels.role || '—';
        }
        if (wizardSummaryEls.intent) {
            wizardSummaryEls.intent.textContent = wizardLabels.intent || '—';
        }
        if (wizardSummaryEls.stage) {
            wizardSummaryEls.stage.textContent = wizardLabels.stage || 'Not specified yet';
        }
    };

    const localSuggestionsForWizard = () => {
        if (wizardContext.intent && Array.isArray(wizardIntentSuggestions[wizardContext.intent]) && wizardIntentSuggestions[wizardContext.intent].length) {
            return wizardIntentSuggestions[wizardContext.intent];
        }
        return defaultSuggestions;
    };

    const filterIntentSections = (role) => {
        if (!wizard) {
            return;
        }

        const allowedGroups = intentVisibilityMap[role] || ['player', 'club', 'coach', 'organizer', 'rewards', 'technical', 'general'];

        wizard.querySelectorAll('[data-wizard-key="intent"]').forEach((button) => {
            const group = button.getAttribute('data-wizard-group') || 'general';
            const shouldShow = allowedGroups.includes(group);
            button.classList.toggle('d-none', !shouldShow);
        });

        wizard.querySelectorAll('.wizard-section').forEach((section) => {
            const buttons = Array.from(section.querySelectorAll('[data-wizard-key="intent"]'));
            const hasVisible = buttons.some((btn) => !btn.classList.contains('d-none'));
            section.classList.toggle('d-none', !hasVisible);
        });
    };

    const buildWizardGreeting = () => {
        const roleLabel = wizardLabels.role || 'Play2Earn teammate';
        const intentLabel = wizardLabels.intent || 'Play2Earn features';
        const stageLine = wizardContext.stage
            ? `You're currently on ${wizardContext.stage}.`
            : 'Tell me where you are in the app and I will point you to the right controls.';

        return `All set! You're working as a ${roleLabel} and need help with ${intentLabel}. ${stageLine} Ask your question when you're ready.`;
    };

    const finalizeWizard = () => {
        if (!wizard) {
            return;
        }

        if (!wizardContext.role) {
            goToWizardStep(1);
            showFeedback('Select the role that best matches you.', 'error');
            return;
        }

        if (!wizardContext.intent) {
            goToWizardStep(2);
            showFeedback('Choose the feature area you need help with.', 'error');
            return;
        }

        wizardCompleted = true;
        wizard.classList.add('hidden');
        wizard.setAttribute('aria-hidden', 'true');
        setChatInputEnabled(true);
        renderSuggestions(localSuggestionsForWizard());
        hideFeedback();
        renderMessage('assistant', buildWizardGreeting(), { save: true });
        setTimeout(() => inputEl?.focus(), 120);
    };

    const resetWizard = () => {
        if (!wizard) {
            wizardCompleted = true;
            setChatInputEnabled(true);
            return;
        }

        wizardCompleted = false;
        wizard.classList.remove('hidden');
        wizard.setAttribute('aria-hidden', 'false');
        wizardContext.role = null;
        wizardContext.role_label = null;
        wizardContext.intent = null;
        wizardContext.intent_label = null;
        wizardContext.intent_group = null;
        wizardContext.stage = '';
        wizardLabels.role = '';
        wizardLabels.intent = '';
        wizardLabels.stage = 'Not specified yet';
        if (wizardStageInput) {
            wizardStageInput.value = '';
        }
        wizard.querySelectorAll('.wizard-option.is-selected').forEach((btn) => btn.classList.remove('is-selected'));
        wizard.querySelectorAll('[data-wizard-key="intent"]').forEach((btn) => btn.classList.remove('d-none'));
        wizard.querySelectorAll('.wizard-section').forEach((section) => section.classList.remove('d-none'));
        goToWizardStep(1);
        updateWizardSummary();
        setChatInputEnabled(false);
    };

    const hideFeedback = () => {
        if (!feedbackEl) {
            return;
        }

        if (feedbackTimer) {
            clearTimeout(feedbackTimer);
            feedbackTimer = null;
        }

        feedbackEl.classList.add('visually-hidden');
        feedbackEl.classList.remove('text-danger', 'text-success', 'text-info');
        feedbackEl.textContent = '';
    };

    const hasMediaDevice = async (kind) => {
        if (!navigator.mediaDevices) {
            return false;
        }

        const fallbacks = {
            videoinput: { constraints: { video: true }, track: 'video' },
            audioinput: { constraints: { audio: true }, track: 'audio' }
        };

        if (navigator.mediaDevices.enumerateDevices) {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                if (devices.some((device) => device.kind === kind)) {
                    return true;
                }
                // Some browsers return empty lists until permission is granted
                if (!devices.length && navigator.mediaDevices.getUserMedia && fallbacks[kind]) {
                    const stream = await navigator.mediaDevices.getUserMedia(fallbacks[kind].constraints);
                    stream.getTracks().forEach((track) => track.stop());
                    return true;
                }
                return false;
            } catch (error) {
                console.warn('Unable to enumerate media devices:', error);
            }
        }

        if (navigator.mediaDevices.getUserMedia && fallbacks[kind]) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia(fallbacks[kind].constraints);
                stream.getTracks().forEach((track) => track.stop());
                return true;
            } catch (error) {
                console.warn('Unable to access media device:', error);
            }
        }

        return false;
    };

    const renderAttachments = () => {
        if (!attachmentPreview) {
            return;
        }

        attachmentPreview.innerHTML = '';
        if (!attachments.length) {
            attachmentRow?.classList?.add('d-none');
            return;
        }

        attachmentRow?.classList?.remove('d-none');

        attachments.forEach((item, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'help-chat-thumb';
            wrapper.title = item.file.name;
            wrapper.style.width = '64px';
            wrapper.style.height = '64px';

            const img = document.createElement('img');
            img.src = item.url;
            img.alt = item.file.name;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            wrapper.appendChild(img);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.setAttribute('aria-label', `Remove ${item.file.name}`);
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => {
                const removed = attachments.splice(index, 1);
                if (removed[0]?.url) {
                    URL.revokeObjectURL(removed[0].url);
                }
                renderAttachments();
                inputEl.focus();
                if (attachments.length) {
                    showFeedback(`${attachments.length} image${attachments.length > 1 ? 's' : ''} ready to send.`, 'info');
                } else {
                    hideFeedback();
                }
            });

            wrapper.appendChild(removeBtn);
            attachmentPreview.appendChild(wrapper);
        });
    };

    const clearAttachments = () => {
        revokeAttachmentUrls();
        attachments = [];
        if (attachmentPreview) {
            attachmentPreview.innerHTML = '';
        }
        attachmentRow?.classList?.add('d-none');
    };

    const insertEmoji = (emoji) => {
        if (!inputEl) {
            return;
        }
        const start = inputEl.selectionStart ?? inputEl.value.length;
        const end = inputEl.selectionEnd ?? inputEl.value.length;
        const text = inputEl.value;
        inputEl.value = `${text.slice(0, start)}${emoji}${text.slice(end)}`;
        const cursor = start + emoji.length;
        inputEl.focus();
        inputEl.setSelectionRange(cursor, cursor);
    };

    const emojiList = ['😀', '😁', '😂', '🤣', '😅', '😊', '😍', '🤔', '👍', '🙌', '🔥', '⚽', '🏀', '🏈', '⚾', '🥇', '💪', '📸', '📎', '✅', '❓', '🚀', '🙏', '📅', '🎯', '🏆', '🛠️', '💡', '🤖', '💬'];

    const populateEmojiGrid = () => {
        if (!emojiGrid) {
            return;
        }
        emojiGrid.innerHTML = '';
        emojiList.forEach((emoji) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'help-chat-emoji';
            button.textContent = emoji;
            button.addEventListener('click', (event) => {
                event.preventDefault();
                insertEmoji(emoji);
                closeEmojiPanel();
            });
            emojiGrid.appendChild(button);
        });
    };

    const formatTime = (date = new Date()) => {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    const scrollToBottom = () => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    };

    const addDayChipIfNeeded = () => {
        if (!messagesEl.querySelector('.chat-day')) {
            const chip = document.createElement('div');
            chip.className = 'chat-day';
            const label = document.createElement('span');
            label.textContent = 'TODAY';
            chip.appendChild(label);
            messagesEl.appendChild(chip);
        }
    };

    const clearMessages = () => {
        messagesEl.innerHTML = '';
        addDayChipIfNeeded();
    };

    const renderMessage = (role, content, options = {}) => {
        const { time = formatTime(), save = true } = options;
        const template = role === 'assistant' ? templates.agent : templates.user;
        if (!template) {
            return;
        }

        addDayChipIfNeeded();

        const fragment = template.content.cloneNode(true);
        const bubble = role === 'assistant'
            ? fragment.querySelector('.msg p')
            : fragment.querySelector('.msg');
        const timeLabel = fragment.querySelector('.msg-time');

        if (bubble) {
            bubble.textContent = content;
        }

        if (timeLabel) {
            timeLabel.textContent = time;
        }

        messagesEl.appendChild(fragment);
        scrollToBottom();

        if (save) {
            conversationHistory.push({ role, content });
            if (conversationHistory.length > 20) {
                conversationHistory = conversationHistory.slice(-20);
            }
        }
    };

    const toggleSending = (state) => {
        if (wizardCompleted) {
            setChatInputEnabled(!state);
        } else {
            setChatInputEnabled(false);
        }

        if (sendIcon) {
            sendIcon.classList.toggle('d-none', state);
        }
        if (sendSpinner) {
            sendSpinner.classList.toggle('d-none', !state);
        }
    };

    const showTypingIndicator = () => {
        if (!templates.typing) {
            return null;
        }

        const fragment = templates.typing.content.cloneNode(true);
        const indicator = fragment.firstElementChild;
        messagesEl.appendChild(fragment);
        scrollToBottom();
        return indicator;
    };

    const removeTypingIndicator = (indicator) => {
        if (indicator && indicator.parentNode === messagesEl) {
            messagesEl.removeChild(indicator);
        }
    };

    const handleAssistantReply = (text) => {
        renderMessage('assistant', text, { save: true });
    };

    const addFiles = (files) => {
        if (!files || !files.length) {
            return;
        }

        Array.from(files).forEach((file) => {
            if (!file.type.startsWith('image/')) {
                showFeedback('Only image attachments can be added to this chat.', 'error');
                return;
            }
            if (attachments.length >= MAX_ATTACHMENTS) {
                showFeedback(`Attachment limit of ${MAX_ATTACHMENTS} reached. Remove an image before adding more.`, 'error');
                return;
            }
            const url = URL.createObjectURL(file);
            attachments.push({ file, url });
        });
        renderAttachments();
        if (attachments.length) {
            showFeedback(`${attachments.length} image${attachments.length > 1 ? 's' : ''} ready to send.`, 'success');
        } else {
            hideFeedback();
        }
    };

    const sendMessage = async () => {
        const trimmed = inputEl.value.trim();
        const hasAttachments = attachments.length > 0;
        if ((trimmed === '' && !hasAttachments) || sendBtn.disabled) {
            return;
        }

        if (!wizardCompleted) {
            goToWizardStep(wizardStepIndex);
            showFeedback('Answer the quick setup questions to start chatting.', 'info');
            return;
        }

        const priorHistory = conversationHistory.slice(-10);
        inputEl.value = '';
        const attachmentSummary = hasAttachments
            ? `\n\n[Attached images: ${attachments.map((item) => `${item.file.name}`).join(', ')}]`
            : '';
        const outgoingContent = `${trimmed || 'Sharing attachments.'}${attachmentSummary}`.trim();
        renderMessage('user', outgoingContent, { save: true });

        messagesEl.setAttribute('aria-busy', 'true');
        toggleSending(true);
        const typingIndicator = showTypingIndicator();
        const typingStartedAt = getTimestamp();
        let assistantReply = null;
        const wizardPayload = wizardCompleted ? {
            role: wizardContext.role,
            role_label: wizardContext.role_label || wizardLabels.role || null,
            intent: wizardContext.intent,
            intent_label: wizardContext.intent_label || wizardLabels.intent || null,
            intent_group: wizardContext.intent_group || null,
            stage: wizardContext.stage || null,
        } : null;

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    message: outgoingContent,
                    history: priorHistory,
                    diagnostics: diagnosticsCheckbox?.checked ? 1 : 0,
                    wizard_context: wizardPayload,
                }),
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const payload = await response.json();
            const reply = payload.reply || payload.message || payload.error;
            renderSuggestions(payload.suggestions || []);

            assistantReply = reply || 'Thanks! A support agent will take a look and get back shortly.';
        } catch (error) {
            console.error('Help chat send failed:', error);
            assistantReply = 'Sorry, there was an issue sending that. Please try again shortly.';
            renderSuggestions([]);
        } finally {
            await waitForMinimumTyping(typingStartedAt);
            removeTypingIndicator(typingIndicator);
            toggleSending(false);
            messagesEl.setAttribute('aria-busy', 'false');
            if (assistantReply) {
                handleAssistantReply(assistantReply);
            }
            inputEl.focus();
            clearAttachments();
        }
    };

    const initializeConversation = () => {
        conversationHistory = [];
        clearMessages();
        renderSuggestions(defaultSuggestions);

        if (wizard) {
            resetWizard();
        } else {
            setChatInputEnabled(true);
            renderMessage('assistant', defaultGreeting, { save: true });
        }
    };

    initializeConversation();
    populateEmojiGrid();

    sendBtn.addEventListener('click', sendMessage);

    inputEl.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    });

    if (attachBtn && attachmentInput) {
        attachBtn.addEventListener('click', (event) => {
            event.preventDefault();
            closeEmojiPanel();
            attachmentInput.removeAttribute('capture');
            attachmentInput.click();
        });
    }

    if (attachmentInput) {
        attachmentInput.addEventListener('change', (event) => {
            addFiles(event.target.files || []);
            attachmentInput.value = '';
            inputEl.focus();
        });
    }

    if (dropzone) {
        dropzone.addEventListener('click', (event) => {
            event.preventDefault();
            closeEmojiPanel();
            if (attachmentInput) {
                attachmentInput.removeAttribute('capture');
                attachmentInput.click();
            }
        });

        dropzone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('is-dragover');
        });

        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('is-dragover');
            closeEmojiPanel();
            if (event.dataTransfer?.files?.length) {
                addFiles(event.dataTransfer.files);
                inputEl.focus();
            }
        });
    }

    if (uploadBtn) {
        uploadBtn.addEventListener('click', (event) => {
            event.preventDefault();
            dropzone?.dispatchEvent(new Event('click'));
        });
    }

    if (wizard) {
        goToWizardStep(1);
        updateWizardSummary();

        wizard.addEventListener('click', (event) => {
            const option = event.target.closest('.wizard-option');
            if (option) {
                event.preventDefault();
                const key = option.getAttribute('data-wizard-key');
                const value = option.getAttribute('data-wizard-value');
                const label = option.getAttribute('data-wizard-label') || option.textContent.trim();

                if (key && value) {
                    let targetStep = wizardStepIndex + 1;

                    if (key === 'role') {
                        wizardContext.role = value;
                        wizardContext.role_label = label;
                        wizardLabels.role = label;
                        wizardContext.intent = null;
                        wizardContext.intent_label = null;
                        wizardContext.intent_group = null;
                        wizardLabels.intent = '';
                        wizard.querySelectorAll('[data-wizard-key="intent"]').forEach((btn) => btn.classList.remove('is-selected'));
                        filterIntentSections(value);
                        targetStep = 2;
                    }

                    if (key === 'intent') {
                        wizardContext.intent = value;
                        wizardContext.intent_label = label;
                        wizardContext.intent_group = option.getAttribute('data-wizard-group') || null;
                        wizardLabels.intent = label;
                        targetStep = 3;
                    }

                    const optionsContainer = option.closest('.wizard-options');
                    optionsContainer?.querySelectorAll('.wizard-option').forEach((btn) => {
                        btn.classList.toggle('is-selected', btn === option);
                    });

                    updateWizardSummary();
                    hideFeedback();
                    goToWizardStep(targetStep);
                }
            }

            const backBtn = event.target.closest('[data-wizard-back]');
            if (backBtn) {
                event.preventDefault();
                goToWizardStep(wizardStepIndex - 1);
                return;
            }

            const skipBtn = event.target.closest('[data-wizard-skip]');
            if (skipBtn) {
                event.preventDefault();
                wizardContext.stage = '';
                wizardLabels.stage = 'Not specified yet';
                updateWizardSummary();
                hideFeedback();
                goToWizardStep(wizardStepIndex + 1);
                return;
            }

            const nextBtn = event.target.closest('[data-wizard-next]');
            if (nextBtn) {
                event.preventDefault();
                const stageValue = wizardStageInput ? wizardStageInput.value.trim() : '';
                wizardContext.stage = stageValue;
                wizardLabels.stage = stageValue !== '' ? stageValue : 'Not specified yet';
                updateWizardSummary();
                hideFeedback();
                goToWizardStep(wizardStepIndex + 1);
                return;
            }

            const startBtn = event.target.closest('[data-wizard-start]');
            if (startBtn) {
                event.preventDefault();
                finalizeWizard();
            }
        });
    }

    if (cameraBtn && attachmentInput) {
        cameraBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            closeEmojiPanel();
            const cameraAvailable = await hasMediaDevice('videoinput');
            if (!cameraAvailable) {
                showFeedback('No camera detected or permission denied. Check your device and browser settings.', 'error');
                return;
            }
            attachmentInput.setAttribute('capture', 'environment');
            attachmentInput.click();
            setTimeout(() => attachmentInput.removeAttribute('capture'), 0);
        });
    }

    if (emojiBtn) {
        emojiBtn.addEventListener('click', (event) => {
            event.preventDefault();
            toggleEmojiPopover();
            if (!emojiPanel?.classList.contains('d-none')) {
                inputEl.focus();
            }
        });
    }

    modal.querySelectorAll('[data-help-prompt]').forEach((button) => {
        button.addEventListener('click', () => {
            const preset = button.getAttribute('data-help-prompt') || '';
            if (!preset) {
                return;
            }
            inputEl.value = preset;
            sendMessage();
        });
    });

    const audioBtn = modal.querySelector('#playerHelpChatAudio');
    if (audioBtn) {
        audioBtn.addEventListener('click', async () => {
            const hasMic = await hasMediaDevice('audioinput');
            if (!hasMic) {
                showFeedback('No microphone found or permission denied. Please check your device settings before starting an audio chat.', 'error');
                return;
            }
            inputEl.value = 'Can we switch to an audio chat to resolve this quicker?';
            sendMessage();
            renderMessage('assistant', 'Audio chat request logged! Keep this chat open — a support coach will send an audio link shortly.', { save: true });
            showFeedback('Audio chat request sent to support.', 'success');
        });
    }

    const voiceBtn = modal.querySelector('#playerHelpChatVoice');
    if (voiceBtn) {
        voiceBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            closeEmojiPanel();
            const hasMic = await hasMediaDevice('audioinput');
            if (!hasMic) {
                showFeedback('No microphone found or permission denied. Please check your device settings before recording audio.', 'error');
                return;
            }

            inputEl.value = 'I would like to start an audio session to troubleshoot this issue.';
            sendMessage();
            renderMessage('assistant', 'Audio session request noted. We will alert you here once the call is ready to join.', { save: true });
            showFeedback('Audio assistance request sent to support.', 'success');
        });
    }

    const handleDocumentClick = (event) => {
        const clickedInsideEmoji = (emojiPanel && emojiPanel.contains(event.target))
            || (emojiBtn && emojiBtn.contains(event.target));
        if (emojiPanel && !clickedInsideEmoji) {
            closeEmojiPanel();
        }
    };

    document.addEventListener('click', handleDocumentClick);

    modal.addEventListener('shown.bs.modal', () => {
        if (wizard && !wizardCompleted) {
            const firstOption = wizard.querySelector('[data-wizard-step="1"] .wizard-option');
            setTimeout(() => firstOption?.focus(), 120);
        } else {
            setTimeout(() => inputEl.focus(), 120);
        }
    });

    modal.addEventListener('hidden.bs.modal', () => {
        inputEl.value = '';
        clearAttachments();
        closeEmojiPanel();
        hideFeedback();
        initializeConversation();
    });
});
</script>
@endpush

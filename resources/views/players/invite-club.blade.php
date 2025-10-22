@extends('layouts.player-new')
@section('title','Refer a Club')
@section('content')
<div class="refer-card">
    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @error('contacts')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @if(session('failed_invites') && count(session('failed_invites')))
            <div class="alert alert-warning">
                We couldn't send the club invitation to:
                <ul class="mb-0">
                    @foreach(session('failed_invites') as $failed)
                        <li>{{ $failed['email'] }} <small class="text-muted">({{ $failed['error'] }})</small></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="refer-card w-100 panel-soft mb-3 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex flex-wrap align-items-center justify-content-start">
                <div class="eyebrow align-items-center">
                    <span class="pe-3">Refer a Club Earn $1,000</span>
                </div>
                <div class="sub">
                    When the club joins and activates their team(s).
                </div>
            </div>
            <a href="{{ route('player.invite.overview') }}" class="btn btn-outline-primary btn-sm">Referral dashboard</a>
        </div>

        <!-- FORM -->
        <div class="panel mb-3">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="label">Club name</div>
                    <input
                        class="form-control"
                        placeholder="e.g. Ottawa Falcons FC"
                        name="club_name"
                    />
                </div>
                <div class="col-md-5">
                    <div class="label">League</div>
                    <input
                        class="form-control"
                        placeholder="e.g. Ontario Soccer League"
                        name="league"
                    />
                </div>
                <div class="col-md-5">
                    <div class="label">City / Province / State</div>
                    <input class="form-control" placeholder="e.g. Ottawa, ON" name="location" />
                </div>
                <div class="col-md-5">
                    <div class="label">Club website</div>
                    <input
                        class="form-control"
                        placeholder="https://www.exampleclub.ca"
                        name="website"
                    />
                </div>
            </div>
        </div>

        <!-- CONTACTS -->
        <div class="row g-3 align-items-start mb-3">
            <div class="col-lg-9">
                <div class="label">
                    Club contacts (email or phone). Add multiple.
                </div>
                <div class="token-input">
                    <span class="token">
                        manager@falcons.ca
                        <button class="x" type="button" aria-label="Remove">
                            &times;
                        </button>
                    </span>
                    <span class="token">
                        +1 613 555 0199
                        <button class="x" type="button" aria-label="Remove">
                            &times;
                        </button>
                    </span>
                    <span class="token">
                        president@falcons.ca
                        <button class="x" type="button" aria-label="Remove">
                            &times;
                        </button>
                    </span>
                    <input
                        type="text"
                        placeholder="Type and press Enter"
                        id="tokenInput"
                    />
                </div>
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2 mt-lg-4 pt-lg-2">
                    <button class="btn-outline-soft w-100" type="button">
                        <span class="csv-badge">csv</span> Upload CSV
                    </button>
                    <button class="btn-outline-soft w-100" type="button">Paste List</button>
                </div>
            </div>
        </div>

        <!-- TEXTAREAS -->
        <div class="row g-3 mt-1 mb-3">
            <div class="col-lg-9">
                <div class="label">
                    Paste multiple (comma, space, or newline separated)
                </div>
                <textarea
                    class="form-control"
                    rows="3"
                    placeholder="e.g. coach@club.com, ops@club.com, +16135550199"
                    name="contact_list"
                ></textarea>
            </div>
            <div class="col-lg-3">
                <div class="label">Personal message (optional)</div>
                <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Hi! Our platform helps your club run events and rewards players. Join with my link!"
                    name="personal_message"
                ></textarea>
            </div>
        </div>

        <!-- SHAREABLE + PAYOUT -->
        <div class="row g-3 mt-1 mb-3">
            <div class="col-lg-7">
                <div class="panel">
                    <div class="link-title">Shareable link</div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="big-link" id="shareLink">
                            club.join/PLAY-CLUB-1234
                        </div>
                        <button class="btn btn-primary ms-auto" id="copyLinkTop">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="panel payout">
                    <div class="link-title">Payout</div>
                    <div>
                        <small
                            >Earn $1,000 when the referred club activates 300+ players
                            within 14–30 days.</small
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex flex-wrap gap-2 mb-2">
                <button class="btn btn-primary send-invitations-btn" type="submit">Send Invitations</button>
                <button class="btn-muted send-invitations-btn" type="button">Get Shareable Link</button>
                <div class="ms-auto d-flex align-items-center sub">
                    Or share directly
                </div>
            </div>
            <div class="share-mini mt-2">
                <div class="item" role="button" id="copyLinkBottom">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                        <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                    </svg>
                    Copy Link
                </div>
                <div class="item" role="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                        <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                    </svg>
                    WhatsApp
                </div>
                <div class="item" role="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                        <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                    </svg>
                    SMS
                </div>
                <div class="item" role="button">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                        <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                    </svg>
                    Email
                </div>
            </div>
        </div>

        @if(session('link'))
            <div class="alert alert-info mt-3">
                Share this link: <a href="{{ session('link') }}">{{ session('link') }}</a>
            </div>
        @endif
    </div>
</div>

<!-- Hidden form for submission -->
<form method="POST" action="{{ route('player.invite.club.store') }}" id="inviteForm" style="display: none;">
    @csrf
    <input type="hidden" name="type" value="club" />
    <input type="hidden" name="form_data" id="formData" />
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    const submitButtons = document.querySelectorAll('.send-invitations-btn');
    submitButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            // Collect form data
            const formData = {};
            const inputs = document.querySelectorAll('input[name], textarea[name]');
            inputs.forEach(input => {
                if (input.value.trim()) {
                    formData[input.name] = input.value;
                }
            });

            // Set form data and submit
            document.getElementById('formData').value = JSON.stringify(formData);
            document.getElementById('inviteForm').submit();
        });
    });

    // Copy link functionality
    const copyButtons = document.querySelectorAll('#copyLinkTop, #copyLinkBottom');
    const shareLink = document.getElementById('shareLink');
    if (shareLink) {
        shareLink.textContent = '{{ session('link', url('/invite/' . auth()->id())) }}';
    }
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const linkElement = document.getElementById('shareLink');
            if (linkElement) {
                navigator.clipboard.writeText(linkElement.textContent).then(() => {
                    // Show success feedback
                    const originalText = button.textContent;
                    button.textContent = 'Copied!';
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 2000);
                });
            }
        });
    });

    // Token input functionality
    const tokenInput = document.getElementById('tokenInput');
    const tokenContainer = tokenInput.parentElement;

    tokenInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value) {
                // Create new token
                const token = document.createElement('span');
                token.className = 'token';
                token.innerHTML = `
                    ${value}
                    <button class="x" type="button" aria-label="Remove">
                        &times;
                    </button>
                `;

                // Insert before input
                tokenContainer.insertBefore(token, tokenInput);

                // Clear input
                this.value = '';

                // Add remove functionality
                token.querySelector('.x').addEventListener('click', function() {
                    token.remove();
                });
            }
        }
    });

    // Remove existing tokens
    document.querySelectorAll('.token .x').forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
});
</script>
@endsection

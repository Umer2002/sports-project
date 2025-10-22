@extends('layouts.player-new')
@section('title','Invite Players')
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
                We couldn't send invitations to:
                <ul class="mb-0">
                    @foreach(session('failed_invites') as $failed)
                        <li>{{ $failed['email'] }} <small class="text-muted">({{ $failed['error'] }})</small></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="refer-card w-100 panel-soft mb-3 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center justify-content-start">
                <div class="eyebrow align-items-center">
                    <span class="pe-3"><i>Invite & Earn $10 </i></span>
                </div>
                <div class="sub">
                    Get $10 for each friend who joins and completes signup.
                </div>
            </div>
            <a href="{{ route('player.invite.overview') }}" class="btn btn-outline-primary btn-sm">Referral dashboard</a>
        </div>

        <!-- FORM -->
        <form action="{{ route('player.invite.store') }}" method="POST" id="inviteForm">
            @csrf
            <input type="hidden" name="type" value="player">
            <input type="hidden" name="form_data" id="formData">

            <div class="panel mb-3">
                <div class="row g-3">
                    <div class="col-md-12 club-div">
                        <div>
                            <div class="label">Your Referral Code</div>
                            <span class="eyebrow pe-3"><i>{{ auth()->user()->referral_code ?? 'PLAY' . str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}</i></span>
                        </div>
                        <button class="btn btn-primary ms-auto" type="button" id="copyLinkTop">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>

            <!-- CONTACTS -->
            <div class="row g-3 align-items-start mb-3">
                <div class="col-lg-9">
                    <div class="label">Invite by email or phone</div>
                    <div class="token-input" id="tokenContainer">
                        <!-- Dynamic tokens will be added here -->
                        <input
                            type="text"
                            placeholder="Type email or phone and press Enter"
                            id="tokenInput"
                            class="form-control"
                        />
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex gap-2 mt-lg-4 pt-lg-2 mt-3">
                        <button class="btn-outline-soft w-100" type="button" id="addFromContacts">
                            <i>Add from Contacts</i>
                        </button>
                        <button class="btn-outline-soft w-100" type="button" id="pasteList"><i>Paste List</i></button>
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
                        placeholder="e.g. sam@example.com, +16135550199, jordan@club.ca"
                        name="contact_list"
                        id="contactList"
                    ></textarea>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="label mt-3">
                You'll earn $10 for each verified signup. They'll also get $10 off.
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button class="btn btn-primary send-invitations-btn" type="submit">Send Invitations</button>
                    <button class="btn-muted send-invitations-btn" type="button" id="previewMessage">Preview Message</button>
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
                    <div class="item" role="button" id="shareWhatsApp">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                            <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                        </svg>
                        WhatsApp
                    </div>
                    <div class="item" role="button" id="shareSMS">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                            <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                        </svg>
                        SMS
                    </div>
                    <div class="item" role="button" id="shareEmail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.07 0l1.77-1.77a5 5 0 1 0-7.07-7.07L10 5"></path>
                            <path d="M14 11a5 5 0 0 0-7.07 0L5.16 12.8a5 5 0 0 0 7.07 7.07L14 19"></path>
                        </svg>
                        Email
                    </div>
                </div>
            </div>
        </form>

        @if(session('link'))
            <div class="alert alert-info mt-3">
                Share this link: <a href="{{ session('link') }}">{{ session('link') }}</a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tokenContainer = document.getElementById('tokenContainer');
    const tokenInput = document.getElementById('tokenInput');
    const contactList = document.getElementById('contactList');
    const formData = document.getElementById('formData');
    const inviteForm = document.getElementById('inviteForm');

    let contacts = [];
    let inviteLink = '{{ session("link") }}' || '{{ url("/invite/" . auth()->id()) }}';

    // Token input functionality
    tokenInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const value = this.value.trim();
            if (value && isValidContact(value)) {
                addToken(value);
                this.value = '';
            }
        }
    });

    // Add token function
    function addToken(value) {
        if (contacts.includes(value)) return;

        contacts.push(value);
        const token = document.createElement('span');
        token.className = 'token';
        token.innerHTML = `
            ${value}
            <button class="x" type="button" aria-label="Remove" onclick="removeToken(this, '${value}')">
                &times;
            </button>
        `;
        tokenContainer.insertBefore(token, tokenInput);
        updateFormData();
    }

    // Remove token function
    window.removeToken = function(button, value) {
        const token = button.parentElement;
        token.remove();
        contacts = contacts.filter(contact => contact !== value);
        updateFormData();
    };

    // Validate contact (email or phone)
    function isValidContact(value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return emailRegex.test(value) || phoneRegex.test(value.replace(/\s/g, ''));
    }

    // Update form data
    function updateFormData() {
        const data = {
            contacts: contacts,
            contactList: contactList.value
        };
        formData.value = JSON.stringify(data);
    }

    // Contact list textarea change
    contactList.addEventListener('input', function() {
        updateFormData();
    });

    // Paste list functionality
    document.getElementById('pasteList').addEventListener('click', function() {
        navigator.clipboard.readText().then(text => {
            const contacts = text.split(/[,\n\s]+/).filter(contact => contact.trim());
            contacts.forEach(contact => {
                if (isValidContact(contact.trim())) {
                    addToken(contact.trim());
                }
            });
        });
    });

    // Copy link functionality
    document.getElementById('copyLinkTop').addEventListener('click', function() {
        copyToClipboard(inviteLink);
    });

    document.getElementById('copyLinkBottom').addEventListener('click', function() {
        copyToClipboard(inviteLink);
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link copied to clipboard!');
        });
    }

    // Share functionality
    document.getElementById('shareWhatsApp').addEventListener('click', function() {
        const message = `Join me on Play2Earn Sports! Use my referral code: {{ auth()->user()->referral_code ?? 'PLAY' . str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}`;
        const url = `https://wa.me/?text=${encodeURIComponent(message + ' ' + inviteLink)}`;
        window.open(url, '_blank');
    });

    document.getElementById('shareSMS').addEventListener('click', function() {
        const message = `Join me on Play2Earn Sports! Use my referral code: {{ auth()->user()->referral_code ?? 'PLAY' . str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}`;
        const url = `sms:?body=${encodeURIComponent(message + ' ' + inviteLink)}`;
        window.open(url);
    });

    document.getElementById('shareEmail').addEventListener('click', function() {
        const subject = 'Join me on Play2Earn Sports!';
        const body = `Hi! I'd like to invite you to join Play2Earn Sports. Use my referral code: {{ auth()->user()->referral_code ?? 'PLAY' . str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}\n\n${inviteLink}`;
        const url = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.open(url);
    });

    // Preview message
    document.getElementById('previewMessage').addEventListener('click', function() {
        const message = `Hi! I'd like to invite you to join Play2Earn Sports. Use my referral code: {{ auth()->user()->referral_code ?? 'PLAY' . str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}\n\n${inviteLink}`;
        alert('Preview Message:\n\n' + message);
    });

    // Form submission
    inviteForm.addEventListener('submit', function(e) {
        if (contacts.length === 0 && !contactList.value.trim()) {
            e.preventDefault();
            alert('Please add at least one contact to invite.');
            return;
        }
        updateFormData();
    });
});
</script>
@endsection

@extends('layouts.club-dashboard')
@section('title', 'Invite Players')
@section('page_title', 'Invite Players')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Invite Players to Join Your Club</h4>
                    <p class="card-subtitle">Send invitations to players to join {{ auth()->user()->club->name }}</p>
                </div>
                <div class="card-body">
                    @php
                        $newInviteLinks = session('generated_invite_links', []);
                    @endphp

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('bulk_errors'))
                        <div class="alert alert-warning">
                            <strong>Bulk upload notices:</strong>
                            <ul class="mb-0">
                                @foreach(session('bulk_errors') as $bulkError)
                                    <li>{{ $bulkError }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty($newInviteLinks))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong>Invite links generated:</strong>
                            <ul class="mb-0">
                                @foreach ($newInviteLinks as $inviteLink)
                                    <li class="d-flex flex-wrap align-items-center gap-2">
                                        <span>{{ $inviteLink['email'] ?? 'Invite' }} — <a href="{{ $inviteLink['link'] }}" target="_blank" rel="noopener">{{ $inviteLink['link'] }}</a></span>
                                        <button type="button" class="btn btn-sm btn-outline-primary copy-invite-link" data-link="{{ $inviteLink['link'] }}">Copy</button>
                                    </li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(isset($invites) && $invites->isNotEmpty())
                        <div class="mb-5">
                            <h5>Recent Player Invites</h5>
                            <p class="text-muted">Copy any invite link below to share it via your own email or messaging apps.</p>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">Email</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Invited</th>
                                            <th scope="col" class="text-end">Link</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invites as $invite)
                                            @php
                                                $inviteUrl = route('invite.accept', ['token' => $invite->token]);
                                            @endphp
                                            <tr>
                                                <td>{{ $invite->receiver_email }}</td>
                                                <td>
                                                    @if($invite->accepted_at)
                                                        <span class="badge bg-success">Accepted</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($invite->created_at)->format('M j, Y g:i a') }}</td>
                                                <td class="text-end">
                                                    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                                        <a href="{{ $inviteUrl }}" target="_blank" rel="noopener" class="text-decoration-underline small">{{ $inviteUrl }}</a>
                                                        <button type="button" class="btn btn-sm btn-outline-primary copy-invite-link" data-link="{{ $inviteUrl }}">Copy</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mb-5">
                        <h5>Bulk Invite via CSV</h5>
                        <p class="text-muted">Upload a CSV file with <code>player_email</code> and <code>name</code> columns to invite multiple players at once. Invitations are queued and released every few seconds to avoid server spikes.</p>

                        <form action="{{ route('club.players.invite.bulk') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label" for="bulk_file">CSV File</label>
                                    <input type="file" name="bulk_file" id="bulk_file" class="form-control" accept=".csv,text/csv" required>
                                    <div class="form-text">Maximum 500 rows per upload.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="bulk_personal_message">Personal Message (Optional)</label>
                                    <textarea name="bulk_personal_message" id="bulk_personal_message" class="form-control" rows="3" placeholder="Add a message to appear in the invitation email">{{ old('bulk_personal_message') }}</textarea>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-upload"></i> Upload &amp; Send Invites
                                </button>
                            </div>
                        </form>
                    </div>

                    <form action="{{ route('club.players.invite.store') }}" method="POST">
                        @csrf

                        <!-- Club Information -->
                        <div class="mb-4">
                            <h5>Club Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <strong>Club:</strong> {{ auth()->user()->club->name }}<br>
                                        <strong>Sport:</strong> {{ auth()->user()->club->sport->name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Player Invitations -->
                        <div class="mb-4">
                            <h5>Player Email Addresses</h5>
                            <p class="text-muted">Enter email addresses of players you want to invite to join your club.</p>
                            
                            <div id="email-container">
                                <div class="row mb-2">
                                    <div class="col-md-8">
                                        <input type="email" name="emails[]" class="form-control" placeholder="Enter player email address" required>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="positions[]" class="form-select">
                                            <option value="">Select Preferred Position (Optional)</option>
                                            @if(isset($positions))
                                                @foreach($positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-email-row">
                                <i class="fas fa-plus"></i> Add Another Player
                            </button>
                        </div>

                        <!-- Personal Message -->
                        <div class="mb-4">
                            <h5>Personal Message (Optional)</h5>
                            <textarea name="personal_message" class="form-control" rows="4" placeholder="Add a personal message to your invitation...">{{ old('personal_message', 'Hi! You\'re invited to join ' . auth()->user()->club->name . ' on our platform. Join us to participate in events, track your progress, and connect with other players!') }}</textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('club.players.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Players
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Invitations
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailContainer = document.getElementById('email-container');
    const addButton = document.getElementById('add-email-row');

    addButton.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2';
        newRow.innerHTML = `
            <div class="col-md-8">
                <input type="email" name="emails[]" class="form-control" placeholder="Enter player email address" required>
            </div>
            <div class="col-md-3">
                <select name="positions[]" class="form-select">
                    <option value="">Select Preferred Position (Optional)</option>
                    @if(isset($positions))
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->position_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-email-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        emailContainer.appendChild(newRow);
    });

    // Remove email row functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-email-row')) {
            e.target.closest('.row').remove();
        }
    });

    function showCopiedState(button) {
        const originalLabel = button.dataset.originalLabel || button.textContent;
        button.dataset.originalLabel = originalLabel;
        button.textContent = 'Copied!';
        button.disabled = true;
        setTimeout(() => {
            button.textContent = originalLabel;
            button.disabled = false;
        }, 1800);
    }

    function fallbackCopy(text, button) {
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        try {
            document.execCommand('copy');
            showCopiedState(button);
        } catch (err) {
            console.error('Fallback copy failed', err);
        }
        document.body.removeChild(tempInput);
    }

    function bindCopyButtons() {
        document.querySelectorAll('.copy-invite-link').forEach((button) => {
            button.addEventListener('click', function () {
                const link = this.dataset.link;

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(link)
                        .then(() => showCopiedState(this))
                        .catch(() => fallbackCopy(link, this));
                } else {
                    fallbackCopy(link, this);
                }
            });
        });
    }

    bindCopyButtons();
});
</script>
@endsection

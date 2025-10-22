@extends('layouts.admin')

@section('title', 'Lifetime Player Invites')

@section('content')
    <div class="container-fluid py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                @if (session('generated_invite_token'))
                    @php
                        $newLink = route('invite.accept', session('generated_invite_token'));
                    @endphp
                    <div class="mt-2 small">
                        <strong>Invite link:</strong>
                        <a href="{{ $newLink }}" target="_blank" rel="noopener">{{ $newLink }}</a>
                    </div>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Create Lifetime Free Player Invite</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.player-invites.free.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label for="email" class="form-label">Player Email <span class="text-muted">(optional)</span></label>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="player@example.com" value="{{ old('email') }}">
                        <div class="form-text">If provided, the invite will be linked to this email.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="notes" class="form-label">Notes <span class="text-muted">(optional)</span></label>
                        <input type="text" name="notes" id="notes" class="form-control"
                               placeholder="Reason or context" value="{{ old('notes') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Generate Invite
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Lifetime Free Invites</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th scope="col">Email</th>
                            <th scope="col">Status</th>
                            <th scope="col">Created</th>
                            <th scope="col">Accepted</th>
                            <th scope="col">Notes</th>
                            <th scope="col">Invite Link</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($invites as $invite)
                            @php
                                $link = route('invite.accept', $invite->token);
                                $metadata = is_array($invite->metadata) ? $invite->metadata : [];
                            @endphp
                            <tr>
                                <td>{{ $invite->receiver_email ?: '—' }}</td>
                                <td>
                                    @if ($invite->accepted_at)
                                        <span class="badge bg-success">Accepted</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>{{ optional($invite->created_at)->format('M j, Y g:i a') }}</td>
                                <td>
                                    {{ optional($invite->accepted_at)->format('M j, Y g:i a') ?? '—' }}
                                </td>
                                <td>{{ $metadata['notes'] ?? '—' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ $link }}" target="_blank" rel="noopener" class="small text-decoration-underline">
                                            {{ $link }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary copy-link"
                                                data-link="{{ $link }}">Copy</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No lifetime free invites generated yet.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $invites->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.copy-link').forEach(function (button) {
                button.addEventListener('click', function () {
                    navigator.clipboard.writeText(this.dataset.link).then(() => {
                        this.innerText = 'Copied!';
                        setTimeout(() => this.innerText = 'Copy', 1500);
                    });
                });
            });
        </script>
    @endpush
@endsection

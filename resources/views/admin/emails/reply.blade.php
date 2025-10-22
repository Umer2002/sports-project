@extends('layouts.admin')

@section('title', 'Reply')

@section('header_styles')
<link href="{{ asset('vendors/summernote/css/summernote-bs4.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/mail_box.css') }}" rel="stylesheet">
@endsection

@section('content')
@if (isset($email_not_found))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ $email_not_found }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (isset($success))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success:</strong> {{ $success }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row clearfix">
    <!-- Sidebar -->
    <div class="col-md-4 col-lg-3">
        <div class="card">
            <div class="body">
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <a href="{{ route('admin.emails.compose') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-envelope me-1"></i> Compose
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('admin.emails.inbox') }}">
                            <i class="fa fa-inbox me-1"></i> Inbox
                            @if ($count > 0)
                                <span class="badge bg-success float-end">{{ $count }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.emails.sent') }}">
                            <i class="fa fa-paper-plane me-1"></i> Sent
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="col-md-8 col-lg-9">
        <div class="card">
            <div class="header bg-primary text-white">
                <h2><i class="fa fa-reply me-2"></i>Reply</h2>
            </div>

            <div class="body">
                <form action="{{ url('admin/emails/compose') }}" method="POST" enctype="multipart/form-data" id="mail_compose">
                    @csrf

                    <div class="mb-3">
                        <label for="email_id" class="form-label">To</label>
                        <input type="email" name="email_id" id="email_id" class="form-control"
                            placeholder="Recipient Email"
                            value="{{ old('email_id', $email->senderName->email ?? '') }}" required>
                        @error('email_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" name="subject" id="subject" class="form-control"
                            placeholder="Subject" value="{{ old('subject', $email->subject ?? '') }}" required>
                        @error('subject')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email_message" class="form-label">Message</label>
                        <textarea id="summernote" name="email_message" class="form-control" rows="6">{{ old('email_message') }}</textarea>
                        @error('email_message')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-paper-plane me-1"></i> Send
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-archive me-1"></i> Draft
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/summernote/js/summernote-bs4.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#summernote').summernote({
            height: 300,
            placeholder: 'Write your reply here...'
        });
    });
</script>
@endsection

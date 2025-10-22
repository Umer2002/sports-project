@extends($layout ?? 'layouts.admin')

@section('title', 'Inbox')

@section('header_styles')
<link href="{{ asset('vendors/iCheck/css/all.css') }}" rel="stylesheet">
<link href="{{ asset('css/pages/mail_box.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-lg-3 col-md-4 col-sm-12">
        <div class="card">
            <div class="body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('email.compose') }}" class="btn btn-primary btn-block">
                            <i class="fa fa-envelope me-1"></i> Compose
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('email.inbox') }}" class="d-flex justify-content-between align-items-center {{ request()->routeIs('email.inbox') ? 'fw-bold' : '' }}">
                            <span><i class="fa fa-inbox me-1"></i> Inbox</span>
                            @if ($unread_mails_count > 0)
                                <span class="badge bg-success">{{ $unread_mails_count }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('email.sent') }}">
                            <i class="fa fa-paper-plane me-1"></i> Sent
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-9 col-md-8 col-sm-12">
        <div class="card">
            <div class="header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2><i class="fa fa-envelope-open-text me-1"></i> Inbox</h2>
            </div>

            <div class="body">
                @if (isset($success))
                    <div class="alert alert-success alert-dismissible fade show">
                        <strong>Success:</strong> {{ $success }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <table class="table table-hover align-middle" id="inbox-check">
                    <thead>
                        <tr>
                            <th width="5%">
                                <input type="checkbox" id="checkall" class="form-check-input select_all_mail">
                            </th>
                            <th width="5%"><i class="fa fa-star"></i></th>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th class="text-end">Received</th>
                        </tr>
                    </thead>
                    <tbody class="tr_read">
                        @foreach ($emails as $email)
                        <tr data-messageid="{{ $email->id }}" class="{{ $email->status === 1 ? 'fw-bold' : '' }}">
                            <td>
                                <input type="checkbox" name="inbox_checkbox" class="mail-checkbox form-check-input" value="{{ $email->id }}">
                            </td>
                            <td>
                                <i class="fa fa-star starred"></i>
                            </td>
                            <td>
                                <a href="{{ route('email.show', $email->id) }}" class="d-flex align-items-center">
                                    @php
                                        $currentUser = Auth::user();
                                        $gender = $currentUser->gender ?? null;
                                        $defaultAvatar = $gender === 'female'
                                            ? asset('images/authors/avatar5.png')
                                            : ($gender === 'male'
                                                ? asset('images/authors/avatar3.png')
                                                : asset('images/authors/no_avatar.jpg'));
                                        $avatar = $email->senderName->pic ?? $defaultAvatar;
                                    @endphp
                                    <img src="{{ $avatar }}" alt="img" class="rounded-circle me-2" width="40" height="40">
                                    <span>{{ $email->senderName->first_name }} {{ $email->senderName->last_name }}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('email.show', $email->id) }}">{{ $email->subject }}</a>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('email.show', $email->id) }}">
                                    {{ $email->created_at->diffForHumans() }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pt-3">
                    {!! $emails->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_scripts')
<script src="{{ asset('vendors/iCheck/js/icheck.js') }}"></script>
<script src="{{ asset('js/pages/mail_box.js') }}"></script>
<script>
    document.getElementById('checkall').addEventListener('change', function () {
        document.querySelectorAll('.mail-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection

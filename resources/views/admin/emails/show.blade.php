@extends($layout ?? 'layouts.admin')

@section('title', 'Read Email')

@section('content')
<section class="content-header">
    <h1>Read Email</h1>
</section>

<section class="content ps-3 pe-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>{{ $email->subject }}</h4>
        </div>
        <div class="card-body">
            <p><strong>From:</strong> {{ $email->senderName->email ?? '-' }}</p>
            <p><strong>To:</strong> {{ $email->email_id }}</p>
            <hr>
            <div>
                {!! nl2br(e($email->email_message)) !!}
            </div>
        </div>
    </div>
</section>
@endsection

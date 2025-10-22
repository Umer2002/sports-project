@extends($layout ?? 'layouts.admin')

@section('title', 'Compose Email')

@section('content')
<section class="content-header">
    <h1>Compose Email</h1>
</section>

<section class="content ps-3 pe-3">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('email.send') }}">
                @csrf

                <div class="mb-3">
                    <label for="email_id" class="form-label">Recipient Email</label>
                    <input type="email" class="form-control" name="email_id" required>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" name="subject">
                </div>

                <div class="mb-3">
                    <label for="email_message" class="form-label">Message</label>
                    <textarea class="form-control" name="email_message" rows="5"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" name="action" value="draft" class="btn btn-warning">Save as Draft</button>
                    <button type="submit" name="action" value="send" class="btn btn-success">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

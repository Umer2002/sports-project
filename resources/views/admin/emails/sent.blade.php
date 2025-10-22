@extends('layouts.admin')

@section('title', 'Sent Emails')

@section('content')
<section class="content-header">
    <h1>Sent Emails</h1>
</section>

<section class="content ps-3 pe-3">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4><i class="fa fa-paper-plane me-1"></i> Sent Emails</h4>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Sent At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($emails as $email)
                        <tr>
                            <td>{{ $email->email_id }}</td>
                            <td>{{ $email->subject }}</td>
                            <td>{{ $email->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('email.show', $email->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No sent emails found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {!! $emails->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</section>
@endsection

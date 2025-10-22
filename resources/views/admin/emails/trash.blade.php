@extends($layout ?? 'layouts.admin')

@section('title', 'Trash')

@section('content')
<section class="content-header">
    <h1>Trashed Emails</h1>
</section>

<section class="content ps-3 pe-3">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4><i class="fa fa-trash me-1"></i> Trash</h4>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.email.bulk-move-to-trash') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="checkall" class="form-check-input">
                                </th>
                                <th>Recipient</th>
                                <th>Subject</th>
                                <th>Deleted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($emails as $email)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_emails[]" value="{{ $email->id }}" class="form-check-input">
                                    </td>
                                    <td>{{ $email->email_id }}</td>
                                    <td>{{ $email->subject ?? '(No Subject)' }}</td>
                                    <td>{{ $email->deleted_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No emails in trash.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($emails->count())
                <div class="mt-3">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete selected emails permanently?')">
                        Delete Selected
                    </button>
                </div>
                @endif
            </form>

            <div class="mt

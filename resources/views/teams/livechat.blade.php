{{-- create view for live chat --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Live Chat</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <chat-messages :messages="messages"></chat-messages>

                    <chat-form v-on:messagesent="addMessage"></chat-form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.Laravel = { csrfToken: '{{ csrf_token() }}' }
</script>
<script src="{{ mix('js/app.js') }}"></script>
@endpush

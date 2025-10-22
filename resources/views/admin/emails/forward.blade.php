@extends($layout ?? 'layouts.admin')

{{-- Page title --}}
@section('title')
    Forward Mail
    @parent
@stop

{{-- page level styles --}}
@section('header_styles')
    <link href="{{ asset('vendors/select2/css/select2.min.css') }}" type="text/css" rel="stylesheet">
    <link href="{{ asset('vendors/select2/css/select2-bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('vendors/summernote/css/summernote-bs4.css') }}" rel="stylesheet" media="screen" />
    <link href="{{ asset('css/pages/mail_box.css') }}" rel="stylesheet" type="text/css" />
@stop
{{-- Page content --}}
@section('content')
    @if (isset($email_not_found))
        <div id="notific">
            <div class="alert alert-danger alert-dismissible margin5">
                <strong>Error:</strong> {{ $email_not_found }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if (isset($success))
        <div id="notific">
            <div class="alert alert-success alert-dismissible margin5">
                <strong>Success:</strong> {{ $success }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Content Header (Page header) -->
    <section class="content-header">

        <!--section starts-->
        <h1>Forward</h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <i class="livicon" data-name="home" data-size="14" data-loop="true"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="#">Emails</a>
            </li>
            <li class="active">Forward</li>
        </ol>
    </section>
    <!-- Main content -->
    <!-- Main content -->
    <section class="content ps-3 pe-3">
        <div class="row">
            <div class="col-lg-3 col-xl-2 col-md-3 col-sm-4  web-mail">
                <div class="whitebg1">
                    <ul>
                        <li class="compose">
                            <a href="{{ URL::to('admin/emails/compose') }}">
                                <i class="fa fa-fw fa-envelope"></i>
                                &nbsp; &nbsp;Compose
                            </a>
                        </li>
                        <li>
                            <a href="{{ URL::to('admin/emails/inbox') }}">
                                <i class="fa fa-inbox" aria-hidden="true"></i>
                                &nbsp; &nbsp;Inbox
                                @if ($count > 0)
                                    <span class="badge rounded-pill bg-success float-end  mt-1">{{ $count }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ URL::to('admin/emails/sent') }}">
                                <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                &nbsp; &nbsp;Sent
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-9 col-xl-10 col-md-9 col-sm-8">
                <div class="card border-primary">
                    <div class="card-header bg-primary border-bottom">
                        <h4 class="mb-0">
                            <strong>Forward</strong>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ URL('admin/emails/compose') }}" method="POST" class="form-horizontal"
                            enctype="multipart/form-data" files="true" id="mail_compose">
                            {{ csrf_field() }}
                            <div class="compose">
                                <div class="form-group  {{ $errors->first('email_id', 'has-error') }}">
                                    <label class="col-xs-2 form-label hidden-xs" for="email_id">To:</label>
                                    <div class="col-xs-9">
                                        <select class="form-select select_email" name="email_id" id="email_id">
                                            <option></option>
                                            @foreach ($existing_emails as $existing_email)
                                                <option value={{ $existing_email->email }}"">{{ $existing_email->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('email_id', '<span class="form-text">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class="form-group  {{ $errors->first('subject', 'has-error') }}">
                                    <label class="col-xs-2 form-label hidden-xs" for="subject">Subject:</label>
                                    <div class="col-xs-9">
                                        <input type="text" name="subject" class="form-control required" id="subject"
                                            tabindex="1" value="{{ $email->subject }}" placeholder="Subject">
                                        {!! $errors->first('subject', '<span class="help-block">:message</span>') !!}
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <div class='box-body pad'>
                                    <div class="form-group {{ $errors->first('email_message', 'has-error') }}">
                                        <textarea id="summernote" class="form-control height400" name="email_message">{{ $email->email_message }}</textarea>
                                        {!! $errors->first('email_message', '<span class="form-text">:message</span>') !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <button type="submit" class="btn btn-sm  btn-primary btn_margin_top">
                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                    Send
                                </button>

                                <a href="#" class="btn btn-sm btn-success btn_margin_top">
                                    <i class="fa fa-archive" aria-hidden="true"></i>
                                    Draft
                                </a>
                            </div>

                        </form>
                    </div>

                </div>
            </div>


        </div>
    </section>
    <!-- content -->

@stop

{{-- page level scripts --}}
@section('footer_scripts')
    <script src="{{ asset('vendors/select2/js/select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendors/summernote/js/summernote-bs4.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/pages/compose.js') }}" type="text/javascript"></script>

@stop

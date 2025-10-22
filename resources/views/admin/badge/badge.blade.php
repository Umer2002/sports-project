@extends('layouts.admin')
{{-- Page title --}}
@section('title')
Badge
@parent
@stop

{{-- page level styles --}}
@section('header_styles')
<link rel="stylesheet" type="text/css" href="{{ asset('vendors/datatables/css/dataTables.bootstrap5.css') }}" />
<link href="{{ asset('css/pages/tables.css') }}" rel="stylesheet" type="text/css" />
<style>
    .page-wrap {
        padding: 20px;
    }

    .entry-txt {
        padding: 8px;
    }

    .badge-history {
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    .common_btn {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 10px;
        width: 100%;
        border: none;
        border-radius: 5px;
    }

    .common_btn_2 {
        background-color: #28a745;
        color: white;
        text-align: center;
        padding: 10px;
        width: 100%;
        border: none;
        border-radius: 5px;
    }

    .card {
        margin: 15px 0;
    }

    .card-header {
        background-color: #007bff;
        color: white;
    }

    .card-body {
        background-color: #f9f9f9;
    }

    .card-body p {
        font-size: 14px;
    }

    .badge-image {
        text-align: center;
        /* Center the contents */
        display: flex;
        flex-direction: column;
        /* Stack the image and text vertically */
        justify-items: center;
        align-items: center;
    }

    .common_btn:hover {
        background-color: #007bff;
        /* Keep the background color same */
        color: white;
        /* Ensure the text color stays white */
    }

    .common_btn_2:hover {
        background-color: #007bff;
        /* Keep the background color same */
        color: white;
        /* Ensure the text color stays white */
    }
</style>
@stop

{{-- Page content --}}
@section('content')
<section class="content-header">
    <h1>Badge</h1>
    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <i class="livicon" data-name="home" data-size="14" data-color="#000"></i>
                Dashboard
            </a>
        </li>
        <li><a href="#">Badge</a></li>
        <li class="active">Badge</li>
    </ol>
</section>

<!-- Main content -->
<section class="content ps-3 pe-3">
    <div class="page-wrap">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-12 my-3">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <span>
                            <i class="livicon" data-name="tasks" data-size="16" data-loop="true" data-c="#fff"
                                data-hc="white"></i> {{get_phrase('Build trust with Sociopro Verified')}}
                        </span>
                        <span class="float-end">
                            <i class="fa fa-chevron-up clickable"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="badge-image">
                            <img src="{{ get_user_image(Sentinel::getUser()->id, 'optimized') }}" alt="img" height="35px" width="100px" class="rounded-circle img-fluid float-start" />
                            <div class="badge_info d-flex justify-content-center mt-2">
                                <h5>{{ Sentinel::getUser()->first_name }}</h5> <!-- Displaying the first name -->
                                <p>
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.1825 1.16051C11.5808 0.595046 12.4192 0.595047 12.8175 1.16051L13.8489 2.62463C14.1272 3.01962 14.648 3.15918 15.0865 2.95624L16.7118 2.20397C17.3395 1.91343 18.0655 2.33261 18.1277 3.02149L18.2889 4.80515C18.3324 5.28634 18.7137 5.66763 19.1948 5.71111L20.9785 5.87226C21.6674 5.9345 22.0866 6.66054 21.796 7.28825L21.0438 8.91352C20.8408 9.35198 20.9804 9.87284 21.3754 10.1511L22.8395 11.1825C23.405 11.5808 23.405 12.4192 22.8395 12.8175L21.3754 13.8489C20.9804 14.1272 20.8408 14.648 21.0438 15.0865L21.796 16.7118C22.0866 17.3395 21.6674 18.0655 20.9785 18.1277L19.1948 18.2889C18.7137 18.3324 18.3324 18.7137 18.2889 19.1948L18.1277 20.9785C18.0655 21.6674 17.3395 22.0866 16.7117 21.796L15.0865 21.0438C14.648 20.8408 14.1272 20.9804 13.8489 21.3754L12.8175 22.8395C12.4192 23.405 11.5808 23.405 11.1825 22.8395L10.1511 21.3754C9.87284 20.9804 9.35198 20.8408 8.91352 21.0438L7.28825 21.796C6.66054 22.0866 5.9345 21.6674 5.87226 20.9785L5.71111 19.1948C5.66763 18.7137 5.28634 18.3324 4.80515 18.2889L3.02149 18.1277C2.33261 18.0655 1.91343 17.3395 2.20397 16.7117L2.95624 15.0865C3.15918 14.648 3.01962 14.1272 2.62463 13.8489L1.16051 12.8175C0.595046 12.4192 0.595047 11.5808 1.16051 11.1825L2.62463 10.1511C3.01962 9.87284 3.15918 9.35198 2.95624 8.91352L2.20397 7.28825C1.91343 6.66054 2.33261 5.9345 3.02149 5.87226L4.80515 5.71111C5.28634 5.66763 5.66763 5.28634 5.71111 4.80515L5.87226 3.02149C5.9345 2.33261 6.66054 1.91343 7.28825 2.20397L8.91352 2.95624C9.35198 3.15918 9.87284 3.01962 10.1511 2.62463L11.1825 1.16051Z" fill="#329CE8" />
                                        <path d="M7.5 11.83L10.6629 14.9929L17 8.66705" stroke="white" stroke-width="1.67647" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </p>
                            </div>
                        </div>
                        <div class="box-body">
                            <ul class="entry_badge">

                                <div class="entry_badge_text">
                                    <h5 style="font-weight: bold;"><i class="fa-solid fa-circle-check"></i>{{get_phrase('A verified badge')}}</h5>
                                    <p>{{get_phrase('Your audience can trust that you"re a real person sharing your real stories.')}}</p>
                                </div>

                                <div class="entry_badge_text">
                                    <h5 style="font-weight: bold;"><i class="fa-solid fa-circle-check"></i>{{get_phrase('Increased account protection')}}</h5>
                                    <p>{{get_phrase('Worry less about impersonation with proactive identity monitoring.')}}</p>
                                </div>

                            </ul>
                            @php
                            $currentDate = \Carbon\Carbon::now();
                            $badge_info = \App\Models\Badge::where('user_id', Sentinel::getUser()->id)
                            ->whereDate('start_date', '<=', $currentDate)
                                ->whereDate('end_date', '>=', $currentDate)
                                ->first();
                                @endphp
                                @if($badge_info?->status == '1' && $badge_info->start_date <= now() && $badge_info->end_date >= now())
                                    <a href="javascript:;" class="btn common_btn_2 next w-100">{{get_phrase('Already purchased')}}</a>
                                    @else
                                    <a href="{{route('admin.badge.info')}}" class="btn common_btn w-100">{{get_phrase('Next')}}</a>
                                    @endif
                        </div>
                    </div>
                </div>
            </div>
            @if(count($badges))
            <div class="col-md-12 col-lg-12 col-sm-12 col-12 my-3">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <span>
                            <i class="livicon" data-name="tasks" data-size="16" data-loop="true" data-c="#fff"
                                data-hc="white"></i> {{get_phrase('Build trust with Sociopro Verified')}}
                        </span>
                        <span class="float-end">
                            <i class="fa fa-chevron-up clickable"></i>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="box-body">
                            <!-- Users Image (Centered) -->
                            <div class="badge-image text-center mb-3">
                                <img src="{{ get_user_image(Sentinel::getUser()->id, 'optimized') }}" alt="Users Image" height="80px" width="80px" class="rounded-circle img-fluid" />
                            </div>

                            <!-- Users Name (Center below the Image) -->
                            <div class="badge_info text-center mb-4">
                                <h5>{{ Sentinel::getUser()->first_name }}</h5> <!-- Users's first name -->
                            </div>

                            <!-- Content with Verified Badge and Account Protection -->
                            <div class="badge-content">
                                <div class="badge-item mb-3">
                                    <i class="fa-solid fa-circle-check text-success" style="font-size: 20px;"></i> <!-- Tick Icon -->
                                    <h5 class="d-inline-block ms-2">{{get_phrase('A Verified Badge')}}</h5>
                                    <p class="text-muted">{{get_phrase('Your audience can trust that you\'re a real person sharing your real stories.')}}</p>
                                </div>
                                <div class="badge-item mb-3">
                                    <i class="fa-solid fa-circle-check text-success" style="font-size: 20px;"></i> <!-- Tick Icon -->
                                    <h5 class="d-inline-block ms-2">{{get_phrase('Increased Account Protection')}}</h5>
                                    <p class="text-muted">{{get_phrase('Worry less about impersonation with proactive identity monitoring.')}}</p>
                                </div>
                            </div>

                            @php
                            $currentDate = \Carbon\Carbon::now();
                            $badge_info = \App\Models\Badge::where('user_id', Sentinel::getUser()->id)
                            ->whereDate('start_date', '<=', $currentDate)
                                ->whereDate('end_date', '>=', $currentDate)
                                ->first();
                                @endphp
                                @if($badge_info?->status == '1' && $badge_info->start_date <= now() && $badge_info->end_date >= now())
                                    <a href="javascript:;" class="btn common_btn_2 next w-100">{{get_phrase('Already purchased')}}</a>
                                    @else
                                    <a href="{{route('admin.badge.info')}}" class="btn common_btn w-100">{{get_phrase('Next')}}</a>
                                    @endif
                        </div>
                    </div>
                </div>
            </div>


            @endif
        </div>
    </div>
</section>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
<script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendors/datatables/js/dataTables.bootstrap5.js') }}"></script>
<script>
    $(function() {
        $('#table').DataTable();
    });
</script>
@stop

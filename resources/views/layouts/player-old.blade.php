<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>@yield('title', 'Player') </title>

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- Core Styles -->
    <link href="{{ asset('assets/css/common.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dark-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles/all-themes.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/form.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/bundles/flatpicker/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/bundles/multiselect/css/multi-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @yield('header_styles')
</head>

<body>
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30">
                <img class="loading-img-spin" src="{{ asset('assets/images/loading.png') }}" width="20" height="20" alt="loading">
            </div>
            <p>Please wait...</p>
        </div>
    </div>

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <div class="menu">

            <ul class="list">
                <li class="sidebar-user-panel active" style="display: block;">
                    <div class="user-panel">
                        <div class="image">
                            @if (!empty($player->photo))
                                <img src="{{ asset('uploads/players/' . $player->photo) }}" class="user-img-style" alt="User Image">
                            @endif
                        </div>
                    </div>
                    <div class="profile-usertitle">
                        <div class="sidebar-userpic-name"> {{ ucfirst(auth()->user()->name)}}</div>
                        <div class="profile-usertitle-job ">Player</div>
                    </div>
                </li>
                @include('players.partials.sidebar-menu-items', ['player' => $player ?? null])
            </ul>
        </div>
        <style>
            .sidebar .menu {
                height: 35vh;
            }
        </style>
        @include('partials.dashboard_reminders')
    </aside>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </section>

    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="{{ asset('assets/js/common.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/morris.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/flot-chart.min.js') }}"></script>

    <script src="{{ asset('assets/js/admin.js') }}"></script>
    <script src="{{ asset('assets/js/pages/medias/carousel.js') }}"></script>
    <script src="{{ asset('assets/js/pages/charts/jquery-knob.js') }}"></script>

    @stack('scripts')
    @yield('footer_scripts')
</body>
</html>

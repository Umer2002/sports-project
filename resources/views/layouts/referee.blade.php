<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>@yield('title', 'Referee') - Lorax Admin</title>

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

    <style>
        body.dark-mode .note-editor.note-frame {
            background-color: #2c2f3a;
            color: #ffffff;
            border-color: #444;
        }

        body.dark-mode .note-editor.note-frame .note-editing-area .note-editable {
            background-color: #2c2f3a;
            color: #ffffff;
        }

        body.dark-mode .note-editor.note-frame .note-toolbar {
            background-color: #1f2230;
            border-color: #444;
        }

        body.dark-mode .note-editor.note-frame .dropdown-menu {
            background-color: #2c2f3a;
            color: #fff;
        }

        body.dark-mode .note-editor.note-frame .dropdown-menu .dropdown-item {
            color: #fff;
        }

        body.dark-mode .note-editor.note-frame .dropdown-menu .dropdown-item:hover {
            background-color: #444;
        }
    </style>

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
                <li class="header">Referee Panel</li>
                <li class="{{ request()->routeIs('referee.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('referee.dashboard') }}">
                        <i class="zmdi zmdi-view-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('referee.availability.form') ? 'active' : '' }}">
                    <a href="{{ route('referee.availability.form') }}">
                        <i class="zmdi zmdi-calendar-check"></i>
                        <span>Availability</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('referee.matches.available') || request()->routeIs('referee.matches.view') ? 'active' : '' }}">
                    <a href="{{ route('referee.matches.available') }}">
                        <i class="zmdi zmdi-format-list-bulleted"></i>
                        <span>Available Games</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('referee.email') ? 'active' : '' }}">
                    <a href="{{ route('referee.email') }}">
                        <i class="zmdi zmdi-email"></i>
                        <span>Email</span>
                    </a>
                </li>

            </ul>
        </div>
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

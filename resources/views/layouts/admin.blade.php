<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Play2Earn</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- Core Styles -->
    <link href="{{ asset('assets/css/common.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dark-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/styles/all-themes.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/form.min.css') }}" rel="stylesheet">

    <!-- Plugin Styles -->
    <link href="{{ asset('assets/js/bundles/flatpicker/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/bundles/multiselect/css/multi-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <!-- Custom Dashboard Styling -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-dashboard.css') }}">
    <!-- Custom Dashboard Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/admin-dashboard-custom.css') }}">

    <style>
        .cards_css{
            margin: 10px 10px;
        }
        .main_cards_css {
            width: 100%;
            max-width: calc(100vw - 16px);
            margin: auto !important;
        }
    </style>

    @yield('header_styles')
</head>
<body style="overflow-x: hidden">
    <!-- Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30">
                <img class="loading-img-spin" src="{{ asset('assets/images/logo.png') }}" width="20" height="20" alt="loading">
            </div>
            <p>Please wait...</p>
        </div>
    </div>

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <section class="content" >
        <div class="container-fluid">
            @yield('content') {{-- Sections should handle their own cards --}}
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('assets/js/common.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/raphael.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/morris.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/flot-chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/select2.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>
    <script>
        setTimeout(function () {
            $(".page-loader-wrapper").css('display', 'none');
        }, 50);
    </script>

    @stack('scripts')
    @yield('footer_scripts')
    @include('partials.theme-toggle')
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>@yield('title', 'Homepage')</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">



    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .custom-login-btn {
            background-color: #0096db;
            color: #e3f400;
            font-weight: bold;
            font-size: 14px;
            font-size: 14px;
            padding: 10px 56px;
            border: none;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            margin-left: 10px;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .custom-login-btn:hover {
            background-color: #007bb2;
            text-decoration: none;
            color: #e3f400;
        }

        .custom-login-btn:hover {
            background-color: #007bb2;
        }

        .footer-logo {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Footer Styles */
        .footer-bottom {
            background-color: #000000;
            position: relative;
            overflow: hidden;
            height: 150px;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .footer-bottom::before {
            content: 'PLAY 2 EARN SPORTS';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 1561.6064453125px;
            height: 167.27272033691406px;
            font-family: 'Gilroy', Arial, sans-serif;
            font-weight: 900;
            font-style: normal;
            font-size: 100px;
            line-height: 100px;
            letter-spacing: 0%;
            text-transform: uppercase;
            color: #EEEEEE;
            opacity: 0.1;
            text-align: center;
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
        }

        .footer-bottom .container {
            position: relative;
            z-index: 1;
        }

        .footer-left {
            gap: 20px;
        }

        .footer-logo {
            max-width: 60px;
            height: auto;
        }

        .footer-left p {
            color: #ffffff;
            font-size: 16px;
            margin: 0;
        }

        @media (max-width: 768px) {
            .footer-bottom {
                height: 200px;
            }

            .footer-bottom::before {
                font-size: 80px;
                line-height: 80px;
                width: 100%;
                height: auto;
            }

            .footer-left {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 10px;
            }

            .footer-logo {
                max-width: 50px;
            }

            .footer-left p {
                font-size: 14px;
            }
        }



        .custom-login-btn:hover {
            background-color: #007bb2;
        }

        .custom-registration-btn {
            background: linear-gradient(180deg, #1f2522 0%, #121412 100%);
            color: #e3f400;
            font-weight: bold;
            font-size: 14px;
            padding: 10px 30px;
            border: none;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            /* transition: background 0.3s ease, transform 0.2s ease; */
        }

        .custom-registration-btn,
        .custom-login-btn {
            width: 150px;
            text-align: center;
            padding: 10px;
            display: inline-block;
            text-decoration: none;
        }

        /* Search Box CSS */
        /* Style the search input field */
        .custom-search-input {
            border-radius: 999px;
            font-size: 14px;
            padding-left: 10px 30px;
            width: 100px !important;
            background-color: #3e4541;
            border: none;
            /*     color: #ffffff !important; */
        }

        /* Style the search button */
        .custom-search-btn {
            border-radius: 999px;
            font-size: 14px;
            /* Adjust font size */
            background-color: #3e4541;
            /* Match the button background color */
            color: white;
            /* Button text color */
            border: none;
            /* Remove border */
            padding: 10px 30px;
            cursor: pointer;
            /* Add pointer cursor */
            display: flex;
            /* Center the icon */
            align-items: center;
            justify-content: center;
        }

        /* Wrapper to position icon inside input */
        .custom-search-wrapper {
            position: relative;
            width: 100%;
            max-width: 150px;
        }

        /* Input field styling */
        .custom-search-input {
            width: 100%;
            padding: 8px 15px;
            border-radius: 999px !important;
            background-color: #3e4541 !important;
            border: 1px solid transparent !important;
            color: #ffffff !important;
            outline: none;
            transition: all 0.3s ease;
        }

        /* Placeholder color */
        form .input-group .custom-search-input::placeholder {
            color: #ffffff;
            opacity: 0.8;
        }

        /* Search icon inside input */
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
            font-size: 14px;
            pointer-events: none;
        }

        /* Hover effect */
        .custom-search-input:hover,
        .custom-search-input:focus {
            border-color: #ffffff;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
        }

        /* Navigation links */
        .navbar-nav .nav-link {
            font-size: 14px;
            font-weight: 400;
            color: #333;
            text-decoration: none;
            transition: font-weight 0.2s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #000;
        }

        .navbar-nav .nav-link.active {
            font-weight: 600;
            color: #000;
        }

        .custom-registration-btn:hover {
            background: linear-gradient(180deg, #2c2f2d 0%, #191b1a 100%);
            transform: scale(1.03);
        }

        .footer-cta {
            background: url('{{ asset('images/theme/footer-bg.png') }}') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        /* .footer-cta .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 0;
        } */

        .footer-cta .container {
            position: relative;
            z-index: 2;
        }


        input {
            color: #000;
        }

        body {
            background: url('{{ asset("images/Group_53.png") }}') no-repeat center center fixed;
            background: url('{{ asset("images/Group_53.png") }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
        }

        .register-box {
            background: rgba(10, 10, 10, 0.85);
            border-radius: 20px;
            padding: 40px;
            max-width: 700px;
            margin: 80px auto;
        }

        .btn-signup {
            background: #00AEEF !important;
            color: #FFF700 !important;
            border-radius: 30px;
            padding: 10px 40px;
            font-weight: bold;
            border: none;
        }

        .signup-footer {
            background: url('{{ asset(' storage/signup_footer.png') }}') no-repeat center center;
            background: url('{{ asset(' storage/signup_footer.png') }}') no-repeat center center;
            background-size: cover;
            text-align: center;
            padding: 80px 20px 40px;
        }

        .signup-footer .form-control {
            max-width: 400px;
            margin: 0 auto 20px;
            border-radius: 25px;
            padding: 10px 20px;
        }


        .subscribe-group {
            max-width: 500px;
            border-radius: 50px;
            overflow: hidden;
            background-color: #4E4B4B;
        }

        .subscribe-input-container {
            position: relative;
            display: flex;
            align-items: center;
            border: 1px solid grey;
            border-radius: 50px;
            background-color: #4E4B4B;
            overflow: hidden;
            padding: 5px;
            width: 100%;
        }

        .subscribe-input:focus {
            background-color: #4E4B4B !important;
            box-shadow: none !important;
        }

        .subscribe-input {
            flex: 1;
            border: none !important;
            background: transparent !important;
            color: #8B8B8B !important;
            padding: 10px 15px !important;
            border-radius: 50px;
            outline: none;
        }

        .subscribe-input::placeholder {
            color: #8B8B8B !important;
        }

        .subscribe-btn {
            border: none !important;
            background-color: #0A92D1 !important;
            color: #D5F40B !important;
            padding: 10px 20px !important;
            border-radius: 50px !important;
            cursor: pointer;
            font-weight: bold !important;
            font-family: 'Gilroy', sans-serif;
        }

        .subscribe-btn:hover {
            background-color: darkblue !important;
            color: #fff !important;
        }

        .subscribe-btn:focus {
            outline: none;
        }


        .footer-bottom {
            background-color: #000;
            color: #fff;
        }

        .footer-logo {
            width: 60px;
            height: auto;
        }

        .signup-btn {
            background-color: #00aaff;
            color: #e3f400;
            font-weight: bold;
            padding: 10px 25px;
            border-radius: 999px;
            text-transform: uppercase;
            border: none;
        }

        .logo-container {
            position: relative;
            display: inline-block;
            bottom: 10px;
        }

        .logo {
            display: block;
            position: relative;
            width: 90px;
            height: auto;
            z-index: 1;
        }

        .logo-container::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0%;
            right: 0%;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.5);
            z-index: 0;
            border-radius: 20px;
            filter: blur(10px);
            box-shadow: 0px 0px 20px 8px rgba(255, 255, 255, 0.3);
        }

        /* Footer Logo */
        .footer-logo-container {
            position: relative;
            display: inline-block;
            bottom: 10px;
        }

        .footer-logo {
            display: block;
            position: relative;
            width: 90px;
            height: auto;
            z-index: 1;
        }

        .footer-logo-container::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0%;
            right: 0%;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.5);
            z-index: 0;
            border-radius: 20px;
            filter: blur(10px);
            box-shadow: 0px 0px 20px 8px rgba(255, 255, 255, 0.3);
        }

        .footer-bottom .container p {
            text-align: left !important;
            margin: 0;
            flex: 1;
            padding: 20px;
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>

<body>

    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Brand/Logo -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <div class="logo-container">
                    <img class="logo" src="{{ asset('images/theme/logo.png') }}" alt="Play2Earn">
                </div>
            </a>

            <!-- Toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="mainNav">
                <!-- Search form -->
                <form class="d-flex my-3 my-lg-0 ms-lg-3" method="GET" action="{{ url('/search') }}">
                    <div class="input-group custom-search-wrapper">
                        <input class="form-control custom-search-input" type="search" name="q"
                            placeholder="Search" aria-label="Search">
                        <span class="search-icon">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </form>

                <!-- Navigation links -->
                <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                            href="{{ url('/') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tournaments.search') ? 'active' : '' }}"
                            href="{{ route('tournaments.search') }}">Tournament Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clubs.search') ? 'active' : '' }}"
                            href="{{ route('clubs.search') }}">Club Search</a>
                    </li>
                </ul>

                <!-- Auth buttons -->
                @auth
                    <form method="POST" action="{{ route('logout') }}" class="d-flex ms-lg-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">Logout</button>
                    </form>
                @else
                    <div class="d-flex ms-lg-3 align-items-center gap-2">
                        <a href="{{ route('register') }}" class="custom-registration-btn">Registration</a>
                        <a href="{{ route('login') }}" class="custom-login-btn">Login</a>
                    </div>
                    {{--
        If you later want a dropdown for registration types, use this:
        <div class="dropdown ms-lg-3">
          <a class="custom-registration-btn dropdown-toggle" href="#" id="registrationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Registration
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="registrationDropdown">
            <li><a class="dropdown-item" href="{{ route('register.player') }}">As Player</a></li>
            <li><a class="dropdown-item" href="{{ route('register.club') }}">As Club</a></li>
            <li><a class="dropdown-item" href="{{ route('register.ambassador') }}">As Ambassador</a></li>
            <li><a class="dropdown-item" href="{{ route('register.college') }}">As College / University</a></li>
          </ul>
        </div>
        --}}
                @endauth
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <main class="container-fluid">
        <div class="row justify-content-center position-relative">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <!-- Footer CTA Section -->
    <section class="footer-cta text-white position-relative">
        <div class="overlay"></div>
        <div class="container py-5 text-center position-relative">
            <h2 class="fw-bold display-5">JOIN OUR ONLINE <br> COMMUNITY TODAY</h2>
            <p class="mt-3 mb-4">Create a profile that showcases your child’s talent.</p>
            <div class="input-group subscribe-group mx-auto">
                <!-- <input type="email" class="form-control subscribe-input" placeholder="Email Here...">
                <button class="btn subscribe-btn">SUBSCRIBE</button> -->
                <div class="subscribe-input-container">
                    <input type="email" class="form-control subscribe-input" placeholder="Email Here...">
                    <button class="btn subscribe-btn">SUBSCRIBE</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Bottom Bar -->
    <footer class="footer-bottom text-white py-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <!-- <img src="{{ asset('storage/theme/logo-footer.png') }}" alt="Logo" class="footer-logo mb-3 mb-md-0"> -->
            <div class="footer-logo-container footer-left d-flex">
                <img src="{{ asset('images/theme/logo.png') }}" alt="Logo" class="footer-logo">
            </div>
            <p class="mb-3 mb-md-0 text-start">info@play2earnsports.com</p>
            <a href="{{ route('register') }}" class="custom-login-btn">SIGN UP NOW</a>
        </div>
    </footer>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    @yield('scripts')

</body>

</html>
@include('partials.theme-toggle')

</html>

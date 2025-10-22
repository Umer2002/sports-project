@extends('layouts.default')
@section('title', 'Homepage')

@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/home.css" />
    <style>
        .info-cards-wrapper {
            background: url('{{ asset('storage/theme/bg-info-card.png') }}') no-repeat center center;
            background-size: cover;
            padding-bottom: 132px;
            width: 100%;
            color: white;
            /* max-width: 1200px; */
            margin: 0 auto;
            padding: 132px;
        }

        .all-sports-section {
            background: url('{{ asset('storage/theme/bg-all-sp.png') }}') no-repeat center center;
            background-size: cover;
            width: 100%;
            color: white;
            height: 1071px;
            padding-top: 250px !important;

        }

        .welcome-section {
            background: white;
        }

        .about-us-section {
            background: white;
            padding: 0;
            margin: 0;
        }

        .info-card-row-main {
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-card {
            background: rgba(0, 0, 0, 0.8);
            /* darker semi-transparent for readability */
            padding: 30px 25px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            height: 100%;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        /* .info-card h5 {
                                                                    font-weight: 700;
                                                                    margin-bottom: 20px;
                                                                    font-size: 1.25rem;
                                                                    background: rgba(255, 255, 255, 0.1);
                                                                    padding: 12px 20px;
                                                                    display: inline-block;
                                                                    border-radius:
                                                                } */



        .info-card h5 {
            font-weight: 700;
            margin-bottom: 15px;
            position: absolute;
            top: -26%;
            background: #252c29;
            /* margin-bottom: 10px; */
            width: 67%;
            padding: 15px 14px;
            border-radius: 25px;
            left: 1%;
        }

        .player-img {
            max-width: 100px;
            position: absolute;
            bottom: 0;
        }

        .player-left {
            left: -13%;
            height: 470px;
            bottom: -100%;
        }

        .player-right {
            right: -18%;
            height: 370px;
            bottom: -100%;
        }

        .position-relative {
            position: relative;
        }

        @media (max-width: 768px) {
            .player-img {
                display: none;
            }

            .info-card {
                margin-bottom: 100px;
            }

            .info-card h5 {
                width: 85%;
                top: -11%;
                left: 0%;
                ;
            }

            .welcome-section h1 {
                text-align: center;
            }

            .all-sports-section {
                padding-top: 10px !important;
                min-height: 900px;
                height: auto;
            }
        }

        .all-sports {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 5%;
            padding: 30px;
            background-color: #1f2522;
        }

        .img-container {
            height: 60px;
            width: 60px;
            /* border: 1px solid; */
            position: absolute;
            top: -41%;
            left: 40%;
            border-radius: 50%;
            background-color: black;
            padding: 14px;
        }

        .img-container img {
            height: 30px;
            width: 30px;
        }

        .sports-banner {
            background: url('{{ asset('img/group-210.png') }}') no-repeat center center;
            margin-top: 0px;
            color: rgb(7, 7, 7);
            /* padding: 20px; */
            text-align: center;
            margin-top: 0;
            /* background-repeat: no-repeat; */
            min-height: 1057px;

        }

        .custom-sports-card {
            background-color: white;
            border-radius: 8%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            border: 5px solid #000;
            min-height: 426px;
            width: 250px;
            height: 450px;
        }

        .card-title {
            font-size: 250%;
            /* padding: 20px 10px 10px; */
            font-weight: 900;
            color: #000;
            text-align: center;
        }

        .custom-sports-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .custom-enroll-btn {
            background-color: #2196f3;
            color: #00e676;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        .sp {
            background: #000;
            height: 15px;
            display: block;
            width: 20%;
            border-radius: 7px;
            margin: 13px auto;
        }

        .custom-enroll-btn:hover {
            background-color: #1976d2;
        }

        .carousel-container {
            overflow: hidden;

            margin-top: 362px;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease;
            width: 70%;
            margin: 0 auto;
        }

        .carousel-item-custom {
            flex: 0 0 25%%;
            width: 300px;
            margin: 0 auto;
            /* padding: 30px; */
        }

        @media (width: 2560px) {
            .sports-banner {
                background-size: 100% !important;
                padding: 290px !important;
            }

            .info-cards-wrapper {
                padding: 300px !important;
            }
        }


        @media (max-width: 768px) {
            .carousel-item-custom {
                flex: 0 0 50%;
            }
        }

        @media (max-width: 1024px) {
            .sports-banner {
                background-size: 100% !important;
                padding: 290px !important;
                max-height: 600px;
                min-height: 0px !important;
            }

            .sports-banner h2 {
                padding: 0 !important;
                margin: 0;
                /* min-width: 0px; */
                position: absolute;
                top: 5px;
            }

            .sports-banner h1 {
                padding: 0 !important;
                margin: 0;
                /* min-width: 0px; */
                position: absolute;
                top: 95px;
            }

            .info-cards-wrapper {
                padding: 300px !important;
            }

            .carousel-container {
                width: 1000px;
                margin: 0px;
                padding: 0px;
                float: left;
                margin: -100px;
            }

            .info-cards-wrapper {
                /* background-size: cover; */
                background-position: center;
                padding: 70px 20px !important;
                min-height: 600px !important;
                background-size: 110%;
            }
        }

        @media (max-width: 991.98px) {
            .info-cards-wrapper {
                /* background-size: cover; */
                background-position: center;
                padding: 100px 20px;
                min-height: 600px;
                background-size: 110%;
            }

            .info-card h5 {
                width: 100%;
                left: 0;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .carousel-item-custom {
                flex: 0 0 80%;
                margin: 0 10%;
            }

            .info-cards-wrapper {
                padding: 60px 20px !important;
            }

            .info-card h5 {
                font-size: 1rem;
                top: -14%;
                width: 100%;
                left: 0;
                padding: 10px 12px;
                text-align: center;
            }

            .info-card {
                padding: 20px 15px;
            }

            .info-cards-wrapper {
                padding: 60px 10px;
                background-size: 110%;
                background-position: center top;
                min-height: 600px;
            }
        }

        /* Phone Card Styles - Design like image 2 */
        .phone-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 200px;
            flex: 0 0 200px;
            gap: 15px;
            position: relative;
            border-radius: 25px;
            padding: 8px;
            background: linear-gradient(135deg, #0A0C0B 6.25%, #06B6D4 53.37%, #868C6C 90.87%);
        }

        .phone-frame {
            width: 100%;
            height: 360px;
            background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 50%, #0f1411 100%);
            border-radius: 17px;
            position: relative;
            padding: 20px 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .phone-notch {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 20px;
            background: #000;
            border-radius: 10px;
            z-index: 2;
        }

        .phone-notch::after {
            content: '';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: #333;
            border-radius: 50%;
        }

        .phone-main-content {
            width: 100%;
            height: 200px;
            background: linear-gradient(180deg, #3a3a3a 0%, #2a2a2a 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 25px;
            position: relative;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.3);
            flex: 1;
            overflow: hidden;
        }

        .phone-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .content-text {
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .replace-text {
            color: #B8860B;
            font-weight: bold;
            font-size: 18px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .with-blog-text, .image-text {
            color: #00CED1;
            font-weight: 500;
            font-size: 16px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .phone-white-area {
            width: 100%;
            height: 40px;
            background: #ffffff;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .phone-gradient-area {
            width: 100%;
            height: 40px;
            background: linear-gradient(90deg, #00CED1 0%, #1E90FF 100%);
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .phone-gradient-area.enroll-button {
            cursor: pointer;
        }

        .phone-gradient-area.enroll-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 206, 209, 0.4);
            text-decoration: none;
        }

        .enroll-text {
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Responsive adjustments for phone cards */
        @media (max-width: 768px) {
            .phone-card {
                max-width: 160px;
                flex: 0 0 160px;
            }
            
            .phone-frame {
                width: 150px;
                height: 320px;
                padding: 15px 12px;
            }
            
            .phone-main-content {
                height: 170px;
                margin-top: 20px;
            }
            
            .replace-text {
                font-size: 16px;
            }
            
            .with-blog-text, .image-text {
                font-size: 14px;
            }
            
            .phone-white-area, .phone-gradient-area {
                height: 35px;
            }
            
            .enroll-text {
                font-size: 12px;
            }
        }

        @media (max-width: 576px) {
            .phone-card {
                max-width: 140px;
                flex: 0 0 140px;
            }
            
            .phone-frame {
                width: 130px;
                height: 280px;
                padding: 12px 10px;
            }
            
            .phone-main-content {
                height: 150px;
                margin-top: 18px;
            }
            
            .replace-text {
                font-size: 14px;
            }
            
            .with-blog-text, .image-text {
                font-size: 12px;
            }
            
            .phone-white-area, .phone-gradient-area {
                height: 30px;
            }
            
            .enroll-text {
                font-size: 11px;
            }
        }
    </style>

    <section class="hero" id="hero">
        <!-- center logo -->
        <div class="bg-center_image">
            <img src="images/mobile-header.png" alt="Logo" class="mobile-hero-center-image">
            <img src="images/hero-center-image.png" alt="Logo" class="m-hide hero-center-image">
            <!-- background players / images as separate layers -->
            <img src="images/left1.png" class="m-hide hero-layer breathe  left1" alt="">
            <img src="images/left2.png" class="m-hide hero-layer left2 left-right" alt="">
            <img src="images/basketball.png" class="m-hide basketball rotate-sway " alt="">
            <img src="images/right3.png" class="m-hide hero-layer right3" alt="">
            <img src="images/sponser-text.png" alt="Logo" class="m-hide top-left">
            <img src="images/player-enrol-text.png" alt="Logo" class="m-hide center-right">
            <img src="images/left-big.png" alt="Logo" class="m-hide left-big hero-layer">
            <img src="images/football.png" class="m-hide hero-layer football" alt="">
            <img src="images/pngtree-golf-ball-left-top.png" class=" m-hide pngtree-golf-ball-left-top" alt="">
            <img src="images/hero-center-image-layer.png" alt="Logo"
                class="m-hide up-down  hero-center-image-layer hero-layer">
            <img src="images/right2.png" alt="Logo" class="m-hide right2 hero-layer">
            <img src="images/right1.png" class="m-hide right1" alt="">
            <div class="hero-logo">
                <img src="images/logo.png" alt="Logo" class=" ">
                <a href="{{ route('register') }}" class="blue-btn btn">Enroll Now</a>
            </div>
        </div>
        <!-- cards -->
        <div class="cards-section grid">
                @foreach (($top_blogs ?? collect())->take(5) as $blog)
                    @php
                        // Resolve image URL with a safe fallback
                        $img = $blog->image
                            ? (Str::startsWith($blog->image, ['http://', 'https://'])
                                ? $blog->image
                                : (Storage::disk('public')->exists('blogs/' . $blog->image)
                                    ? Storage::url('blogs/' . $blog->image)
                                    : asset('images/card-inner-place-holder.png')))
                            : asset('images/card-inner-place-holder.png');

                        // Short text preview
                        $excerpt = Str::limit(strip_tags($blog->content ?? ''), 110);
                    @endphp

                    <div class="phone-card">
                        <div class="phone-frame">
                            <!-- Phone notch -->
                            <div class="phone-notch"></div>
                            
                            <!-- Main content area with image -->
                            <div class="phone-main-content">
                                <img src="{{ $img }}" alt="Blog Image" class="phone-image">
                            </div>
                            
                            <!-- White rectangle -->
                            <div class="phone-white-area"></div>
                            
                            <!-- Gradient rectangle with Enroll Now button -->
                            <a href="{{ route('register') }}" class="phone-gradient-area enroll-button">
                                <span class="enroll-text">Enroll Now</span>
                            </a>
                        </div>
                    </div>
                @endforeach

        <!-- FLAGS MARQUEE -->
        <div class="flags-wrap">
            <div class="flags">
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
            </div>
            <div class="flags">
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
                <!-- repeat same sequence for infinite scroll effect -->
                <img src="images/flag1.png" alt="Logo">
                <img src="images/flag2.png" alt="Logo">
                <img src="images/flag3.png" alt="Logo">
                <img src="images/flag4.png" alt="Logo">
                <img src="images/flag5.png" alt="Logo">
            </div>
        </div>
    </section>

    <!-- CTA -->







    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

@endsection

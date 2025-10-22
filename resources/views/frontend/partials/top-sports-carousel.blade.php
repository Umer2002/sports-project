<div class="sports-banner">
    <style>
        .sports-banner {
            position: relative;
            background: url('{{ asset('img/group-210.png') }}') no-repeat center center;
            background-size: cover;
            min-height: 1050px;
            padding: 60px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 1;
        }

        .carousel-wrapper {
            max-width: 100%;
            overflow-x: hidden;
            margin-top: 40px;
            position: relative;
            z-index: 2;
        }

        .carousel-container {
            padding: 0 60px;
            margin-top: 245px;
        }

        #sportsTrack {
            display: flex;
            gap: 30px;
            overflow-x: auto;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #sportsTrack::-webkit-scrollbar {
            display: none;
        }

        .carousel-item-custom {
            flex: 0 0 auto;
            width: 250px;
            padding-bottom: 20px;
        }

        .custom-sports-card {
            background-color: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            height: 100%;
            padding-bottom: 20px;
            transition: transform 0.3s ease;
            border: 8px solid black;
            width: 300px;
        }
        .sp{
            background: #000;
            height: 15px;
            display: block;
            width: 20%;
            border-radius: 7px;
            margin: 13px auto;
        }

        .custom-sports-card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 900;
            text-align: center;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }

        .img-container-sp {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
        }

        .img-container-sp img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .card-body {
            text-align: center;
            padding: 15px;
            color: #444;
            font-size: 0.9rem;
        }

        .custom-enroll-btn {
            background-color: #00aaff;
            color: white;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 50px;
            margin-top: 10px;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            display: inline-block;
        }

        .custom-enroll-btn:hover {
            background-color: #007ac9;
        }
        @media (max-width: 768px) {
            .carousel-container {
                margin-top: 100px;
            }
            .carousel-item-custom {
                width: 100%;
            }
            .custom-sports-card {
                width: 100%;
            }
            .img-container-sp {
                height: 150px;
            }
        }
    </style>

    <h2 class="fw-bold">SPORTS <span class="text-warning">SPONSORSHIP</span></h2>
    <h1 class="fw-bolder display-5">PLAY 2 EARN SPORTS</h1>
    <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill mt-3">CLUB REGISTRATIONS</a>

    <div class="carousel-wrapper">
        <div class="carousel-container">
            <div class="carousel-track d-flex" id="sportsTrack">
                @foreach ($top_sports as $sport)
                    <div class="carousel-item-custom">
                        <div class="custom-sports-card">
                            <div class="card-title"><span class="sp"></span>{{ $sport->name }}</div>
                            <div class="img-container-sp">
                                <img src="{{ asset('storage/' . $sport->icon_path) }}" alt="{{ $sport->name }}">
                            </div>
                            <div class="card-body text-center px-3">
                                <p class="card-text">{{ $sport->description }}</p>
                                <a href="{{ route('register') }}" class="custom-enroll-btn">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

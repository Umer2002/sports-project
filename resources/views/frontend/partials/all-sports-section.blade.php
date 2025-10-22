<div class="container-fluid py-5 group-26 all-sports-section">
    <style>
        .all-sports-section {
            background: url('{{ asset('storage/theme/bg-all-sp.png') }}') no-repeat center center;
            background-size: cover;
            padding: 60px 0;
            width: 100%;
            color: white;
            /* height: 1071px; */
        }

        .all-sports {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 5%;
            padding: 30px;
            background-color: #1f2522;
        }
        .sp {
            background: #000;
            height: 15px;
            display: block;
            width: 20%;
            border-radius: 7px;
            margin: 13px auto;
        }
        .img-container {
            height: 60px;
            width: 60px;
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

        .carousel-container {
            overflow-x: hidden;
            max-width: 100%;
            position: relative;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-item-custom {
            flex: 0 0 25%;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .carousel-item-custom {
                flex: 0 0 50%;
            }
        }

        @media (max-width: 576px) {
            .carousel-item-custom {
                flex: 0 0 80%;
                margin: 0 10%;
            }
        }
    </style>

    <div class="all-sports">
        @foreach ($sports->chunk(4) as $chunk)
            <div class="row mb-3">
                @foreach ($chunk as $sport)
                    <div class="col-md-3 text-center mt-5">
                        <div class="p-3 border rounded-4 bg-dark text-white position-relative">
                            <div class="img-container">
                                <img src="{{ asset('storage/' . $sport->icon_path) }}" alt="{{ $sport->name }}"
                                     class="mb-3 img-fluid rounded-circle">
                            </div>
                            <span class="sp"></span>
                            <h5 class="fw-bold">{{ $sport->name }}</h5>

                            <a href="{{ route('register') }}"
                               class="btn btn-outline-warning btn-sm mt-2 rounded-pill custom-enroll-btn">ENROLL NOW</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

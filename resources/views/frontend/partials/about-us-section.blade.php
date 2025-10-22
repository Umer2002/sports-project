<section class="about-us-section py-5">
    <style>
        .about-us-section {
            background: white;
            padding: 0;
            margin: 0;
        }
    </style>

    <div class="container">
        <div class="row align-items-center p-4">
            <!-- Left Image -->
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="{{ asset('storage/theme/aboutus.png') }}" alt="Enhanced Player Profiles"
                     class="img-fluid rounded">
            </div>

            <!-- Right Content -->
            <div class="col-md-6 text-md-start text-center">
                <h2 class="fw-bold mb-3">ABOUT US</h2>
                <p class="text-muted">
                    Play 2 Earn Sports is an innovative platform designed to revolutionize the way athletes engage with
                    clubs, fans, and their sporting careers. It provides athletes with opportunities to join clubs,
                    create detailed profiles, and earn support through fan donations, performance recognition, and other
                    non-monetary incentives. The platform emphasizes community involvement and player development,
                    enabling athletes to showcase their talents while receiving financial and logistical support to
                    grow.
                </p>
                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 py-2 mt-3 fw-bold"
                   style="background-color: #00aaff; border: none;">
                    CLUB REGISTRATION
                </a>
            </div>
        </div>
    </div>
</section>

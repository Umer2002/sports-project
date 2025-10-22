<div class="row info-cards-wrapper">
    <style>
        .info-cards-wrapper {
            background: url('{{ asset('storage/theme/bg-info-card.png') }}') no-repeat center center;
            background-size: cover;
            padding: 60px 0;
            width: 100%;
            color: white;
            margin: 0 auto;
        }
        .info-card-row-main {
            max-width: 1200px;
            margin: 0 auto;
        }
        .info-card {
            background: rgba(0, 0, 0, 0.8);
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
        .info-card h5 {
            font-weight: 700;
            margin-bottom: 15px;
            position: absolute;
            top: -26%;
            background: #252c29;
            width: 67%;
            padding: 15px 14px;
            border-radius: 25px;
            left: 1%;
        }
        @media (max-width: 768px) {
            .info-card {
                margin-bottom: 100px;
            }
            .info-card h5 {
                width: 85%;
                top: -11%;
                left: 0%;
            }
        }
    </style>

    <div class="container mb-5">
        <div class="row py-4 info-card-row-main">
            <div class="col-md-6 mb-5">
                <div class="info-card">
                    <h5>ALL AGES BOYS & GIRLS</h5>
                    <p>A dynamic platform for boys and girls of all ages to join clubs, showcase their talent, and earn
                        rewards while playing the sports they love!</p>
                </div>
            </div>
            <div class="col-md-6 mb-5">
                <div class="info-card">
                    <h5>CLUB AND PLAYER NETWORK</h5>
                    <p>Empowering clubs and players with sponsorship opportunities—connect, compete, and earn support to
                        fuel your athletic journey!</p>
                </div>
            </div>
            <div class="col-md-6 mb-5">
                <div class="info-card">
                    <h5>PLAY FOR REWARDS</h5>
                    <p>Play your favorite sports, showcase your skills, and earn exciting rewards—where passion meets
                        opportunity!</p>
                </div>
            </div>
            <div class="col-md-6 mb-5">
                <div class="info-card">
                    <h5>EARN REAL CASH</h5>
                    <p>Turn your skills into earnings! Play, compete, and earn real cash rewards while enjoying the
                        sports you love.</p>
                </div>
            </div>
        </div>
    </div>
</div>

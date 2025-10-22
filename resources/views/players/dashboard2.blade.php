@include('players.partials.head')

<link href="https://fonts.cdnfonts.com/css/gilroy-bold" rel="stylesheet">

<style>
    .gap-20rem {
        gap: 20rem;
    }
</style>

<body>
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30">
                <img class="loading-img-spin" src="assets/images/loading.png" width="20" height="20" alt="admin">
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    {{-- @include('players.partials.navbar') --}}
    <!-- #Top Bar -->
    <div>
        <!-- Left Sidebar -->
        @include('players.partials.sidebar2')
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        @include('players.partials.rightsidebar')
        <!-- #END# Right Sidebar -->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap align-items-start breadcrumb breadcrumb-style p-3" style="gap: 4rem;">

                            <!-- Weather Info -->
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <img src="assets/images/player/football.png" width="162" height="164" class="img-fluid">
                                <span class="fs-5" style="font-size: 32px !important;">13°</span>
                                <img src="assets/images/player/rain.png" width="127.42" height="127.42" class="img-fluid">
                                <div class="d-flex flex-column ms-2">
                                    <span class="fw-semibold" style="font-size: 13px !important;">H:16° L:8°</span>
                                    <span class="text-muted">Ottawa, Canada</span>
                                </div>
                            </div>

                            <!-- Share Profile -->
                            <div class="d-flex flex-column" style="margin-top: 44px;">
                                <span class="fw-semibold mb-2">Share Profile</span>
                                <div class="d-flex flex-wrap gap-2">absolute
                                    <img src="assets/images/player/fb.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/insta.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/tiktok.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/snap.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/pixart.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/link.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/cup.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/x.png" width="30" height="20" class="img-fluid">
                                    <img src="assets/images/player/youtube.png" width="30" height="20" class="img-fluid">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Covage -->

            <div class="row">
                <div class="col-lg-3 col-sm-6">

                    <div class="position-relative text-center" style="width: 100%; max-width: 400px; margin: 0 auto; height:550px;">
                        <img src="assets/images/player/cover_covage.png" class="img-fluid w-100">

                        <!-- Responsive overlay using vw/vh (relative to viewport) -->
                        <img src="assets/images/player/covage.png" class="position-absolute img-fluid"
                            style="top: 7vh; left: 3vw; width: 60%; max-width: 300px;">
                    </div>

                    <!-- Awards -->
                    <div class="container mt-3">
                        <div class="row justify-content-center">
                            <!-- Award Item -->
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 96.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>

                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>

                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-4 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>



                            <!-- Add more rows below by repeating col-3s in groups of 4 -->
                        </div>
                    </div>


                </div>

                <div class="col-lg-6 col-sm-6">
                    <div class="counter-box text-center white">
                        <div class="position-relative text-center" style="max-width: 1256px; margin: 0 auto; height: 500px;">
                            <!-- Background image -->
                            <img src="assets/images/player/bg.jpg"
                                class="img-fluid w-100 h-100"
                                style="border-radius: 40px; opacity: 0.16;">

                            <!-- Overlayed text -->
                            <div class="position-absolute"
                                style="top: 25px; left: 10px; font-family: Gilroy, sans-serif; font-weight: 700; font-size: 43px; line-height: 52px; letter-spacing: 0; color: white;">
                                Zinedine<br>Zidane
                            </div>

                            <!-- Midfielder Badge (Left) -->
                            <div class="position-absolute d-flex align-items-center"
                                style="top: 170px; left: 10px; width: 110px; height: 36px; border-radius: 8px;
                                padding: 6px 8px; background: #FFFFFF0D; gap: 8px;">
                                <img src="assets/images/player/Soccer.png" alt="Midfielder Icon" style="width: 18px; height: 18px;">
                                <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                    font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                    Midfielder
                                </div>
                            </div>

                            <!-- Right-Foot Badge (Right) -->
                            <div class="position-absolute d-flex align-items-center"
                                style="top: 170px; left: 130px; width: 110px; height: 36px; border-radius: 8px;
                             padding: 6px 8px; background: #FFFFFF0D; gap: 8px;">
                                <img src="assets/images/player/Soccer.png" alt="Right Foot Icon" style="width: 18px; height: 18px;">
                                <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                                 font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                    Right-Foot
                                </div>
                            </div>

                            <div class="position-absolute d-flex align-items-center"
                                style="top: 230px; left: 10px; width: 200px; height: 40px; border-radius: 8px;
                                  padding: 8px 10px; gap: 8px; ">
                                <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                    font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                    INTL CAREER - 2000 - 2014
                                </div>
                            </div>

                            <!-- Container -->
                            <div class="position-absolute"
                                style="top: 280px; left: 8px; width: 240px; height: 205px; border-radius: 40px; background: #FFFFFF14;">

                                <!-- BORN + DATE Row -->
                                <div class="d-flex align-items-center"
                                    style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                    <!-- Label -->
                                    <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                                  font-size: 16px; line-height: 24px; color: white;">
                                        BORN
                                    </div>
                                    <!-- Value -->
                                    <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                                          font-size: 16px; line-height: 24px; color: white;">
                                        23-06-1972
                                    </div>
                                </div>

                                <!-- Divider Line -->
                                <div style="width: 194px; height: 1px; background-color: #FFFFFF30; margin: 12px 20px 0 20px;"></div>

                                <!-- BORN + DATE Row -->
                                <div class="d-flex align-items-center"
                                    style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                    <!-- Label -->
                                    <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                            font-size: 16px; line-height: 24px; color: white;">
                                        AGE
                                    </div>
                                    <!-- Value -->
                                    <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                                     font-size: 16px; line-height: 24px; color: white;">
                                        52 YEARS
                                    </div>
                                </div>
                                <!-- Divider Line -->
                                <div style="width: 194px; height: 1px; background-color: #FFFFFF30; margin: 12px 20px 0 20px;"></div>


                                <!-- BORN + DATE Row -->
                                <div class="d-flex align-items-center"
                                    style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                    <!-- Label -->
                                    <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                         font-size: 16px; line-height: 24px; color: white;">
                                        NATIONALITY
                                    </div>
                                    <!-- Value -->
                                    <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                       font-size: 16px; line-height: 24px; color: white;">
                                        FRANCE
                                    </div>
                                </div>

                            </div>



                            <div class="position-absolute"
                                style="top: 10px; left: 190px; font-family: Gilroy, sans-serif; font-weight: 900;
                                    font-size: 200px; line-height: 150px;
                                    mix-blend-mode: soft-light; color: #EBE8E3; pointer-events: none;">
                                10
                            </div>
                            <!-- Player Image -->
                            <div class="position-absolute" style="top: 100px; left: 160px;">
                                <img src="assets/images/player/man.png" alt="Player Image"
                                    style="height: 400px; object-fit: contain;">
                            </div>
                            <div class="position-absolute d-inline-flex align-items-center justify-content-center"
                                style="top: 15px; left: 420px; width: 88px; height: 33px;
                                       border-radius: 40px;
                                               background: linear-gradient(180deg, #27417C 0%, #081C49 100%);
                                               font-family: Gilroy, sans-serif; font-size: 14px; color: white;">
                                Stats
                            </div>

                            <!-- Stats Container -->
                            <div class="position-absolute"
                                style="top: 55px; left: 410px; width: 300px; height: 95px; border-radius: 30px;
                                background: linear-gradient(180deg, #27417C 0%, #081C49 100%); padding: 12px 0;">

                                <!-- Labels Row -->
                                <div class="d-flex justify-content-around align-items-center px-2 mb-2" style="gap: 12px;">
                                    <div class="text-white text-center"
                                        style="width: 48px; height: 20px; font-family: Gilroy, sans-serif;
                                               font-weight: 600; font-size: 14px; line-height: 20px; border-radius: 10px;">
                                        PLAYED
                                    </div>
                                    <div class="text-white text-center"
                                        style="width: 48px; height: 20px; font-family: Gilroy, sans-serif;
                                             font-weight: 600; font-size: 14px; line-height: 20px; border-radius: 10px;">
                                        GOAL
                                    </div>
                                    <div class="text-white text-center"
                                        style="width: 48px; height: 20px; font-family: Gilroy, sans-serif;
                                          font-weight: 600; font-size: 14px; line-height: 20px; border-radius: 10px;">
                                        ASSIST
                                    </div>
                                </div>

                                <!-- Numbers Row -->
                                <div class="d-flex justify-content-around align-items-center px-2" style="gap: 12px;">
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="width: 54px; height: 44px; color: #D5F40B;
                                               font-family: Gilroy, sans-serif; font-weight: 500;
                                               font-size: 36px; line-height: 44px; border-radius: 10px;">
                                        102
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="width: 54px; height: 44px; color: #D5F40B;
                                                  font-family: Gilroy, sans-serif; font-weight: 500;
                                                  font-size: 36px; line-height: 44px; border-radius: 10px;">
                                        32
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center"
                                        style="width: 54px; height: 44px; color: #D5F40B;
                                     font-family: Gilroy, sans-serif; font-weight: 500;
                                     font-size: 36px; line-height: 44px; border-radius: 10px;">
                                        56
                                    </div>
                                </div>

                                <!-- Real Madrid -->
                                <div class="position-absolute d-flex align-items-center"
                                    style="top: 125px; left: 10px; width: 140px; height: 40px; border-radius: 8px;
                                     padding: 8px 10px; gap: 8px; ">
                                    <img src="assets/images/player/clock.png" alt="Real Madrid Icon" style="width: 24px; height: 24px;">
                                    <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                       font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                        Real Madrid
                                    </div>
                                </div>

                                <!-- Juventus -->
                                <div class="position-absolute d-flex align-items-center"
                                    style="top: 125px; left: 160px; width: 130px; height: 40px; border-radius: 8px;
                                         padding: 8px 10px; gap: 8px; ">
                                    <img src="assets/images/player/juv.png" alt="Juventus Icon" style="width: 24px; height: 24px;">
                                    <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                           font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                        Juventus
                                    </div>
                                </div>

                                <div class="position-absolute d-flex align-items-center"
                                    style="top: 180px; left: 50px; width: 200px; height: 40px; border-radius: 8px;
                                                  padding: 8px 10px; gap: 8px; ">
                                    <div style="font-family: Gilroy, sans-serif; font-weight: 400;
                                                     font-size: 14px; line-height: 18px; color: #FFFFFF;">
                                        JERSEY NUMBER - 10
                                    </div>
                                </div>

                                <!-- Container -->
                                <div class="position-absolute"
                                    style="top: 225px; left: 70px; width: 240px; height: 205px; border-radius: 40px; background: #FFFFFF14;">

                                    <!-- BORN + DATE Row -->
                                    <div class="d-flex align-items-center"
                                        style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                        <!-- Label -->
                                        <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                         font-size: 16px; line-height: 24px; color: white;">
                                            HEIGHT
                                        </div>
                                        <!-- Value -->
                                        <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                        font-size: 16px; line-height: 24px; color: white;">
                                            1.85 M
                                        </div>
                                    </div>

                                    <!-- Divider Line -->
                                    <div style="width: 194px; height: 1px; background-color: #FFFFFF30; margin: 12px 20px 0 20px;"></div>

                                    <!-- BORN + DATE Row -->
                                    <div class="d-flex align-items-center"
                                        style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                        <!-- Label -->
                                        <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                             font-size: 16px; line-height: 24px; color: white;">
                                            WEIGHT
                                        </div>
                                        <!-- Value -->
                                        <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                      font-size: 16px; line-height: 24px; color: white;">
                                            78 KG
                                        </div>
                                    </div>
                                    <!-- Divider Line -->
                                    <div style="width: 194px; height: 1px; background-color: #FFFFFF30; margin: 12px 20px 0 20px;"></div>


                                    <!-- BORN + DATE Row -->
                                    <div class="d-flex align-items-center"
                                        style="margin-top: 20px; margin-left: 20px; gap: 80px;">
                                        <!-- Label -->
                                        <div style="width: 43px; height: 24px; font-family: Gilroy; font-weight: 700;
                                            font-size: 16px; line-height: 24px; color: white;">
                                            DEBUT
                                        </div>
                                        <!-- Value -->
                                        <div style="width: 82px; height: 24px; font-family: Gilroy; font-weight: 400;
                                           font-size: 16px; line-height: 24px; color: white;">
                                            2000
                                        </div>
                                    </div>

                                </div>

                            </div>



                        </div>
                    </div>

                    <div class="container mt-4">
                        <div class="row justify-content-center">
                            <!-- Award Item -->
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>

                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>

                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>
                            <div class="col-2 d-flex justify-content-center mb-4">
                                <img src="assets/images/player/awards.png" alt="Award" style="width: 90.5px; height: 90.5px;">
                            </div>



                            <!-- Add more rows below by repeating col-3s in groups of 4 -->
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 col-sm-6">
                    <div class="counter-box text-center white">
                        <div class="card">
                            <div class="body">
                                <div id="plist" class="people-list">
                                    <div class="form-line m-b-15">
                                        <input type="text" class="form-control" placeholder="Search..." />
                                    </div>
                                    <div class="tab-content">
                                        <div id="chat_user">
                                            <ul class="chat-list list-unstyled m-b-0">
                                                <li class="clearfix active">
                                                    <img src="../../assets/images/user/user1.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">William Smith</div>
                                                        <div class="status">
                                                            <i class="material-icons offline">fiber_manual_record</i>
                                                            left 7 mins ago
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix ">
                                                    <img src="../../assets/images/user/user2.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Martha Williams</div>
                                                        <div class="status">
                                                            <i class="material-icons offline">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user3.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Joseph Clark</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user4.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Nancy Taylor</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user5.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Margaret Wilson</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user6.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Joseph Jones</div>
                                                        <div class="status">
                                                            <i class="material-icons offline">fiber_manual_record</i>
                                                            left 30 mins ago
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user7.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Jane Brown</div>
                                                        <div class="status">
                                                            <i class="material-icons offline">fiber_manual_record</i>
                                                            left 10 hours ago
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user8.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Eliza Johnson</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user3.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Mike Clark</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user4.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Ann Henry</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user5.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">Nancy Smith</div>
                                                        <div class="status">
                                                            <i class="material-icons online">fiber_manual_record</i>
                                                            online
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix">
                                                    <img src="../../assets/images/user/user9.jpg" alt="avatar">
                                                    <div class="about">
                                                        <div class="name">David Wilson</div>
                                                        <div class="status">
                                                            <i class="material-icons offline">fiber_manual_record</i>
                                                            offline since Oct 28
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <!-- Widgets -->
            <div class="row">
                <!-- Counter Item -->
                <div class="col-lg-3 col-sm-6">
                    <div class="counter-box white">
                        <div class="row align-items-center">
                            <div class="col-5 text-center">
                                <input type="text" class="dial" value="93" data-width="90" data-height="90 disabled"
                                    data-thickness="0.25" data-fgColor="#00BCD4">
                            </div>
                            <div class="col-7">
                                <div class="text-white font-17 m-b-5">Shooting</div>
                                <div class="progress" style="height: 12px; border-radius: 50px;">
                                    <div class="progress-bar bg-info width-per-75 rounded-pill" role="progressbar" aria-valuenow="75"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 75%;">75%</div>
                                </div>
                                <div class="text font-19 m-b-5">10% Increase in 28 days</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="counter-box white">
                        <div class="row align-items-center">
                            <div class="col-5 text-center">
                                <input type="text" class="dial" value="93" data-width="90" data-height="90 disabled"
                                    data-thickness="0.25" data-fgColor="#00BCD4">
                            </div>
                            <div class="col-7">
                                <div class="text-white font-17 m-b-5">Shooting</div>
                                <div class="progress" style="height: 12px; border-radius: 50px;">
                                    <div class="progress-bar bg-info width-per-75 rounded-pill" role="progressbar" aria-valuenow="75"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 75%;">75%</div>
                                </div>
                                <div class="text font-19 m-b-5">10% Increase in 28 days</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="counter-box white">
                        <div class="row align-items-center">
                            <div class="col-5 text-center">
                                <input type="text" class="dial" value="93" data-width="90" data-height="90 disabled"
                                    data-thickness="0.25" data-fgColor="#00BCD4">
                            </div>
                            <div class="col-7">
                                <div class="text-white font-17 m-b-5">Shooting</div>
                                <div class="progress" style="height: 12px; border-radius: 50px;">
                                    <div class="progress-bar bg-info width-per-75 rounded-pill" role="progressbar" aria-valuenow="75"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 75%;">75%</div>
                                </div>
                                <div class="text font-19 m-b-5">10% Increase in 28 days</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6">
                    <div class="counter-box white">
                        <div class="row align-items-center">
                            <div class="col-5 text-center">
                                <input type="text" class="dial" value="93" data-width="90" data-height="90 disabled"
                                    data-thickness="0.25" data-fgColor="#00BCD4">
                            </div>
                            <div class="col-7">
                                <div class="text-white font-17 m-b-5">Shooting</div>
                                <div class="progress" style="height: 12px; border-radius: 50px;">
                                    <div class="progress-bar bg-info width-per-75 rounded-pill" role="progressbar" aria-valuenow="75"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 75%;">75%</div>
                                </div>
                                <div class="text font-19 m-b-5">10% Increase in 28 days</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Calendar -->

                <div class="row">
                    <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                        <div class="card">
                            <div class="header">
                                <h2>Calendar</h2>
                            </div>
                            <div class="body">
                                <div id='calendar'></div>
                            </div>
                        </div>
                    </div>
                    <!-- Latest Post -->
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <div class="card">
                            <div class="header">
                                <h2>Latest Posts</h2>
                                <ul class="header-dropdown">
                                    <li class="dropdown">
                                        <a href="#" onClick="return false;" class="dropdown-toggle"
                                            data-bs-toggle="dropdown" role="button" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="#" onClick="return false;">Add</a>
                                            </li>
                                            <li>
                                                <a href="#" onClick="return false;">Edit</a>
                                            </li>
                                            <li>
                                                <a href="#" onClick="return false;">Delete</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <div class="card-block">
                                    <div class="row m-b-20">
                                        <div class="col-auto p-r-0">
                                            <img src="../../assets/images/posts/post1.jpg" alt="user image"
                                                class="latest-posts-img">
                                        </div>
                                        <div class="col">
                                            <h6>About Something</h6>
                                            <p class="text-muted m-b-5">
                                                <i class="fa fa-play-circle-o"></i> Video | 10 minutes ago
                                            </p>
                                            <p class="text-muted ">Lorem Ipsum is simply dummy text of the.</p>
                                        </div>
                                    </div>
                                    <div class="row m-b-20">
                                        <div class="col-auto p-r-0">
                                            <img src="../../assets/images/posts/post2.jpg" alt="user image"
                                                class="latest-posts-img">
                                        </div>
                                        <div class="col">
                                            <h6>Relationship</h6>
                                            <p class="text-muted m-b-5">
                                                <i class="fa fa-play-circle-o"></i> Video | 24 minutes ago
                                            </p>
                                            <p class="text-muted ">Lorem Ipsum is simply dummy text of the.</p>
                                        </div>
                                    </div>
                                    <div class="row m-b-20">
                                        <div class="col-auto p-r-0">
                                            <img src="../../assets/images/posts/post3.jpg" alt="user image"
                                                class="latest-posts-img">
                                        </div>
                                        <div class="col">
                                            <h6>Human body</h6>
                                            <p class="text-muted m-b-5">
                                                <i class="fa fa-play-circle-o"></i> Video | 53 minutes ago
                                            </p>
                                            <p class="text-muted ">Lorem Ipsum is simply dummy text of the.</p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <a href="#!" class="b-b-primary text-primary">View All Posts</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>


        </div>
        <div class="container-fluid mb-5">
            <h4 class="text-white mb-3">Personal Training Ads</h4>
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">

                <!-- Flyer 1 -->
                <div style="width: 222px; height: 313px; flex: 0 0 auto;">
                    <img src="assets/images/player/flayer1.png" alt="Flayer 1"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>

                <!-- Flyer 2 -->
                <div style="width: 222px; height: 313px; flex: 0 0 auto;">
                    <img src="assets/images/player/flayer2.png" alt="Flayer 2"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>

                <!-- Flyer 3 -->
                <div style="width: 222px; height: 313px; flex: 0 0 auto;">
                    <img src="assets/images/player/flayer3.png" alt="Flayer 3"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>

                <!-- Main Video -->
                <div style="flex: 1 1 340px; max-width: 380px; height: 313px;">
                    <video controls poster="assets/images/player/video.png"
                        style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <source src="assets/images/player/video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Video Card -->
                <div style="flex: 1 1 300px; max-width: 360px;">
                    <div class="card" style="background-color: #000; color: #fff; height: 313px; overflow-y: auto;">
                        <div class="body">
                            <div class="card-block p-3">
                                <!-- Repeat block for each video -->
                                <div class="row mb-3">
                                    <div class="col-auto pe-0">
                                        <video width="90" height="60" controls poster="../../assets/images/posts/post1.jpg">
                                            <source src="../../assets/videos/video1.mp4" type="video/mp4">
                                        </video>
                                    </div>
                                    <div class="col ps-2">
                                        <h6 class="text-white">About Something</h6>
                                        <p class="text-muted mb-1"><i class="fa fa-play-circle-o"></i> Video | 10 minutes ago</p>
                                        <p class="text-muted mb-0">Lorem Ipsum is simply dummy text of the.</p>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-auto pe-0">
                                        <video width="90" height="60" controls poster="../../assets/images/posts/post2.jpg">
                                            <source src="../../assets/videos/video2.mp4" type="video/mp4">
                                        </video>
                                    </div>
                                    <div class="col ps-2">
                                        <h6 class="text-white">Relationship</h6>
                                        <p class="text-muted mb-1"><i class="fa fa-play-circle-o"></i> Video | 24 minutes ago</p>
                                        <p class="text-muted mb-0">Lorem Ipsum is simply dummy text of the.</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-auto pe-0">
                                        <video width="90" height="60" controls poster="../../assets/images/posts/post3.jpg">
                                            <source src="../../assets/videos/video3.mp4" type="video/mp4">
                                        </video>
                                    </div>
                                    <div class="col ps-2">
                                        <h6 class="text-white">Human body</h6>
                                        <p class="text-muted mb-1"><i class="fa fa-play-circle-o"></i> Video | 53 minutes ago</p>
                                        <p class="text-muted mb-0">Lorem Ipsum is simply dummy text of the.</p>
                                    </div>
                                </div>
                                <!-- End video blocks -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </section>

    <!-- Model for Calendar -->

    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEventTitle">Add Event</h5>
                    <h5 class="modal-title" id="editEventTitle">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="">
                        <input type="hidden" id="id" name="id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Title" name="title"
                                            id="title">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="form-group default-select">
                                    <label>Category</label>
                                    <select class="form-control" id="categorySelect">
                                        <option value="" disabled selected>Choose your option</option>
                                        <option id="work" value="fc-event-success">Work</option>
                                        <option id="personal" value="fc-event-warning">Personal</option>
                                        <option id="important" value="fc-event-primary">Important</option>
                                        <option id="travel" value="fc-event-danger">Travel</option>
                                        <option id="friends" value="fc-event-info">Friends</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="text" class="form-control datetimepicker"
                                        placeholder="Start Date" name="starts_at" id="starts-at">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="text" class="form-control datetimepicker"
                                        placeholder="End Date" name="ends_at" id="ends-at">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Event Details</label>
                                    <textarea id="eventDetails" name="eventDetails" placeholder="Enter Details"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-whitesmoke pr-0">
                            <button type="button" class="btn btn-primary" id="add-event">Add
                                Event</button>
                            <button type="button" class="btn btn-round btn-primary" id="edit-event">Edit
                                Event</button>
                            <button type="button" id="close" class="btn btn-danger"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('players.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        function setupCalendar() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: [
                    @foreach ($pickupGames as $game)
                        {
                            id: {{ $game->id }},
                            title: '{{ $game->sport->name }} Game',
                            start: '{{ $game->game_datetime }}',
                            extendedProps: {
                                joined: {{ $game->participants->contains($user->id) ? 'true' : 'false' }},
                                location: '{{ $game->location }}'
                            }
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    const joined = info.event.extendedProps.joined;
                    const gameId = info.event.id;
                    const location = info.event.extendedProps.location;

                    document.getElementById('pickupModalLabel').textContent =
                        `${info.event.title} @ ${location}`;
                    document.getElementById('pickupModalJoinBtn').dataset.id = gameId;
                    document.getElementById('pickupModalLeaveBtn').dataset.id = gameId;

                    document.getElementById('pickupModalJoinBtn').classList.toggle('d-none', joined);
                    document.getElementById('pickupModalLeaveBtn').classList.toggle('d-none', !joined);

                    new bootstrap.Modal(document.getElementById('pickupModal')).show();
                }
            });
            calendar.render();
        }

        function joinGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }

        function leaveGame(btn) {
            const id = btn.dataset.id;
            fetch(`/pickup-games/${id}/leave`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }

        window.addEventListener('DOMContentLoaded', setupCalendar);
    </script>
</body>

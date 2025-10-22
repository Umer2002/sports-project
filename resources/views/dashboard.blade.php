<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c2f33;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            height: 100vh;
            background-color: #23272a;
            padding: 20px;
        }
        .main-content {
            background-color: #2c2f33;
            padding: 20px;
        }
        .card-custom {
            background-color: #33363a;
        }
        .profile-bg {
            background-color: #f47b8a;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-2 sidebar">
                <div class="text-center mb-4">
                    <h4>VALUE<span class="text-primary">BET</span></h4>
                    <img src="https://via.placeholder.com/70" class="rounded-circle my-3" alt="User">
                    <h6>Slava Kornilov</h6>
                    <small>Professional • Level 5</small>
                </div>
                <div class="text-center mb-3">
                    <span class="me-3"><strong>2,434</strong><br><small>Followers</small></span>
                    <span><strong>4,245</strong><br><small>Following</small></span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link text-white" href="#">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">My Feed</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Tipsters</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Hot Tips</a></li>
                </ul>
            </nav>

            <main class="col-7 main-content">
                <div class="card card-custom mb-3">
                    <div class="profile-bg p-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <img src="/mnt/data/player.jpg" class="img-fluid rounded-circle" alt="Player">
                            </div>
                            <div class="col-md-8">
                                <h4>Emily Baker <button class="btn btn-success btn-sm">Subscribe from £75</button> <button class="btn btn-primary btn-sm">Follow</button></h4>
                                <p><small>Professional</small></p>
                                <div class="row">
                                    <div class="col"><strong>475</strong><br><small>Total Tips</small></div>
                                    <div class="col"><strong>98.57</strong><br><small>Avg Odds</small></div>
                                    <div class="col"><strong>98.57</strong><br><small>Win rate</small></div>
                                    <div class="col"><strong>30.03</strong><br><small>Total Units</small></div>
                                    <div class="col"><strong>3,576</strong><br><small>Profit</small></div>
                                    <div class="col"><strong>30%</strong><br><small>ROI</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-3">
                    <h5>Result pending tips</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-custom p-2">
                                <h6>Southampton vs Leicester City</h6>
                                <p>#Southampton to win @Bet365</p>
                                <span class="badge bg-success">+12 Profit</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-2">
                                <h6>West Ham United vs Sheffield United</h6>
                                <p>#westhamunited to win @Bet365</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <aside class="col-3 sidebar">
                <h5>Your Balance <span class="float-end">345.45</span></h5>
                <canvas class="my-3" id="balanceChart" height="100"></canvas>
                <h6>Top Following</h6>
                <div class="mb-3">
                    <img src="https://via.placeholder.com/40" class="rounded-circle" alt="follower">
                    <img src="https://via.placeholder.com/40" class="rounded-circle" alt="follower">
                    <img src="https://via.placeholder.com/40" class="rounded-circle" alt="follower">
                </div>
                <h6>Notifications</h6>
                <ul class="list-unstyled">
                    <li>Parker <small>+273.26 profit</small></li>
                    <li>Teresa <small>-123.34 loss</small></li>
                </ul>
            </aside>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('balanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: { labels: ['Jan', 'Feb', 'Mar', 'Apr'], datasets: [{ label: 'Balance', data: [100, 200, 300, 400], borderColor: '#0d6efd', fill: false }] }
        });
    </script>
</body>
</html>

    <!-- Tournament Card -->
    <div class="tournament-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-2">
            <div class="col-md-auto">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('assets/club-dashboard-main/assets/tr.png') }}" alt="stadium"
                        class="tournament-img" />
                    <h3 class="tournament-title" style="color: #fff;">Tournament Directory</h3>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center align-items-center">
                <div class="subtitle fw-semibold text-white mb-0" style="color: #fff;">
                    Tournament Engine
                </div>
                <a href="#" class="start-btn" data-bs-toggle="modal"
                    data-bs-target="#createTournamentModal" role="button">
                    Start
                </a>

            </div>
        </div>
        <div class="card-desc" style="color: #fff;">
            Search and register for tournaments by location, date, sport, division, age, and more.
        </div>
    </div>

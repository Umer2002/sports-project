<div class="row">
    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-red">
            <div class="icon m-b-10">
                <div class="chart chart-bar-2"></div>
            </div>
            <div class="text m-b-10">Total Assigned</div>
            <h3 class="m-b-0">{{ $totalAssigned }}
                <i class="material-icons">trending_up</i>
            </h3>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-cyan">
            <div class="icon m-b-10">
                <div class="chart chart-line-2"></div>
            </div>
            <div class="text m-b-10">Applications</div>
            <h3 class="m-b-0">{{ $totalApplications }}
                <i class="material-icons">trending_up</i>
            </h3>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-orange">
            <div class="icon m-b-10">
                <div class="chart chart-pie-2"></div>
            </div>
            <div class="text m-b-10">Upcoming Tournaments</div>
            <h3 class="m-b-0">{{ $totalUpcomingTournaments }}
                <i class="material-icons">trending_down</i>
            </h3>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center green">
            <div class="icon m-b-10">
                <div class="chart chart-bar-2"></div>
            </div>
            <div class="text m-b-10">Latest Tournament</div>
            <h3 class="m-b-0">{{ $latestTournament ? $latestTournament->title : 'N/A' }}
                <i class="material-icons">trending_down</i>
            </h3>
        </div>
    </div>
</div>

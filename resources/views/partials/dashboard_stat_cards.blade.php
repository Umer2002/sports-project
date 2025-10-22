<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>

.info-box5 .knob-icon {
    margin-top:-65px !important;
    width: 30%;
    display: flex;
}
.state-overview {
    margin-left: -20px;
    margin-right: -20px;
}
.state-overview > [class*="col-"] {
    padding-left: 5px;
    padding-right: 5px;
}
</style>
<div class="row">
    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-green">
            <div class="icon m-b-10">
                <div class="chart chart-bar"><canvas></canvas></div>
            </div>
            <div class="text m-b-10">Estimated Payout</div>
            <h3 class="m-b-0">{{ $estimatedPayout ?? 0 }}
                <i class="material-icons">trending_{{ $payoutTrendDirection ?? 'up' }}</i>
            </h3>
            <small class="displayblock">{{ $payoutTrendPercent ?? 0 }}% {{ ucfirst($payoutTrendDirection ?? 'up') }} Than Average</small>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-orange">
            <div class="icon m-b-10">
                <span class="chart chart-line"><canvas></canvas></span>
            </div>
            <div class="text m-b-10">Coaches</div>
            <h3 class="m-b-0">{{ $coachCount ?? 0 }}
                <i class="material-icons">trending_{{ $coachTrendDirection ?? 'up' }}</i>
            </h3>
            <small class="displayblock">{{ $coachTrendPercent ?? 0 }}% {{ ucfirst($coachTrendDirection ?? 'up') }} Than Average</small>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-cyan">
            <div class="icon m-b-10">
                <div class="chart chart-pie"><canvas></canvas></div>
            </div>
            <div class="text m-b-10">Teams</div>
            <h3 class="m-b-0">{{ $teamCount ?? 0 }}
                <i class="material-icons">trending_{{ $teamTrendDirection ?? 'up' }}</i>
            </h3>
            <small class="displayblock">{{ $teamTrendPercent ?? 0 }}% {{ ucfirst($teamTrendDirection ?? 'up') }} Than Average</small>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="support-box text-center l-bg-purple">
            <div class="icon m-b-10">
                <div class="chart chart-bar"><canvas></canvas></div>
            </div>
            <div class="text m-b-10">Total Active Players</div>
            <h3 class="m-b-0">{{ $activePlayers ?? 0 }}
                <i class="material-icons">trending_{{ $activePlayerTrendDirection ?? 'up' }}</i>
            </h3>
            <small class="displayblock">{{ $activePlayerTrendPercent ?? 0 }}% {{ ucfirst($activePlayerTrendDirection ?? 'up') }} Than Average</small>
        </div>
    </div>
</div>


<div class="row main_cards_css">
    <div class="metric-widget gray-card p-4 pb-0 mb-4 mt-4">
        <div class="row">
            {{-- Total Revenue --}}
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color: #74d876; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #468477; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient)"
                                    stroke-dasharray="{{ min(((float) ($totalRevenue ?? 0)) / 100, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(((float) ($totalRevenue ?? 0)) / 100, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Total Revenue</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar green-grad"
                                style="width: {{ min(((float) ($totalRevenue ?? 0)) / 100, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            ${{ number_format((float) ($totalRevenue ?? 0), 0) }} Total<br />Revenue
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reported Injuries --}}
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color: #e2a944; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #ea7d4d; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient1)"
                                    stroke-dasharray="{{ min(((float) ($injuryReports ?? 0)) * 10, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(((float) ($injuryReports ?? 0)) * 10, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Reported Injuries</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar orange-grad"
                                style="width: {{ min(((float) ($injuryReports ?? 0)) * 10, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ (float) ($injuryReports ?? 0) }} Injuries<br />Reported
                        </div>
                    </div>
                </div>
            </div>

            {{-- Player Transfers --}}
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color: #78bdde; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #0075ff; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient2)"
                                    stroke-dasharray="{{ min(((float) ($playerTransfers ?? 0)) * 5, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(((float) ($playerTransfers ?? 0)) * 5, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">Player Transfers</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar blue-grad"
                                style="width: {{ min(((float) ($playerTransfers ?? 0)) * 5, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ (float) ($playerTransfers ?? 0) }} Transfers<br />This Month
                        </div>
                    </div>
                </div>
            </div>

            {{-- New Registrations --}}
            <div class="col-sm-12 col-md-6 col-lg-6 col-xxl-3">
                <div class="metric-card p-3 mb-4 d-flex gap-2">
                    <div class="time-breakdown-chart">
                        <div class="percentage-chart percentage-chart-meeting">
                            <svg viewBox="0 0 36 36">
                                <defs>
                                    <linearGradient id="circleGradient3" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color: #667df2; stop-opacity: 1" />
                                        <stop offset="90%" style="stop-color: #7b4ed7; stop-opacity: 1" />
                                    </linearGradient>
                                </defs>
                                <path class="percentage-chart-bg"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <path class="percentage-chart-stroke" stroke="url(#circleGradient3)"
                                    stroke-dasharray="{{ min(((float) ($newRegistrations ?? 0)) * 2, 100) }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <div class="counter" style="--counter-end: {{ min(((float) ($newRegistrations ?? 0)) * 2, 100) }}"></div>
                        </div>
                    </div>
                    <div class="metric-text">
                        <h3 class="metric-title fs-14px mb-0">New Registrations</h3>
                        <div class="progress-xy my-2" style="height: 5px">
                            <div class="progress-bar purple-grad"
                                style="width: {{ min(((float) ($newRegistrations ?? 0)) * 2, 100) }}%"></div>
                        </div>
                        <div class="metric-subtitle fs-12px">
                            {{ (float) ($newRegistrations ?? 0) }} New<br />Registrations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


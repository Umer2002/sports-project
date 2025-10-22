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

<div class="row state-overview">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box5 animate-bar bg-b-purple">
            <div class="knob-icon">
                <div style="display:inline;width:80px;height:80px;">
                    <canvas width="160" height="160" style="width: 80px; height: 80px;"></canvas>
                    <input type="text" class="dial" value="{{ $revenueIncreasePercent }}" data-width="80" data-height="80" data-fgcolor="#67de69"
                        style="width: 44px; height: 26px; position: absolute; vertical-align: middle; margin-top: 26px; margin-left: -62px; border: 0px; background: none; font: bold 16px Arial; text-align: center; color: rgb(103, 222, 105); padding: 0px; appearance: none;">
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Total Revenue</span>
                <div class="progress m-t-20">
                    <div class="progress-bar l-bg-green shadow-style" style="width: {{ $revenueIncreasePercent }}%" role="progressbar"
                        aria-valuenow="{{ $revenueIncreasePercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-description">
                    <small>{{ $totalRevenue }} ({{ $revenueIncreasePercent }}% Increase in 28 Days)</small>
                </span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box5 animate-bar bg-b-danger">
            <div class="knob-icon">
                <div style="display:inline;width:80px;height:80px;">
                    <canvas width="160" height="160" style="width: 80px; height: 80px;"></canvas>
                    <input type="text" class="dial" value="{{ $injuriesIncreasePercent }}" data-width="80" data-height="80" data-fgcolor="#ff7676"
                        style="width: 44px; height: 26px; position: absolute; vertical-align: middle; margin-top: 26px; margin-left: -62px; border: 0px; background: none; font: bold 16px Arial; text-align: center; color: #ff7676; padding: 0px; appearance: none;">
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Reported Injuries</span>
                <div class="progress m-t-20">
                    <div class="progress-bar bg-danger shadow-style" style="width: {{ $injuriesIncreasePercent }}%" role="progressbar"
                        aria-valuenow="{{ $injuriesIncreasePercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-description">
                    <small>{{ $reportedInjuries }} ({{ $injuriesIncreasePercent }}% Increase)</small>
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box5 animate-bar bg-b-blue">
            <div class="knob-icon">
                <div style="display:inline;width:80px;height:80px;">
                    <canvas width="160" height="160" style="width: 80px; height: 80px;"></canvas>
                    <input type="text" class="dial" value="{{ $transfersIncreasePercent }}" data-width="80" data-height="80" data-fgcolor="#4fc3f7"
                        style="width: 44px; height: 26px; position: absolute; vertical-align: middle; margin-top: 26px; margin-left: -62px; border: 0px; background: none; font: bold 16px Arial; text-align: center; color: #4fc3f7; padding: 0px; appearance: none;">
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">Player Transfers</span>
                <div class="progress m-t-20">
                    <div class="progress-bar bg-info shadow-style" style="width: {{ $transfersIncreasePercent }}%" role="progressbar"
                        aria-valuenow="{{ $transfersIncreasePercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-description">
                    <small>{{ $playerTransfers }} Transfers ({{ $transfersIncreasePercent }}% Growth)</small>
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="info-box5 animate-bar bg-b-green">
            <div class="knob-icon">
                <div style="display:inline;width:80px;height:80px;">
                    <canvas width="160" height="160" style="width: 80px; height: 80px;"></canvas>
                    <input type="text" class="dial" value="{{ $registrationsIncreasePercent }}" data-width="80" data-height="80" data-fgcolor="#81c784"
                        style="width: 44px; height: 26px; position: absolute; vertical-align: middle; margin-top: 26px; margin-left: -62px; border: 0px; background: none; font: bold 16px Arial; text-align: center; color: #81c784; padding: 0px; appearance: none;">
                </div>
            </div>
            <div class="info-box-content">
                <span class="info-box-text">New Registrations</span>
                <div class="progress m-t-20">
                    <div class="progress-bar bg-success shadow-style" style="width: {{ $registrationsIncreasePercent }}%" role="progressbar"
                        aria-valuenow="{{ $registrationsIncreasePercent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <span class="progress-description">
                    <small>{{ $newRegistrations }} ({{ $registrationsIncreasePercent }}% Growth)</small>
                </span>
            </div>
        </div>
    </div>

</div>

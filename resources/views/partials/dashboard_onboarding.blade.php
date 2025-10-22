<!-- Onboarding Countdown -->
<div class="countdown-container">
    <div class="countdown-header">
        <div class="d-flex align-items-center">
            <h2 class="mb-0" id="onboardingButton">Onboarding</h2>
        </div>
    </div>

    <div class="countdown-grid">
        <div class="countdown-card purple-grad" style="color: #fff;">
            <span class="number">{{ $onboardingData['Soccer'] }}</span>
            <span class="label" style="color: #fff;">Soccer</span>
        </div>
        <div class="countdown-card orange-grad" style="color: #fff;">
            <span class="number">{{ $onboardingData['Basketball'] }}</span>
            <span class="label" style="color: #fff;">Basketball</span>
        </div>
        <div class="countdown-card blue-grad" style="color: #fff;">
            <span class="number">{{ $onboardingData['Football'] }}</span>
            <span class="label" style="color: #fff;">American Football</span>
        </div>
        <div class="countdown-card pink-grad" style="color: #fff;">
            <span class="number">{{ $onboardingData['Baseball'] }}</span>
            <span class="label" style="color: #fff;">Baseball</span>
        </div>
        <div class="countdown-card green-grad" style="color: #fff;">
            <span class="number">{{ $onboardingData['Hockey'] }}</span>
            <span class="label" style="color: #fff;">Hockey</span>
        </div>
    </div>

    <p class="tip">
        Tip: Complete your club setup to unlock all features and start managing your teams effectively.
    </p>
</div>


<style>
    #onboardingButton{
        background-color: #FF2D55;
        color: #fff;
        padding: 4px 8px;
        border-radius: 5px;
        font-size: 18px;
    }
</style>

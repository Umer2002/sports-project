<div class="wizard-steps mb-4">
    <div class="d-flex justify-content-between bg-dark rounded p-2 px-4 text-white text-center">

        <div class="flex-fill">
            <div class="{{ request()->routeIs('admin.teams.wizard.step1') ? 'bg-success text-white' : 'text-muted' }} py-2 rounded">
                <strong>1.</strong> Team Info
            </div>
        </div>

        <div class="flex-fill">
            <div class="{{ request()->routeIs('admin.teams.wizard.step2') ? 'bg-success text-white' : 'text-muted' }} py-2 rounded">
                <strong>2.</strong> Eligibility
            </div>
        </div>

        <div class="flex-fill">
            <div class="{{ request()->routeIs('admin.teams.wizard.step3') ? 'bg-success text-white' : 'text-muted' }} py-2 rounded">
                <strong>3.</strong> Players
            </div>
        </div>

        <div class="flex-fill">
            <div class="{{ request()->routeIs('admin.teams.wizard.step4') ? 'bg-success text-white' : 'text-muted' }} py-2 rounded">
                <strong>4.</strong> Formation
            </div>
        </div>

    </div>
</div>

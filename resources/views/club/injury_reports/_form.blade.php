@php
    $report = $report ?? null;
@endphp

<form action="{{ route('club.injury_reports.store') }}" method="POST" enctype="multipart/form-data" id="injuryForm">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <style>
        .step-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-nav::before {
            content: '';
            position: absolute;
            top: 22px;
            left: 0;
            right: 0;
            height: 2px;
            background: #ddd;
            z-index: 0;
        }
        .step-nav .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-nav .step .step-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 6px;
            color: #fff;
            font-size: 18px;
        }
        .step-nav .step.active .step-icon {
            background: #00AEEF;
        }
        .step-nav .step .step-title {
            font-size: 0.9rem;
            color: #aaa;
        }
        .step-nav .step.active .step-title {
            color: #00AEEF;
            font-weight: bold;
        }
    </style>

    <div id="injury-wizard">
        <div class="step-nav mb-4">
            <div class="step active" data-step="0">
                <div class="step-icon"><i class="ti ti-user-check"></i></div>
                <div class="step-title">Step 1</div>
            </div>
            <div class="step" data-step="1">
                <div class="step-icon"><i class="ti ti-alert-triangle"></i></div>
                <div class="step-title">Step 2</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-icon"><i class="ti ti-ambulance"></i></div>
                <div class="step-title">Step 3</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-icon"><i class="ti ti-file-text"></i></div>
                <div class="step-title">Step 4</div>
            </div>
        </div>

        <div class="wizard-step step-1">
            @include('club.injury_reports.steps.step1', ['report' => $report])
        </div>

        <div class="wizard-step step-2 d-none">
            @include('club.injury_reports.steps.step2', ['report' => $report])
        </div>

        <div class="wizard-step step-3 d-none">
            @include('club.injury_reports.steps.step3', ['report' => $report])
        </div>

        <div class="wizard-step step-4 d-none">
            @include('club.injury_reports.steps.step4', ['report' => $report])
        </div>

        <div class="card mt-3">
            <div class="card-body text-end">
                <button type="button" class="btn btn-secondary" id="prevStep">Back</button>
                <button type="button" class="btn btn-primary" id="nextStep">Next</button>
                <button type="button" class="btn btn-success d-none" id="submitForm">Save Report</button>
            </div>
        </div>
    </div>
</form>



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const steps = document.querySelectorAll('#injury-wizard .wizard-step');
        const navSteps = document.querySelectorAll('#injury-wizard .step-nav .step');
        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');
        const submitBtn = document.getElementById('submitForm');
        const form = document.getElementById('injuryForm');
        let currentStep = 0;

        function showStep(index) {
            steps.forEach((step, i) => step.classList.toggle('d-none', i !== index));
            navSteps.forEach((s, i) => s.classList.toggle('active', i === index));
            prevBtn.classList.toggle('d-none', index === 0);
            nextBtn.classList.toggle('d-none', index === steps.length - 1);
            submitBtn.classList.toggle('d-none', index !== steps.length - 1);
        }

        nextBtn?.addEventListener('click', () => {
            if (currentStep < steps.length - 1) currentStep++;
            showStep(currentStep);
        });

        prevBtn?.addEventListener('click', () => {
            if (currentStep > 0) currentStep--;
            showStep(currentStep);
        });

        // AJAX Submit
        submitBtn?.addEventListener('click', function () {
            const formData = new FormData(form);
            const actionUrl = form.getAttribute('action');
            const method = form.querySelector('input[name="_method"]')?.value || 'POST';

            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

            fetch(actionUrl, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                if (response.status === 422) {
                    const data = await response.json();
                    showValidationErrors(data.errors);
                    return false;
                } else if (response.ok) {
                    const data = await response.text();
                    window.location.href = "{{ route('club.injury_reports.index') }}"; // redirect on success
                } else {
                    alert('An unexpected error occurred.');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Submission failed. Check console for details.');
            });
        });

        function showValidationErrors(errors) {
            let firstErrorStep = -1;

            for (const name in errors) {
                const field = form.querySelector(`[name="${name}"]`);
                if (field) {
                    field.classList.add('is-invalid');
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    errorDiv.innerText = errors[name][0];
                    field.parentNode.appendChild(errorDiv);

                    if (firstErrorStep === -1) {
                        const stepElement = field.closest('.wizard-step');
                        if (stepElement) {
                            firstErrorStep = Array.from(steps).indexOf(stepElement);
                        }
                    }
                }
            }

            if (firstErrorStep !== -1) {
                showStep(firstErrorStep);
            }
        }

        showStep(currentStep);
    });
</script>
@endpush


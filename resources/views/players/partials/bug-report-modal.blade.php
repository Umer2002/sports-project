<div class="modal fade" id="playerBugReportModal" tabindex="-1" aria-labelledby="playerBugReportLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-responsive modal-content-one">
            <form id="playerBugReportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header modal-header-one">
                    <div class="modal-headding modal-headding-two">
                        <div class="leftmodal-header report-headding-card">
                            <div class="report-icon">
                                <img src="{{ asset('assets/player-dashboard/images/bugIcon-two.png') }}" alt="Bug icon">
                            </div>
                            <div class="modal-text-headding">
                                <h1 class="modal-title fs-5" id="playerBugReportLabel">Report a Bug</h1>
                                <p>HELP US FIX THINGS FAST</p>
                            </div>
                        </div>
                    </div>
                    <div class="ticket-main-btn d-flex align-items-center gap-2">
                        <span class="ticket-pill">Ticket ID: #TEMP</span>
                        <button type="button" class="btn-diagnostics">Submit Diagnostics</button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-7 order-1 order-lg-0">
                            <div class="mb-3">
                                <label class="label-a" for="bugTitle">Title</label>
                                <input type="text" id="bugTitle" name="title" class="form-control-a" placeholder='Short summary, e.g., "Crash when opening team chat"' required>
                            </div>

                            <div class="row g-2 mb-3 align-items-stretch">
                                <div class="col-md-6">
                                    <label class="label-a" for="bugCategory">Category</label>
                                    <select id="bugCategory" name="category" class="form-select form-select-a" required>
                                        <option value="ui">UI</option>
                                        <option value="chat">Chat</option>
                                        <option value="payments">Payments</option>
                                        <option value="tournaments">Tournaments</option>
                                        <option value="performance">Performance</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-a d-block" for="bugSeverity">Severity</label>
                                    <div class="panel-box-one" id="bugSeverity" role="group" aria-label="Bug severity selector">
                                        <input type="hidden" name="severity" value="low" id="bugSeverityValue">
                                        <button type="button" class="severity-btn active-low is-active" data-severity="low">Low</button>
                                        <button type="button" class="severity-btn" data-severity="medium">Medium</button>
                                        <button type="button" class="severity-btn" data-severity="high">High</button>
                                        <button type="button" class="severity-btn" data-severity="critical">Crit</button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="label-a" for="bugDescription">Description</label>
                                <textarea id="bugDescription" name="description" class="form-control-a" rows="4" placeholder="What happened? What did you expect to happen?"></textarea>
                            </div>

                            <div class="steps-box">
                                <label class="label-a" for="bugSteps">Steps to Reproduce</label>
                                <div class="steps-list" id="bugStepsList"></div>
                                <textarea id="bugSteps" name="steps" class="form-control-a" rows="4" placeholder="1) Open Calendar → select tournament
2) Tap Team Chat
3) App freezes for 5-7 seconds"></textarea>
                                <button type="button" class="btn-add" id="bugAddStep">+ Add Step</button>
                            </div>
                        </div>

                        <div class="col-lg-5 order-0 order-lg-1">
                            <div class="mb-3">
                                <label class="label-a" for="bugAttachment">Screenshots / Files</label>
                                <input type="file" name="attachment" id="bugAttachment" class="d-none" accept="image/*,video/*,application/pdf">
                                <div class="upload-box-one" id="bugAttachmentDropzone">
                                    <span class="bug-attachment-label">Drag &amp; drop or click to upload</span>
                                    <button type="button" class="btn-upload-one" id="bugAttachmentButton">Upload</button>
                                </div>
                                <div class="small-one mt-2 d-none" id="bugAttachmentName"></div>
                            </div>

                            <div class="mb-3">
                                <label class="label-a" for="bugEnvironment">Environment</label>
                                <div class="environment-bg">
                                    <textarea id="bugEnvironment" name="environment" class="form-control-a" rows="3" placeholder="Device, OS, browser, build..."></textarea>
                                    <div class="form-check form-switch mt-2">
                                        <label class="form-check-label" for="bugIncludeLogs">Include console logs</label>
                                        <input class="form-check-input" type="checkbox" id="bugIncludeLogs" name="include_logs" value="1">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="label-a" for="bugContact">Contact (Optional)</label>
                                <input type="text" id="bugContact" name="contact" class="form-control-a" placeholder="Email or phone for follow-up">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="bugShareDiagnostics" name="share_diagnostics" value="1">
                                    <label class="form-check-label" for="bugShareDiagnostics">I agree to share diagnostics &amp; screenshots for troubleshooting</label>
                                </div>
                                <p class="small-one mt-2">We redact tokens and personal identifiers automatically.</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <button type="button" class="btn-outline open-help-btn" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#playerHelpChatModal">Open Help Chat</button>
                                <a href="{{ route('player.dashboard') }}" class="btn-view view-docs-btn">View Docs</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn-cancel cancel-button" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-submit submit-report-btn">
                        <span class="submit-label">Submit Report</span>
                        <span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('playerBugReportModal');
    if (!modalEl || modalEl.dataset.initialized === '1') {
        return;
    }
    modalEl.dataset.initialized = '1';

    const form = modalEl.querySelector('#playerBugReportForm');
    const severityButtons = modalEl.querySelectorAll('[data-severity]');
    const severityInput = modalEl.querySelector('#bugSeverityValue');
    const submitBtn = modalEl.querySelector('.submit-report-btn');
    const submitSpinner = submitBtn.querySelector('.spinner-border');
    const submitLabel = submitBtn.querySelector('.submit-label');
    const dropzone = modalEl.querySelector('#bugAttachmentDropzone');
    const attachmentInput = modalEl.querySelector('#bugAttachment');
    const attachmentName = modalEl.querySelector('#bugAttachmentName');
    const addStepBtn = modalEl.querySelector('#bugAddStep');
    const stepsTextarea = modalEl.querySelector('#bugSteps');

    // Prefill environment info
    const environmentField = modalEl.querySelector('#bugEnvironment');
    if (environmentField && !environmentField.value) {
        const nav = navigator;
        const parts = [
            `Browser: ${nav.userAgent}`,
            `Platform: ${nav.platform || 'n/a'}`,
            `Language: ${nav.language || 'n/a'}`,
        ];
        environmentField.value = parts.join('\n');
    }

    severityButtons.forEach((button) => {
        button.addEventListener('click', () => {
            severityButtons.forEach((btn) => btn.classList.remove('active-low', 'active-high', 'active-crit')); // reset custom active classes
            severityButtons.forEach((btn) => btn.classList.remove('is-active'));
            button.classList.add('is-active');
            const severity = button.dataset.severity;
            severityInput.value = severity;

            if (severity === 'low') {
                button.classList.add('active-low');
            } else if (severity === 'high') {
                button.classList.add('active-high');
            } else if (severity === 'critical') {
                button.classList.add('active-crit');
            }
        });
    });

    const setSubmitting = (state) => {
        submitBtn.disabled = state;
        submitSpinner.classList.toggle('d-none', !state);
        submitLabel.classList.toggle('d-none', state);
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setSubmitting(true);

        const formData = new FormData(form);
        const actionUrl = @json(route('player.bug-reports.store'));

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                const firstError = data?.errors ? Object.values(data.errors).flat()[0] : null;
                const message = firstError || data.message || 'Unable to submit report. Please try again.';
                throw new Error(message);
            }

            const payload = await response.json();
            const toastMessage = payload.message || 'Report submitted successfully!';
            window.dispatchEvent(new CustomEvent('bug-report:submitted', { detail: payload }));
            alert(toastMessage);

            form.reset();
            if (attachmentName) {
                attachmentName.classList.add('d-none');
                attachmentName.textContent = '';
            }

            const bootstrapModal = (window.bootstrap && window.bootstrap.Modal)
                ? window.bootstrap.Modal.getInstance(modalEl)
                : null;
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        } catch (error) {
            console.error('Bug report submission failed', error);
            alert(error.message || 'Unable to submit report. Please try again.');
        } finally {
            setSubmitting(false);
        }
    });

    const openFilePicker = () => attachmentInput?.click();

    dropzone?.addEventListener('click', openFilePicker);
    modalEl.querySelector('#bugAttachmentButton')?.addEventListener('click', openFilePicker);

    const updateAttachmentLabel = (file) => {
        if (!attachmentName) return;
        if (file) {
            attachmentName.textContent = `${file.name} (${Math.round(file.size / 1024)} KB)`;
            attachmentName.classList.remove('d-none');
        } else {
            attachmentName.classList.add('d-none');
            attachmentName.textContent = '';
        }
    };

    attachmentInput?.addEventListener('change', () => {
        updateAttachmentLabel(attachmentInput.files?.[0]);
    });

    const handleDrop = (event) => {
        event.preventDefault();
        const [file] = event.dataTransfer.files;
        if (file) {
            attachmentInput.files = event.dataTransfer.files;
            updateAttachmentLabel(file);
        }
        dropzone.classList.remove('dragging');
    };

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone?.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('dragging');
        });
    });

    ['dragleave', 'dragend'].forEach((eventName) => {
        dropzone?.addEventListener(eventName, () => dropzone.classList.remove('dragging'));
    });

    dropzone?.addEventListener('drop', handleDrop);

    addStepBtn?.addEventListener('click', () => {
        const newStepNumber = (stepsTextarea.value.match(/\n|$/g)?.length || 0) + 1;
        const template = `${newStepNumber}) `;
        if (!stepsTextarea.value.endsWith('\n') && stepsTextarea.value.trim().length > 0) {
            stepsTextarea.value += '\n';
        }
        stepsTextarea.value += template;
        stepsTextarea.focus();
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        form.reset();
        updateAttachmentLabel(null);
        severityButtons.forEach((btn) => btn.classList.remove('is-active', 'active-low', 'active-high', 'active-crit'));
        const defaultBtn = modalEl.querySelector('[data-severity="low"]');
        if (defaultBtn) {
            defaultBtn.classList.add('active-low', 'is-active');
        }
        severityInput.value = 'low';
    });
});
</script>
@endpush

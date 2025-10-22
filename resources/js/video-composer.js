const bootstrapModal = () => window.bootstrap?.Modal;

const slugify = (text) => {
    if (!text) return '';
    return text.replace(/\s+/g, ' ').trim();
};

document.addEventListener('DOMContentLoaded', () => {
    const Modal = bootstrapModal();

    document.querySelectorAll('[data-video-composer]').forEach((wrapper) => {
        const form = wrapper.querySelector('[data-composer-form]');
        const fileInput = wrapper.querySelector('[data-composer-file]');
        const pickButton = wrapper.querySelector('[data-composer-pick]');
        const goLiveButton = wrapper.querySelector('[data-composer-go-live]');
        const captionInput = wrapper.querySelector('[data-composer-caption]');
        const titleInput = wrapper.querySelector('[data-composer-title]');
        const categoryInput = wrapper.querySelector('[data-composer-category]');
        const selectedFileLabel = wrapper.querySelector('[data-selected-file]');
        const selectedTypeBadge = wrapper.querySelector('[data-selected-type]');
        const modalEl = wrapper.querySelector('[data-composer-modal]');
        const confirmBtn = modalEl?.querySelector('[data-confirm-type]');
        const errorText = modalEl?.querySelector('[data-option-error]');
        const radios = modalEl ? Array.from(modalEl.querySelectorAll('input[type="radio"]')) : [];
        const liveModalEl = wrapper.querySelector('[data-live-modal]');
        const livePreview = liveModalEl?.querySelector('[data-live-preview]');
        const liveStatusEl = liveModalEl?.querySelector('[data-live-status]');
        const liveStartBtn = liveModalEl?.querySelector('[data-live-start]');
        const liveStopBtn = liveModalEl?.querySelector('[data-live-stop]');
        const liveUseBtn = liveModalEl?.querySelector('[data-live-use]');
        let modalInstance = null;
        let intendedType = null;
        let liveModalInstance = null;
        let liveStream = null;
        let liveRecorder = null;
        let liveChunks = [];
        let liveFile = null;

        const resetSelection = (clearCategory = false) => {
            if (clearCategory) {
                categoryInput.value = '';
            }
            if (selectedFileLabel) {
                selectedFileLabel.textContent = 'No video selected';
            }
            if (selectedTypeBadge) {
                selectedTypeBadge.textContent = '';
                selectedTypeBadge.hidden = true;
            }
            if (fileInput) {
                fileInput.value = '';
            }
            if (errorText) {
                errorText.classList.add('d-none');
            }
        };

        const updateLiveStatus = (text, badgeClass = 'bg-secondary') => {
            if (!liveStatusEl) return;
            liveStatusEl.textContent = text;
            liveStatusEl.className = `badge ${badgeClass}`;
        };

        const stopLiveStream = () => {
            if (liveRecorder && liveRecorder.state === 'recording') {
                try { liveRecorder.stop(); } catch (_err) {}
            }
            if (liveStream) {
                liveStream.getTracks().forEach((track) => track.stop());
            }
            if (livePreview) {
                livePreview.srcObject = null;
            }
            liveStream = null;
            liveRecorder = null;
        };

        const resetLiveState = () => {
            stopLiveStream();
            liveChunks = [];
            liveFile = null;
            if (liveStartBtn) liveStartBtn.disabled = false;
            if (liveStopBtn) liveStopBtn.disabled = true;
            if (liveUseBtn) liveUseBtn.disabled = true;
            updateLiveStatus('Camera idle', 'bg-secondary');
        };

        if (Modal && liveModalEl) {
            liveModalInstance = new Modal(liveModalEl, { backdrop: 'static' });
            liveModalEl.addEventListener('hidden.bs.modal', () => {
                if (!liveFile) {
                    resetSelection(true);
                }
                resetLiveState();
            });
        }

        if (Modal && modalEl) {
            modalInstance = new Modal(modalEl, { backdrop: 'static' });
        }

        const openModal = () => {
            if (!modalInstance) return;
            if (errorText) {
                errorText.classList.add('d-none');
            }
            if (intendedType) {
                const toSelect = radios.find((r) => r.value === intendedType);
                if (toSelect) {
                    toSelect.checked = true;
                }
            }
            modalInstance.show();
        };

        pickButton?.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput?.click();
        });

        goLiveButton?.addEventListener('click', (e) => {
            e.preventDefault();
            intendedType = null;
            categoryInput.value = 'live';
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Your browser does not support live video capture. Please upload a recorded video instead.');
                return;
            }
            if (selectedTypeBadge) {
                selectedTypeBadge.textContent = 'Go Live';
                selectedTypeBadge.hidden = false;
            }
            if (errorText) {
                errorText.classList.add('d-none');
            }
            if (liveModalInstance) {
                resetLiveState();
                liveModalInstance.show();
            }
        });

        fileInput?.addEventListener('change', () => {
            if (!fileInput.files || fileInput.files.length === 0) {
                resetSelection(true);
                return;
            }
            if (categoryInput.value === 'live') {
                const file = fileInput.files[0];
                if (selectedFileLabel && file) {
                    selectedFileLabel.textContent = file.name;
                }
                return;
            }
            const fileName = fileInput.files[0].name;
            if (selectedFileLabel) {
                selectedFileLabel.textContent = fileName;
            }
            if (!intendedType) {
                radios.forEach((r) => { r.checked = false; });
            }
            openModal();
        });

        modalEl?.addEventListener('hidden.bs.modal', () => {
            if (!categoryInput.value) {
                resetSelection(true);
            }
            intendedType = null;
        });

        confirmBtn?.addEventListener('click', () => {
            const checked = radios.find((r) => r.checked);
            if (!checked) {
                if (errorText) {
                    errorText.classList.remove('d-none');
                }
                return;
            }
            categoryInput.value = checked.value;
            if (selectedTypeBadge) {
                selectedTypeBadge.textContent = checked.dataset.optionLabel || checked.value;
                selectedTypeBadge.hidden = false;
            }
            if (errorText) {
                errorText.classList.add('d-none');
            }
            modalInstance?.hide();
            intendedType = null;
        });

        const applyLiveFile = () => {
            if (!liveFile || !fileInput) return;
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(liveFile);
            fileInput.files = dataTransfer.files;
            categoryInput.value = 'live';
            if (selectedFileLabel) {
                selectedFileLabel.textContent = liveFile.name;
            }
            if (selectedTypeBadge) {
                selectedTypeBadge.textContent = 'Go Live';
                selectedTypeBadge.hidden = false;
            }
            if (errorText) {
                errorText.classList.add('d-none');
            }
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            liveModalInstance?.hide();
        };

        liveStartBtn?.addEventListener('click', async () => {
            if (typeof MediaRecorder === 'undefined') {
                alert('Live capture is not supported in this browser.');
                return;
            }
            try {
                liveStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                if (livePreview) {
                    livePreview.srcObject = liveStream;
                }
                liveChunks = [];
                const mimeType = MediaRecorder.isTypeSupported('video/webm;codecs=vp9')
                    ? 'video/webm;codecs=vp9'
                    : 'video/webm';
                liveRecorder = new MediaRecorder(liveStream, { mimeType });
                liveRecorder.ondataavailable = (event) => {
                    if (event.data && event.data.size > 0) {
                        liveChunks.push(event.data);
                    }
                };
                liveRecorder.onstart = () => {
                    updateLiveStatus('Recording…', 'bg-danger');
                    if (liveStartBtn) liveStartBtn.disabled = true;
                    if (liveStopBtn) liveStopBtn.disabled = false;
                    if (liveUseBtn) liveUseBtn.disabled = true;
                };
                liveRecorder.onstop = () => {
                    const blob = new Blob(liveChunks, { type: liveRecorder.mimeType || 'video/webm' });
                    liveFile = new File([blob], `live-${Date.now()}.webm`, { type: blob.type });
                    updateLiveStatus('Recording saved. Review and share.', 'bg-success');
                    if (liveUseBtn) liveUseBtn.disabled = false;
                    if (liveStopBtn) liveStopBtn.disabled = true;
                    if (liveStartBtn) liveStartBtn.disabled = false;
                    stopLiveStream();
                };
                liveRecorder.start();
            } catch (err) {
                console.error('Unable to access camera', err);
                updateLiveStatus('Camera access denied', 'bg-warning');
                resetLiveState();
                alert('Unable to access your camera. Please check permissions and try again.');
            }
        });

        liveStopBtn?.addEventListener('click', () => {
            if (liveRecorder && liveRecorder.state === 'recording') {
                liveRecorder.stop();
                if (liveStopBtn) liveStopBtn.disabled = true;
            }
        });

        liveUseBtn?.addEventListener('click', () => {
            if (!liveFile) {
                alert('Record a video before continuing.');
                return;
            }
            applyLiveFile();
        });

        form?.addEventListener('submit', (event) => {
            if (!fileInput?.files || fileInput.files.length === 0) {
                event.preventDefault();
                if (fileInput) {
                    fileInput.focus();
                }
                alert('Please choose a video before sharing.');
                return;
            }
            if (!categoryInput.value) {
                event.preventDefault();
                if (modalInstance) {
                    modalInstance.show();
                }
                if (errorText) {
                    errorText.classList.remove('d-none');
                }
                return;
            }
            const caption = slugify(captionInput?.value || '');
            if (titleInput) {
                if (caption.length > 0) {
                    titleInput.value = caption.substring(0, 80);
                } else {
                    const stamp = new Date().toLocaleString();
                    titleInput.value = `Video shared ${stamp}`;
                }
            }
        });
    });
});

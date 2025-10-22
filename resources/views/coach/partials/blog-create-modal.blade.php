<div class="modal fade" id="coachBlogModal" tabindex="-1" aria-labelledby="coachBlogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="coachBlogModalLabel">Upload Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="upload-dropzone" data-upload-dropzone tabindex="0">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p class="upload-text">Drag & drop your files here, or</p>
                    <button type="button" class="btn btn-primary" data-upload-browse>Browse Files</button>
                </div>
                <div class="upload-file-preview mt-3" data-upload-file-preview hidden>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt me-2"></i>
                        <span data-upload-file-name></span>
                        <small class="text-muted ms-auto" data-upload-file-details></small>
                    </div>
                </div>
                <div class="upload-file-empty mt-3" data-upload-file-empty>
                    <p class="text-muted text-center mb-0">No file selected.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-upload-confirm disabled>Confirm Upload</button>
            </div>
        </div>
    </div>
</div>

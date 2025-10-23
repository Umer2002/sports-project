<style>
/* Theme Colors Only */
[data-theme='light'] {
    --modal-bg: #ffffff;
    --text-primary: #1f2937;
    --text-secondary: #9ca3af;
    --border-color: #e5e7eb;
    --input-bg: #ffffff;
    --input-border: #d1d5db;
    --input-focus: #3b82f6;
    --button-primary: #9ca3af;
    --button-secondary: #f3f4f6;
    --button-text: #ffffff;
    --preview-bg: #f8fafc;
    --preview-border: #e5e7eb;
    --badge-bg: #1f2937;
    --badge-circle: #fbbf24;
    --badge-ribbon-bg: #6366f1;
    --badge-ribbon-text: #ffffff;
}

[data-theme='dark'] {
    --modal-bg: #1e293b;
    --text-primary: #e5e7eb;
    --text-secondary: #94a3b8;
    --border-color: #374151;
    --input-bg: #374151;
    --input-border: #4b5563;
    --input-focus: #60a5fa;
    --button-primary: #9ca3af;
    --button-secondary: #374151;
    --button-text: #ffffff;
    --preview-bg: #0f172a;
    --preview-border: #374151;
    --badge-bg: #1f2937;
    --badge-circle: #fbbf24;
    --badge-ribbon-bg: #6366f1;
    --badge-ribbon-text: #ffffff;
}

/* Modal CSS */
.modal-content {
    background-color: var(--modal-bg);
    color: var(--text-primary);
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-dialog .modal-content .modal-header {
    border-bottom: none !important;
    /* padding: 2rem 2rem 1rem 2rem; */
    position: relative;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.modal-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.5;
}

.modal-body {
    padding: 0 2rem 2rem 2rem;
}
.f-form .f-input{
    min-height: 16px !important;
    height: 22px !important;
    font-size: 12px !important;
    width: 85% !important;
}
/* Form Styles */
.form-section {
    display: flex;
    /* grid-template-columns: 200px 1fr; */
    gap: 2rem;
    margin-bottom: 2rem;
}

.upload-area {
    background-color: var(--input-bg);
    border: 2px dashed var(--input-border);
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
    flex:0.75;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: var(--input-focus);
}

.upload-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.form-fields {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.f-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem !important;
    margin-bottom: 0px !important;
}

.form-control {
    background-color: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 12px;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    width: 100%;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control::placeholder {
    color: var(--text-secondary);
}

.form-control.description {
    height: 80px;
    resize: vertical;
}

.form-control.requirements,
.form-control.rewards {
    height: 100px;
    resize: vertical;
}

/* Section Headers */
.section-header {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
    margin: 2rem 0 1rem 0;
}

/* Assign Section */
.assign-section {
    margin-bottom: 2rem;
}

.assign-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 1rem;
}

/* Award Preview */
.award-preview {
    background-color: var(--preview-bg);
    border: 1px solid var(--preview-border);
    border-radius: 16px;
    padding: 2rem;
    margin-top: 2rem;
}

.preview-content {
    display: flex;
    gap: 2rem;
    align-items: start;
}

.badge-container {
    width: 180px;
    height: 180px;
    background-color: var(--badge-bg);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.badge-circle {
    width: 120px;
    height: 120px;
    background-color: var(--badge-circle);
    border-radius: 50%;
}

.badge-ribbon {
    position: absolute;
    bottom: 15px;
    background-color: var(--badge-ribbon-bg);
    color: var(--badge-ribbon-text);
    padding: 0.375rem 0.875rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.preview-info {
    padding-top: 0.5rem;
}

.award-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.award-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.requirements-section,
.rewards-section {
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.section-content {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.4;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
}

.btn-action {
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid var(--border-color);
    cursor: pointer;
    transition: all 0.2s;
    background-color: var(--button-secondary);
    color: var(--text-primary);
}

.btn-acknowledge {
    background-color: var(--button-primary);
    color: var(--button-text);
    border-color: var(--button-primary);
}

.btn-action:hover {
    transform: translateY(-1px);
}

/* Close Button */
.btn-close {
    z-index: 99999999;
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.25rem;
    color: var(--text-secondary);
    cursor: pointer;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    .form-section{
        flex-direction: column;
    }
    .modal-body {
        padding: 1rem;
    }
    .assign-row{
        grid-template-columns: 1fr;
    }
    .modal-dialog{
        margin: 0px auto !important
    }
    .upload-area {
        padding: 2rem 1rem;
    }
    
    .badge-container {
        width: 120px;
        height: 120px;
    }
    
    .badge-circle {
        width: 80px;
        height: 80px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    .preview-content{
        flex-direction: column;
    }
}
</style>

<div class="modal fade" id="createNewAward" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-xmark"></i></button>

            <div class="modal-header">
                <div>
                    <h4 class="modal-title">Create New Award</h4>
                    <p class="modal-subtitle">Define an award, assign it to a sport, set requirements and rewards, then preview how players see it.</p>
                </div>
            </div>
            
            <!-- Body -->
            <div class="modal-body f-form">
                <!-- Form Section -->
                <div class="form-section ">
                    <!-- Upload Area -->
                    <div class="upload-area">
                        <div class="upload-text">Drop Award Image</div>
                    </div>
                    
                    <!-- Form Fields -->
                    <div class="form-fields">
                        <!-- Name and Sport Row -->
                        <div class="form-row">
                            <input type="text" class="form-control f-input" placeholder="Award Name (e.g., New Recruit!)">
                            <input type="text" class="form-control f-input" placeholder="Sport (Soccer, Hockey, ...)">
                        </div>
                        
                        <!-- Description -->
                        <textarea class="form-control description" placeholder="Description — What is this award for? (visible to players)"></textarea>
                        
                        <!-- Requirements and Rewards Row -->
                        <div class="form-row">
                            <textarea class="form-control requirements" placeholder="Requirements — e.g., 5 key passes, 2+ assists, 80% accuracy"></textarea>
                            <textarea class="form-control rewards" placeholder="Rewards — e.g., +250 XP, frame, leaderboard highlight"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Assign to Sport & Audience Section -->
                <div class="assign-section">
                    <div class="section-header">Assign to Sport & Audience</div>
                    <div class="assign-row">
                        <select class="form-control">
                            <option>Select Sport</option>
                            <option>Soccer</option>
                            <option>Hockey</option>
                            <option>Basketball</option>
                        </select>
                        <input type="text" class="form-control f-input" placeholder="Optional: Team / Division">
                        <select class="form-control">
                            <option>Country</option>
                            <option>United States</option>
                            <option>Canada</option>
                            <option>United Kingdom</option>
                        </select>
                        <select class="form-control">
                            <option>State / Province</option>
                            <option>California</option>
                            <option>New York</option>
                            <option>Texas</option>
                        </select>
                    </div>
                </div>
                
                <!-- Award Preview -->
                <div class="award-preview">
                    <div class="preview-content">
                        <!-- Badge -->
                        <div class="badge-container">
                            <div class="badge-circle"></div>
                            <div class="badge-ribbon">NEW RECRUIT</div>
                        </div>
                        
                        <!-- Preview Info -->
                        <div class="preview-info">
                            <h3 class="award-title">Award Unlocked — New Recruit</h3>
                            <p class="award-description">Welcome to the team! This badge celebrates your first milestone.</p>
                            
                            <div class="requirements-section">
                                <div class="section-title">REQUIREMENTS</div>
                                <div class="section-content">• 5 key passes • 2+ assists • 80% pass accuracy</div>
                            </div>
                            
                            <div class="rewards-section">
                                <div class="section-title">REWARDS</div>
                                <div class="section-content">+250 XP • Profile frame • Leaderboard highlight</div>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="btn-action">Share</button>
                                <button class="btn-action btn-acknowledge">Acknowledge</button>
                                <button class="btn-action">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('createNewAward');
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            modal.setAttribute('data-theme', currentTheme);
        });
        
        document.addEventListener('themeChange', function(e) {
            modal.setAttribute('data-theme', e.detail.theme);
        });
    }
});
</script>

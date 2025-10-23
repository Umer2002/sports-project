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
    --button-primary: #10b981;
    --button-secondary: #f3f4f6;
    --button-cancel: #6b7280;
    --button-draft: #6366f1;
    --button-reminder: #9ca3af;
    --dropdown-arrow: #9ca3af;
    --priority-low: #f3f4f6;
    --priority-medium: #f3f4f6;
    --priority-high: #f97316;
    --priority-critical: #8b5cf6;
    --upload-bg: #f9fafb;
    --upload-border: #d1d5db;
    --subtask-bg: #f9fafb;
    --icon-bg: #10b981;
    --icon-text: #ffffff;
}

[data-theme='dark'] {
    --modal-bg: #1e293b;
    --text-primary: #e5e7eb;
    --text-secondary: #94a3b8;
    --border-color: #374151;
    --input-bg: #374151;
    --input-border: #4b5563;
    --input-focus: #60a5fa;
    --button-primary: #10b981;
    --button-secondary: #374151;
    --button-cancel: #6b7280;
    --button-draft: #6366f1;
    --button-reminder: #4b5563;
    --dropdown-arrow: #94a3b8;
    --priority-low: #374151;
    --priority-medium: #374151;
    --priority-high: #f97316;
    --priority-critical: #8b5cf6;
    --upload-bg: #2d3748;
    --upload-border: #4b5563;
    --subtask-bg: #2d3748;
    --icon-bg: #10b981;
    --icon-text: #ffffff;
}

/* Modal CSS */
.modal-dialog {
    max-width: 1200px;
}
.modal-content {
    background-color: var(--modal-bg);
    color: var(--text-primary);
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-width: 1200px;
    width: 100vw;
}

.modal-header {
    border-bottom: none;
    padding: 1.5rem 2rem 1rem 2rem;
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.task-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    color: var(--icon-text);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.125rem;
}

.header-content {
    flex: 1;
}

.modal-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.modal-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.template-select {
    background-color: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--text-secondary);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    min-width: 140px;
}

.save-template-btn {
    background: linear-gradient(135deg, #7C3AED 0%, #39A2FF 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
}

.modal-body {
    padding: 0 2rem 2rem 2rem;
}

/* Main Layout */
.task-form-container {
    display: flex;
    gap: 2rem;
}

.left-section {
    flex: 1;
    min-width: 0;
}

.right-section {
    width: 280px;
    flex-shrink: 0;
}

/* Form Styles */
.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.form-control {
    box-sizing: border-box !important;
    background-color: var(--input-bg);
    border: 1px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 8px;
    padding: 0.75rem;
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
    height: 120px;
    resize: vertical;
}

.form-control.date-input {
    color: var(--text-secondary);
}

/* Dropdown Styles */
.dropdown-container {
    position: relative;
}

.dropdown-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}

/* Priority Buttons */
.priority-group {
    display: flex;
    gap: 0.5rem;
}

.priority-btn {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--input-border);
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.priority-btn.low {
    background-color: var(--priority-low);
    color: var(--text-secondary);
}

.priority-btn.medium {
    background-color: var(--priority-medium);
    color: var(--text-secondary);
}

.priority-btn.high {
    background: linear-gradient(135deg, #F59E0B 0%, #F97316 100%);
    color: white;
}

.priority-btn.critical {
    background: linear-gradient(135deg, #7C3AED 0%, #39A2FF 100%);
    color: white;
}

.priority-btn.active {
    border-width: 2px;
}

/* Subtasks Section */
.subtasks-section {
    margin-bottom: 1.5rem;
}

.subtasks-container {
    background-color: var(--subtask-bg);
    border: 1px solid var(--input-border);
    border-radius: 8px;
    padding: 1rem;
}

.subtask-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--input-border);
}

.subtask-item:last-child {
    border-bottom: none;
}

.subtask-bullet {
    width: 6px;
    height: 6px;
    background-color: var(--text-secondary);
    border-radius: 50%;
    flex-shrink: 0;
}

.subtask-text {
    flex: 1;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.add-subtask-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
}

.add-subtask-btn:hover {
    color: var(--text-primary);
}

/* Attachments Section */
.attachments-section {
    margin-bottom: 1.5rem;
}

.upload-area {
    background-color: var(--upload-bg);
    border: 2px dashed var(--upload-border);
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    justify-content: space-between !important;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-area:hover {
    border-color: var(--input-focus);
}

.upload-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.upload-btn {
    background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    cursor: pointer;
}

/* Notification Section */
.notify-section {
    margin-bottom: 1.5rem;
}

.notify-buttons {
    display: flex;
    gap: 0.75rem;
}

.notify-btn {
    flex: 1;
    background-color: var(--button-secondary);
    border: 1px solid var(--input-border);
    color: var(--text-primary);
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
}

.notify-btn:hover {
    background-color: var(--input-bg);
}

.notify-btn.active {
    background-color: var(--button-primary);
    color: white;
    border-color: var(--button-primary);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
}

.btn-action {
    flex: 1;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid var(--input-border);
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel {
    background-color: var(--button-secondary);
    color: var(--button-cancel);
    border-color: var(--input-border);
}

.btn-assign {
background: linear-gradient(135deg, #12E0B0 0%, #39A2FF 100%);
    color: white;
    border-color: var(--button-primary);
}

.btn-reminder {
    background-color: var(--button-reminder);
    color: white;
    border-color: var(--button-reminder);
}

.btn-draft {
    background-color: var(--button-draft);
    color: white;
    border-color: var(--button-draft);
}

.btn-action:hover {
    transform: translateY(-1px);
}

/* Close Button */
.btn-close {
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

/* User Avatar */
.user-avatar {
    width: 24px;
    height: 24px;
    background-color: var(--text-secondary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    margin-right: 0.5rem;
}

.user-select {
    display: flex;
    align-items: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95vw;
        margin: 1rem;
    }
    
    .modal-header {
        padding: 1rem 1.5rem 0.5rem 1.5rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .header-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .modal-body {
        padding: 0 1.5rem 1.5rem 1.5rem;
    }
    
    .task-form-container {
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .right-section {
        width: 100%;
    }
    
    .priority-group {
        flex-wrap: wrap;
    }
    
    .priority-btn {
        min-width: calc(50% - 0.25rem);
    }
    
    .notify-buttons {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .modal-header {
        padding: 1rem;
    }
    
    .modal-body {
        padding: 0 1rem 1rem 1rem;
    }
    
    .header-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .template-select,
    .save-template-btn {
        width: 100%;
    }
    
    .priority-group {
        flex-direction: column;
    }
    
    .priority-btn {
        min-width: 100%;
    }
}
</style>

<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>

            <div class="modal-header">
                <div class="task-icon"><i style="color:#08121A" class="fa-solid fa-square-check"></i></div>
                <div class="header-content">
                    <h4 class="modal-title">Assign Task</h4>
                    <p class="modal-subtitle">CLUB DASHBOARD</p>
                </div>
                <div class="header-actions">
                    <select class="template-select">
                        <option>Template: None</option>
                        <option>Template: Tournament Setup</option>
                        <option>Template: Player Review</option>
                    </select>
                    <button class="save-template-btn">Save as Template</button>
                </div>
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                <div class="task-form-container">
                    <!-- Left Section -->
                    <div class="left-section">
                        <!-- Task Title -->
                        <div class="form-group">
                            <label class="form-label">TASK TITLE</label>
                            <input type="text" class="form-control" placeholder="e.g., Confirm hotel bookings for out-of-town tournament">
                        </div>
                        
                        <!-- Description -->
                        <div class="form-group">
                            <label class="form-label">DESCRIPTION</label>
                            <textarea class="form-control description" placeholder="Add details, links, or notes for the assignee...
Markdown supported — paste screenshots below."></textarea>
                        </div>
                        
                        <!-- Subtasks -->
                        <div class="subtasks-section">
                            <label class="form-label">SUBTASKS</label>
                            <div class="subtasks-container">
                                <div class="subtask-item">
                                    <div class="subtask-bullet"></div>
                                    <div class="subtask-text">Call hotel and confirm block rate</div>
                                </div>
                                <div class="subtask-item">
                                    <div class="subtask-bullet"></div>
                                    <div class="subtask-text">Share address & map link in team chat</div>
                                </div>
                                <button class="add-subtask-btn">
                                    <span>+</span>
                                    <span>Add</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Attachments -->
                        <div class="attachments-section">
                            <label class="form-label">ATTACHMENTS</label>
                            <div class="upload-area">
                                <div class="upload-text">Drag & drop files or click to upload</div>
                                <button class="upload-btn">Upload</button>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-action btn-cancel">Cancel</button>
                            <button class="btn-action btn-assign">Assign Task</button>
                        </div>
                    </div>
                    
                    <!-- Right Section -->
                    <div class="right-section">
                        <!-- Club -->
                        <div class="form-group">
                            <label class="form-label">CLUB</label>
                            <div class="dropdown-container">
                                <select class="form-control dropdown-select">
                                    <option>
                                        <div class="user-select">
                                            <div class="user-avatar">VP</div>
                                            Vincent Porter
                                        </div>
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Player -->
                        <div class="form-group">
                            <label class="form-label">PLAYER</label>
                            <div class="dropdown-container">
                                <select class="form-control dropdown-select">
                                    <option>
                                        <div class="user-select">
                                            <div class="user-avatar">VP</div>
                                            Vincent Porter
                                        </div>
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Due Date -->
                        <div class="form-group">
                            <label class="form-label">DUE DATE</label>
                            <input type="date" class="form-control date-input" value="2025-08-30">
                        </div>
                        
                        <!-- Priority -->
                        <div class="form-group">
                            <label class="form-label">PRIORITY</label>
                            <div class="priority-group">
                                <button class="priority-btn low">Low</button>
                                <button class="priority-btn medium">Medium</button>
                                <button class="priority-btn high">High</button>
                                <button class="priority-btn critical">Critical</button>
                            </div>
                        </div>
                        
                        <!-- Status -->
                        <div class="form-group">
                            <label class="form-label">STATUS</label>
                            <div class="dropdown-container">
                                <select class="form-control dropdown-select">
                                    <option>To Do</option>
                                    <option>In Progress</option>
                                    <option>Review</option>
                                    <option>Complete</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Related -->
                        <div class="form-group">
                            <label class="form-label">RELATED</label>
                            <div class="dropdown-container">
                                <select class="form-control dropdown-select">
                                    <option>Tournament: Summer Cup • Team: U14 Elite</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Notify -->
                        <div class="notify-section">
                            <label class="form-label">NOTIFY</label>
                            <div class="notify-buttons">
                                <button class="notify-btn">Email assignee</button>
                                <button class="notify-btn">Post in team chat</button>
                            </div>
                        </div>
                        
                        <!-- Bottom Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-action btn-reminder">Set Reminder</button>
                            <button class="btn-action btn-draft">Save Draft</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assignTaskModal');
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            modal.setAttribute('data-theme', currentTheme);
        });
        
        document.addEventListener('themeChange', function(e) {
            modal.setAttribute('data-theme', e.detail.theme);
        });
    }

    // Priority button functionality
    const priorityButtons = document.querySelectorAll('.priority-btn');
    priorityButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            priorityButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Notify button functionality
    const notifyButtons = document.querySelectorAll('.notify-btn');
    notifyButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });

    // Add subtask functionality
    const addSubtaskBtn = document.querySelector('.add-subtask-btn');
    if (addSubtaskBtn) {
        addSubtaskBtn.addEventListener('click', function() {
            // Add subtask logic here
            console.log('Add subtask clicked');
        });
    }
});
</script>

<!-- Assign Task Modal -->
<div class="modal fade" id="assignTaskModal1" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <div class="assign-task-modal">
          <!-- Header -->
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="header-left d-flex align-items-center gap-3">
              <img src="{{ url('assets/club-dashboard-main/assets/TaskIcon.png') }}" alt="" width="40" height="40" />
              <div class="task-header-content">
                <div>Assign Task</div>
              </div>
            </div>

            <div class="header-right d-flex align-items-center gap-3">
              <span class="template-badge d-flex align-items-center">Template: None</span>
              <button class="save-template-btn">Save as Template</button>
              <button class="close-btn d-flex align-items-center justify-content-center" data-bs-dismiss="modal">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <!-- Body -->
          <form action="{{ route('coach.tasks.store') }}" method="POST" enctype="multipart/form-data" id="taskAssignmentForm">
            @csrf
            <div class="assign-task-modal-body">
              <!-- Left Column -->
              <div class="assign-task-left-column">
                <!-- Task Title -->
                <div class="mb-3">
                  <label class="form-label">TASK TITLE</label>
                  <input type="text" name="title" class="form-control" placeholder="e.g., Confirm hotel bookings for out-of-town tournament" required style="color: #65686d !important;"/>
                </div>

                <!-- Description -->
                <div class="mb-3">
                  <label class="form-label">DESCRIPTION</label>
                  <textarea name="description" class="form-control description-field" placeholder="Add details, links, or notes for the assignee...&#10;&#10;Markdown supported - paste screenshots below." style="color: #65686d !important;"></textarea>
                </div>

                <!-- Subtasks -->
                <label class="form-label">SUBTASKS</label>
                <div class="subtasks-section" id="subtasksContainer">
                  <div class="subtask-item">
                    <div class="subtask-bullet"></div>
                    <input type="text" name="subtasks[]" class="form-control subtask-input" placeholder="Enter subtask..." style="color: #65686d !important;"/>
                  </div>
                  <button type="button" class="add-subtask-btn" id="addSubtaskBtn">
                    <i class="fas fa-plus"></i>
                    Add
                  </button>
                </div>

                <!-- Attachments -->
                <div class="attachments-section">
                  <label class="form-label">ATTACHMENTS</label>
                  <div class="upload-area" id="uploadArea">
                    <p>Drag & drop files or click to upload</p>
                    <button type="button" class="upload-btn" id="uploadBtn">Upload</button>
                    <input type="file" name="attachments[]" id="fileInput" multiple style="display: none;" />
                  </div>
                  <div id="fileList" class="mt-2"></div>
                </div>
              </div>

              <!-- Right Sidebar -->
              <div class="assign-task-right-sidebar">
                <!-- Assignee -->
                <div class="">
                  <label class="form-label">ASSIGNEE</label>
                  <select name="assigned_to" class="status-select sidebar-section" required style="color: #65686d !important;">
                    <option value="">Select assignee</option>
                    @foreach($players as $player)
                      <option value="{{ $player->user_id }}">
                        <div class="d-flex align-items-center">
                          <div class="user-avatar">{{ strtoupper(substr($player->name, 0, 2)) }}</div>
                          <span>{{ $player->name }}</span>
                        </div>
                      </option>
                    @endforeach
                  </select>
                </div>

                <!-- Due Date -->
                <div class="">
                  <label class="form-label">DUE DATE</label>
                  <input type="datetime-local" name="due_date" class="status-select sidebar-section" value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" style="color: #65686d !important;"/>
                </div>

                <!-- Priority -->
                <div class="">
                  <label class="form-label">PRIORITY</label>
                  <div class="priority-buttons sidebar-section">
                    <input type="radio" name="priority" value="low" id="priority_low" class="d-none" />
                    <label for="priority_low" class="priority-btn low">Low</label>
                    
                    <input type="radio" name="priority" value="medium" id="priority_medium" class="d-none" checked />
                    <label for="priority_medium" class="priority-btn medium active">Medium</label>
                    
                    <input type="radio" name="priority" value="high" id="priority_high" class="d-none" />
                    <label for="priority_high" class="priority-btn high">High</label>
                    
                    <input type="radio" name="priority" value="critical" id="priority_critical" class="d-none" />
                    <label for="priority_critical" class="priority-btn critical">Critical</label>
                  </div>
                </div>

                <!-- Status -->
                <div class="">
                  <label class="form-label">STATUS</label>
                  <select name="status" class="status-select sidebar-section">
                    <option value="pending">To Do</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                  </select>
                </div>

                <!-- Related -->
                <div class="">
                  <label class="form-label">RELATED</label>
                  <select name="related_team" class="status-select sidebar-section">
                    <option value="">Select team</option>
                   @if(isset($teams) && is_iterable($teams))
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">
                            Team: {{ $team->name }} • Club: {{ $team->club->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    @else
                        <option disabled>No teams available</option>
                    @endif
                  </select>
                </div>

                <!-- Notify -->
                <div class="">
                  <label class="form-label">NOTIFY</label>
                  <div class="sidebar-section notification-options" style="flex-direction: row;">
                    <input type="checkbox" name="notify_email" id="notify_email" value="1" class="d-none" />
                    <label for="notify_email" class="notification-btn" style="flex: 1;">Email</label>
                    
                    <input type="checkbox" name="notify_chat" id="notify_chat" value="1" class="d-none" />
                    <label for="notify_chat" class="notification-btn" style="flex: 1;">Chat</label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
              <div class="footer-left">
                <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn-primary">Assign Task</button>
              </div>
              <div class="footer-right">
                <button type="button" class="btn-secondary" id="setReminderBtn">Set Reminder</button>
                <button type="button" class="btn-save-draft" id="saveDraftBtn">Save Draft</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Priority button selection (works as radio - only one can be selected)
    const priorityBtns = document.querySelectorAll('.priority-btn');
    priorityBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all priority buttons
            priorityBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Check the corresponding radio input
            const radioId = this.getAttribute('for');
            document.getElementById(radioId).checked = true;
        });
    });

    // Add subtask functionality
    const addSubtaskBtn = document.getElementById('addSubtaskBtn');
    const subtasksContainer = document.getElementById('subtasksContainer');
    
    addSubtaskBtn.addEventListener('click', function() {
        const newSubtask = document.createElement('div');
        newSubtask.className = 'subtask-item';
        newSubtask.innerHTML = `
            <div class="subtask-bullet"></div>
            <input type="text" name="subtasks[]" class="form-control subtask-input" placeholder="Enter subtask..." style="color: #65686d !important;" />
            <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-subtask">×</button>
        `;
        subtasksContainer.insertBefore(newSubtask, addSubtaskBtn);
        
        // Add remove functionality
        newSubtask.querySelector('.remove-subtask').addEventListener('click', function() {
            newSubtask.remove();
        });
        
        // Focus on the new input
        newSubtask.querySelector('input').focus();
    });

    // File upload functionality
    const uploadArea = document.getElementById('uploadArea');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    let selectedFiles = [];
    
    uploadArea.addEventListener('click', function(e) {
        if (e.target !== uploadBtn && !uploadBtn.contains(e.target)) {
            return;
        }
        fileInput.click();
    });
    
    uploadBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#00d4aa';
        this.style.backgroundColor = 'rgba(0, 212, 170, 0.05)';
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.borderColor = '#3a4a5c';
        this.style.backgroundColor = 'rgba(30, 37, 51, 0.5)';
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#3a4a5c';
        this.style.backgroundColor = 'rgba(30, 37, 51, 0.5)';
        
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });
    
    function handleFiles(files) {
        // Add new files to selectedFiles array
        Array.from(files).forEach(file => {
            selectedFiles.push(file);
        });
        
        // Update the file input with all selected files
        updateFileInput();
        
        // Display all files
        displayFiles();
    }
    
    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
    }
    
    function displayFiles() {
        fileList.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2';
            fileItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-file me-2"></i>
                    <span>${file.name}</span>
                    <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">×</button>
            `;
            fileList.appendChild(fileItem);
            
            // Add remove functionality
            fileItem.querySelector('.remove-file').addEventListener('click', function() {
                const fileIndex = parseInt(this.getAttribute('data-index'));
                selectedFiles.splice(fileIndex, 1);
                updateFileInput();
                displayFiles();
            });
        });
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Notification buttons (work as checkboxes - multiple can be selected)
    document.querySelectorAll('.notification-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle active class
            this.classList.toggle('active');
            
            // Toggle the corresponding checkbox
            const checkboxId = this.getAttribute('for');
            const checkbox = document.getElementById(checkboxId);
            checkbox.checked = !checkbox.checked;
        });
    });

    // Save draft functionality
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        // In a real app, you'd save the form data as a draft
        alert('Draft saved! (This is a placeholder)');
    });

    // Set reminder functionality
    document.getElementById('setReminderBtn').addEventListener('click', function() {
        // In a real app, you'd set up a reminder
        alert('Reminder set! (This is a placeholder)');
    });
});
</script>

<style>
/* Modal Styling */
.assign-task-modal {
    padding: 20px;
    border: none;
}

.assign-task-modal-body {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
    margin-top: 30px;
}

.assign-task-left-column {
    display: flex;
    flex-direction: column;
}

.assign-task-right-sidebar {
    display: flex;
    flex-direction: column;
}

/* Form Labels */
.form-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 8px;
    text-transform: uppercase;
}

/* Form Controls */
.form-control, .status-select {
    background: rgba(30, 37, 51, 0.5);
    /* border: 1px solid #3a4a5c; */
    color: #e2e8f0 !important;
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 14px;
}

.form-control:focus, .status-select:focus {
    background: rgba(30, 37, 51, 0.7);
    border-color: #00d4aa;
    color: #e2e8f0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 212, 170, 0.15);
}

.form-control::placeholder {
    color: #64748b !important;
    opacity: 0.7;
}

.description-field {
    min-height: 100px;
    resize: vertical;
}

/* Subtasks */
.subtasks-section {
    background: rgba(30, 37, 51, 0.3);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.subtask-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.subtask-bullet {
    width: 8px;
    height: 8px;
    background: #64748b;
    border-radius: 50%;
    margin-right: 10px;
    flex-shrink: 0;
}

.subtask-input {
    background: transparent;
    border: none;
    color: #3b3e42 !important;
    padding: 5px 10px;
    font-size: 14px;
}

.subtask-input:focus {
    background: rgba(30, 37, 51, 0.5);
    border: 1px solid #3a4a5c;
    color: #505357 !important;
}

.subtask-input::placeholder {
    color: #64748b !important;
    opacity: 0.7;
}

.add-subtask-btn {
    background: transparent;
    /* border: 1px dashed #3a4a5c; */
    color: #64748b;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
}

.add-subtask-btn:hover {
    border-color: #00d4aa;
    color: #00d4aa;
}

/* Upload Area */
.attachments-section {
    margin-top: 20px;
}

.upload-area {
    background: rgba(30, 37, 51, 0.3);
    /* border: 2px dashed #3a4a5c; */
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-area:hover {
    border-color: #00d4aa;
    background: rgba(0, 212, 170, 0.05);
}

.upload-area p {
    color: #94a3b8;
    margin-bottom: 10px;
    font-size: 14px;
}

.upload-btn {
    background: transparent;
    /* border: 1px solid #3a4a5c; */
    color: #e2e8f0;
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-btn:hover {
    background: rgba(0, 212, 170, 0.1);
    border-color: #00d4aa;
    color: #00d4aa;
}

/* Sidebar Sections */
.sidebar-section {
    margin-bottom: 8px;
    border: none;
}

/* Priority Buttons */
.priority-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.priority-btn {
    flex: 1;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    /* border: 1px solid #3a4a5c; */
    background: rgba(30, 37, 51, 0.5);
    color: #94a3b8;
}

.priority-btn:hover {
    border-color: #00d4aa;
    color: #e2e8f0;
}

.priority-btn.active {
    background: linear-gradient(135deg, #12e0b0 0%, #39a2ff 100%);
    border-color: transparent;
    color: #fff;
}

.priority-btn.low.active {
    background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
}

.priority-btn.medium.active {
    background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
}

.priority-btn.high.active {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.priority-btn.critical.active {
    background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
}

/* Notification Buttons */
.notification-options {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.notification-btn {
    padding: 10px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    /* border: 1px solid #3a4a5c; */
    background: rgba(30, 37, 51, 0.5);
    color: #94a3b8;
}

.notification-btn:hover {
    border-color: #00d4aa;
    color: #e2e8f0;
}

.notification-btn.active {
    background: linear-gradient(135deg, #12e0b0 0%, #39a2ff 100%);
    border-color: transparent;
    color: #fff;
}

/* Header Styling */
.task-header-content div {
    font-size: 20px;
    font-weight: 600;
    color: #e2e8f0;
}

.task-header-content p {
    font-size: 11px;
    color: #64748b;
    letter-spacing: 0.5px;
}

.template-badge {
    background: rgba(30, 37, 51, 0.5);
    /* border: 1px solid #3a4a5c; */
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    color: #94a3b8;
}

.save-template-btn {
    background: transparent;
    /* border: 1px solid #3a4a5c; */
    color: #94a3b8;
    padding: 6px 16px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.3s;
}

.save-template-btn:hover {
    border-color: #00d4aa;
    color: #00d4aa;
}

.close-btn {
    background: transparent;
    /* border: 1px solid #3a4a5c; */
    color: #94a3b8;
    width: 36px;
    height: 36px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
}

.close-btn:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #ef4444;
    color: #ef4444;
}

/* Footer Buttons */
.modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    padding-top: 20px;
}

.footer-left, .footer-right {
    display: flex;
    gap: 10px;
}

.btn-secondary {
    background: transparent;
    /* border: 1px solid #3a4a5c; */
    color: #94a3b8;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-secondary:hover {
    border-color: #64748b;
    color: #e2e8f0;
}

.btn-primary {
    background: linear-gradient(135deg, #12e0b0 0%, #39a2ff 100%);
    border: none;
    color: #fff;
    padding: 10px 24px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(18, 224, 176, 0.3);
}

.btn-save-draft {
    background: transparent;
    /* border: 1px solid #3a4a5c; */
    color: #94a3b8;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save-draft:hover {
    border-color: #00d4aa;
    color: #00d4aa;
}

/* File List */
.file-item {
    background: rgba(30, 37, 51, 0.5);
    border-color: #3a4a5c;
}
.light .assign-task-modal .sidebar-section, .light .assign-task-modal .sidebar-section select {
    border: none !important;
}
.light .assign-task-modal .subtasks-section {
    background: #f1f1f1 !important;
    border: none !important;
    border:none;
}
/* Responsive */
@media (max-width: 768px) {
    .assign-task-modal-body {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .header-right {
        flex-wrap: wrap;
    }
}
</style>


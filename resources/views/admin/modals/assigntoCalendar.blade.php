@php
    // Sample data - replace with your actual data source
    $reminderOptions = ['30 min before', '1 hour before', '2 hours before', '1 day before'];
    $tagOptions = ['General', 'Follow-up', 'Urgent', 'Meeting', 'Training', 'Important'];
@endphp

<style>
/* Light Theme */
[data-theme='light'] .calendar-modal {
    background-color: #ffffff;
    color: #1f2937;
}

[data-theme='light'] .modal-content {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

[data-theme='light'] .modal-header {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border-bottom: none !important;
}

[data-theme='light'] .modal-body {
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

[data-theme='light'] .modal-footer {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border-top: none !important;
}

[data-theme='light'] .modal-title {
    color: #1f2937 !important;
    font-weight: 600;
}

[data-theme='light'] .modal-subtitle {
    color: #6b7280;
    font-size: 0.875rem;
}

[data-theme='light'] .form-label {
    color: #374151 !important;
    font-weight: 600;
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

[data-theme='light'] .form-control,
[data-theme='light'] .form-select {
    background-color: #ffffff !important;
    border: 1px solid #d1d5db !important;
    color: #1f2937 !important;
    border-radius: 6px !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.875rem;
    min-height: 44px;
}

[data-theme='light'] .form-control:focus,
[data-theme='light'] .form-select:focus {
    background-color: #ffffff !important;
    border-color: #3b82f6 !important;
    color: #1f2937 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    outline: none;
}

[data-theme='light'] .form-control::placeholder {
    color: #9ca3af !important;
    font-style: italic;
}

[data-theme='light'] .custom-switch {
    position: relative;
    width: 48px;
    height: 24px;
}

[data-theme='light'] .switch-input {
    opacity: 0;
    width: 0;
    height: 0;
}

[data-theme='light'] .switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: 0.3s;
    border-radius: 24px;
}

[data-theme='light'] .switch-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: 0.3s;
    border-radius: 20px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

[data-theme='light'] .switch-input:checked + .switch-slider {
    background-color: #3b82f6;
}

[data-theme='light'] .switch-input:checked + .switch-slider:before {
    transform: translateX(24px);
}

[data-theme='light'] .tag-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

[data-theme='light'] .tag-btn {
    background-color: #f3f4f6;
    color: #6b7280;
    border: 1px solid #d1d5db;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

[data-theme='light'] .tag-btn:hover {
    background-color: #e5e7eb;
    border-color: #9ca3af;
}

[data-theme='light'] .tag-btn.active {
    background-color: #8b5cf6;
    color: #ffffff;
    border-color: #8b5cf6;
}

[data-theme='light'] .btn-cancel {
    background-color: #f3f4f6;
    color: #6b7280;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='light'] .btn-cancel:hover {
    background-color: #e5e7eb;
    color: #374151;
}

[data-theme='light'] .btn-save {
    background-color: #8b5cf6;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='light'] .btn-save:hover {
    background-color: #7c3aed;
    color: #ffffff;
}

/* Dark Theme */
[data-theme='dark'] .calendar-modal {
    background-color: #0f172a;
    color: #e5e7eb;
}

[data-theme='dark'] .modal-content {
    background-color: #1e293b !important;
    color: #e5e7eb !important;
    border: 1px solid #374151 !important;
    border-radius: 16px !important;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5) !important;
}

[data-theme='dark'] .modal-header {
    background-color: #1e293b !important;
    color: #e5e7eb !important;
    border-bottom: none !important;
}

[data-theme='dark'] .modal-body {
    background-color: #1e293b !important;
    color: #e5e7eb !important;
}

[data-theme='dark'] .modal-footer {
    background-color: #1e293b !important;
    color: #e5e7eb !important;
    border-top: none !important;
}

[data-theme='dark'] .modal-title {
    color: #e5e7eb !important;
    font-weight: 600;
}

[data-theme='dark'] .modal-subtitle {
    color: #94a3b8;
    font-size: 0.875rem;
}

[data-theme='dark'] .form-label {
    color: #e5e7eb !important;
    font-weight: 600;
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

[data-theme='dark'] .form-control,
[data-theme='dark'] .form-select {
    background-color: #1e293b !important;
    border: 1px solid #374151 !important;
    color: #e5e7eb !important;
    border-radius: 6px !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.875rem;
    min-height: 44px;
}

[data-theme='dark'] .form-control:focus,
[data-theme='dark'] .form-select:focus {
    background-color: #1e293b !important;
    border-color: #3b82f6 !important;
    color: #e5e7eb !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
    outline: none;
}

[data-theme='dark'] .form-control::placeholder {
    color: #6b7280 !important;
    font-style: italic;
}

[data-theme='dark'] .custom-switch {
    position: relative;
    width: 48px;
    height: 24px;
}

[data-theme='dark'] .switch-input {
    opacity: 0;
    width: 0;
    height: 0;
}

[data-theme='dark'] .switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #374151;
    transition: 0.3s;
    border-radius: 24px;
}

[data-theme='dark'] .switch-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 2px;
    bottom: 2px;
    background-color: #e5e7eb;
    transition: 0.3s;
    border-radius: 20px !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

[data-theme='dark'] .switch-input:checked + .switch-slider {
    background-color: #3b82f6;
}

[data-theme='dark'] .switch-input:checked + .switch-slider:before {
    transform: translateX(24px);
}

[data-theme='dark'] .tag-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

[data-theme='dark'] .tag-btn {
    background-color: #374151;
    color: #94a3b8;
    border: 1px solid #4b5563;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

[data-theme='dark'] .tag-btn:hover {
    background-color: #4b5563;
    border-color: #6b7280;
    color: #e5e7eb;
}

[data-theme='dark'] .tag-btn.active {
    background-color: #8b5cf6;
    color: #ffffff;
    border-color: #8b5cf6;
}

[data-theme='dark'] .btn-cancel {
    background-color: #374151;
    color: #94a3b8;
    border: 1px solid #4b5563;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='dark'] .btn-cancel:hover {
    background-color: #4b5563;
    color: #e5e7eb;
}

[data-theme='dark'] .btn-save {
    background-color: #8b5cf6;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

[data-theme='dark'] .btn-save:hover {
    background-color: #7c3aed;
    color: #ffffff;
}

/* Close button theme support */
[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}

/* Common styles */
.calendar-modal {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
    align-items: flex-start;
}

.form-col {
    flex: 1;
}

.form-col-auto {
    flex: 0 0 auto;
    min-width: 140px;
}

.time-fields {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.time-fields .form-group {
    flex: 1;
    margin-bottom: 0;
}

/* Layout adjustments for exact match */
.date-time-row {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.date-col {
    flex: 2;
    min-width: 200px;
}

.allday-col {
    flex: 0 0 auto;
    min-width: 140px;
    text-align: right;
}

.reminder-tag-row {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.reminder-col {
    flex: 1;
    min-width: 200px;
}

.tag-col {
    flex: 2;
    min-width: 300px;
}

/* Full width fields */
.full-width {
    width: 100%;
}

/* Description specific styling */
.description-textarea {
    min-height: 120px;
    resize: vertical;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .form-row,
    .date-time-row,
    .reminder-tag-row,
    .time-fields {
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95vw !important;
        margin: 0.5rem !important;
    }
    
    .form-row,
    .date-time-row,
    .reminder-tag-row,
    .time-fields {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-col,
    .form-col-auto,
    .date-col,
    .allday-col,
    .reminder-col,
    .tag-col {
        width: 100%;
        min-width: auto;
        text-align: left;
    }
    
    .allday-col {
        text-align: left;
    }
    
    .allday-col .d-flex {
        justify-content: flex-start !important;
    }
    
    .modal-content {
        padding: 1rem !important;
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .modal-footer {
        padding: 1rem !important;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-cancel,
    .btn-save {
        width: 45%;
    }
    
    .tag-buttons {
        gap: 0.5rem;
    }
}

@media (max-width: 576px) {
    .modal-xl {
        max-width: 100vw !important;
        margin: 0 !important;
    }
    
    .modal-content {
        border-radius: 0 !important;
        height: 100vh !important;
        padding: 0.75rem !important;
    }
    
    .modal-body {
        max-height: calc(100vh - 150px) !important;
        overflow-y: auto;
        padding: 0.75rem !important;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .modal-title {
        font-size: 1.1rem !important;
    }
    
    .modal-subtitle {
        font-size: 0.8rem !important;
    }
    
    .tag-btn {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
}
.f-modal .form-control{
    box-sizing: border-box !important;
}
.f-modal [type="checkbox"]:checked+span:not(.lever):before {
    top: 0px;
    left: -8px;
    width: 18px;
    height: 18px;
    border-top: 0px solid white !important;
    border-left: 0px solid white !important;
    border-right: 0px solid white !important;
    border-bottom: 0px solid white !important;
}
</style>

<div class="modal f-modal fade" id="assigntoCalendar" tabindex="-1" aria-labelledby="assigntoCalendarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content calendar-modal">
            <!-- Header -->
            <div class="modal-header pb-3">
                <div>
                    <h4 class="modal-title mb-1">Assign to My Calendar</h4>
                    <p class="modal-subtitle mb-0">Quickly create a personal calendar event.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-xmark"></i></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-4">
                <form id="calendarEventForm">
                    <!-- Title -->
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control full-width" placeholder="e.g., Call with club director" required>
                    </div>

                    <!-- Date and All-day -->
                    <div class="date-time-row">
                        <div class="date-col">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="allday-col">
                            <label class="form-label">All-day</label>
                            <div class="d-flex align-items-center justify-content-end" style="height: 44px; margin-top: 0.5rem;">
                                <label class="custom-switch">
                                    <input type="checkbox" class="switch-input" id="allDayToggle">
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Start Time and End Time -->
                    <div class="time-fields" id="timeFields">
                        <div class="form-group">
                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control" value="09:00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">End Time</label>
                            <input type="time" class="form-control" value="10:00">
                        </div>
                    </div>

                    <!-- Reminder and Tag -->
                    <div class="reminder-tag-row">
                        <div class="reminder-col">
                            <label class="form-label">Reminder</label>
                            <select class="form-select">
                                <option value="30 min before" selected>30 min before</option>
                                @foreach($reminderOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="tag-col">
                            <label class="form-label">Tag</label>
                            <div class="tag-buttons">
                                @foreach($tagOptions as $tag)
                                    <button type="button" class="tag-btn {{ $tag === 'General' ? 'active' : '' }}" data-tag="{{ $tag }}">{{ $tag }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedTag" value="General">
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control full-width" placeholder="Optional">
                    </div>

                    <!-- Description -->
                    <div class="form-group mb-0">
                        <label class="form-label">Description</label>
                        <textarea class="form-control full-width description-textarea" rows="5" placeholder="Add details, links, agenda..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer p-4 pt-3 d-flex justify-content-end gap-3">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="calendarEventForm" class="btn-save">Save Event</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('assigntoCalendar');
    
    function applyThemeToModal() {
        if (modal) {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            modal.setAttribute('data-theme', currentTheme);
            
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.setAttribute('data-theme', currentTheme);
            }
        }
    }
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function() {
            applyThemeToModal();
        });
        
        applyThemeToModal();
    }

    // Listen for theme changes and update modal
    document.addEventListener('themeChange', function(e) {
        if (modal) {
            modal.setAttribute('data-theme', e.detail.theme);
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.setAttribute('data-theme', e.detail.theme);
            }
        }
    });

    // All-day toggle functionality
    const allDayToggle = document.getElementById('allDayToggle');
    const timeFields = document.getElementById('timeFields');

    if (allDayToggle) {
        allDayToggle.addEventListener('change', function() {
            if (this.checked) {
                timeFields.style.display = 'none';
            } else {
                timeFields.style.display = 'flex';
            }
        });
    }

    // Tag selection functionality
    const tagButtons = document.querySelectorAll('.tag-btn');
    const selectedTagInput = document.getElementById('selectedTag');

    tagButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            tagButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update hidden input
            if (selectedTagInput) {
                selectedTagInput.value = this.getAttribute('data-tag');
            }
        });
    });

    // Form submission
    const form = document.getElementById('calendarEventForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the data to your backend
            console.log('Calendar event data:', {
                title: form.querySelector('input[placeholder*="Call with"]').value,
                date: form.querySelector('input[type="date"]').value,
                allDay: allDayToggle.checked,
                startTime: form.querySelector('input[value="09:00"]').value,
                endTime: form.querySelector('input[value="10:00"]').value,
                reminder: form.querySelector('select').value,
                tag: selectedTagInput.value,
                location: form.querySelector('input[placeholder="Optional"]').value,
                description: form.querySelector('textarea').value
            });
            
            // Close modal after successful submission
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Show success message (you can customize this)
            alert('Calendar event created successfully!');
        });
    }
});
</script>
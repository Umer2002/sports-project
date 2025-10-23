{{-- 
    Admin Modal Theme Support - Include this in any modal file 
    Usage: @include('admin.modals._modal-theme')
--}}

<style>
/* Modal Dark Theme Support for Admin System */
[data-theme='dark'] .modal-content {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
    border: 1px solid #1f2937 !important;
}

[data-theme='dark'] .modal-header {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
    border-bottom: 1px solid #1f2937 !important;
}

[data-theme='dark'] .modal-body {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
}

[data-theme='dark'] .modal-footer {
    background-color: #0f172a !important;
    color: #e5e7eb !important;
    border-top: 1px solid #1f2937 !important;
}

[data-theme='dark'] .modal-title {
    color: #e5e7eb !important;
}

[data-theme='dark'] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%) !important;
}

/* Forms in dark theme */
[data-theme='dark'] .form-control,
[data-theme='dark'] .form-select {
    background-color: #111827 !important;
    border-color: #374151 !important;
    color: #e5e7eb !important;
}

[data-theme='dark'] .form-control:focus,
[data-theme='dark'] .form-select:focus {
    background-color: #0b1220 !important;
    border-color: #60a5fa !important;
    color: #fff !important;
    box-shadow: none !important;
}

[data-theme='dark'] .form-label {
    color: #e5e7eb !important;
}

[data-theme='dark'] .form-check-input {
    background-color: #111827 !important;
    border-color: #374151 !important;
}

[data-theme='dark'] .form-check-input:checked {
    background-color: #3b82f6 !important;
    border-color: #3b82f6 !important;
}

[data-theme='dark'] .form-check-label {
    color: #e5e7eb !important;
}

[data-theme='dark'] .input-group-text {
    background-color: #111827 !important;
    border-color: #374151 !important;
    color: #e5e7eb !important;
}

[data-theme='dark'] .text-muted {
    color: #94a3b8 !important;
}

/* Light theme modal styles */
[data-theme='light'] .modal-content {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #e5e7eb !important;
}

[data-theme='light'] .modal-header {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border-bottom: 1px solid #e5e7eb !important;
}

[data-theme='light'] .modal-body {
    background-color: #ffffff !important;
    color: #1f2937 !important;
}

[data-theme='light'] .modal-footer {
    background-color: #ffffff !important;
    color: #1f2937 !important;
    border-top: 1px solid #e5e7eb !important;
}
</style>

<script>
// Universal modal theme inheritance script
document.addEventListener('DOMContentLoaded', function() {
    // Function to apply theme to a modal
    function applyThemeToModal(modal) {
        if (!modal) return;
        
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        modal.setAttribute('data-theme', currentTheme);
        
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.setAttribute('data-theme', currentTheme);
        }
    }
    
    // Apply theme to all existing modals
    document.querySelectorAll('.modal').forEach(applyThemeToModal);
    
    // Set up listeners for modal show events
    document.querySelectorAll('.modal').forEach(function(modal) {
        modal.addEventListener('show.bs.modal', function() {
            applyThemeToModal(modal);
        });
    });
    
    // Listen for theme changes and update all modals
    document.addEventListener('themeChange', function(e) {
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.setAttribute('data-theme', e.detail.theme);
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.setAttribute('data-theme', e.detail.theme);
            }
        });
    });
});
</script>
@extends('layouts.referee-dashboard')

@section('title', 'My Account')
@section('page_title', 'My Account')

@section('content')
<style>
    .step-box {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 30px;
        color: white;
        max-width: 800px;
        margin: 50px auto;
    }

    .step-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 20px;
    }

    #frameOverlay {
        position: absolute;
        top: 20%;
        left: 20%;
        width: 60%;
        height: 60%;
        border: 3px dashed #00aeef;
        box-sizing: border-box;
        z-index: 10;
        pointer-events: none;
    }

    .step-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .step-nav .step {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-bottom: 2px solid #fff;
        color: #aaa;
    }

    .step-nav .active {
        color: #00AEEF;
        font-weight: bold;
        border-color: #00AEEF;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #aaa;
    }

    .btn-primary {
        background: #00AEEF;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #0088cc;
    }

    .profile-picture-container {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }

    .profile-picture {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #00AEEF;
    }

    .camera-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #00AEEF;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hidden {
        display: none;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: block;
    }

    .form-row {
        display: flex;
        gap: 20px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .required {
        color: #ff6b6b;
    }

    .error-message {
        color: #ff6b6b;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .success-message {
        color: #51cf66;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    /* Camera Modal Styles */
    .camera-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .camera-modal-content {
        background: #2a2d38;
        border-radius: 15px;
        padding: 20px;
        max-width: 500px;
        width: 90%;
        color: white;
    }

    .camera-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .camera-header h3 {
        margin: 0;
        color: white;
    }

    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }

    .camera-body {
        text-align: center;
        margin-bottom: 20px;
    }

    .camera-footer {
        display: flex;
        gap: 10px;
        justify-content: center;
    }

    .btn-secondary {
        background: #6c757d;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .validation-error {
        border-color: #ff6b6b !important;
        box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2) !important;
    }

    .field-error {
        color: #ff6b6b;
        font-size: 0.8rem;
        margin-top: 5px;
        display: block;
    }
</style>

<div class="step-box">
    <div class="step-title">My Account</div>
    
    @if(session('success'))
        <div class="alert alert-success" style="background: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c2c7;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c2c7;">
            <i class="fas fa-exclamation-circle"></i> Please correct the following errors:
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="step-nav">
        <div class="step active" data-step="1">Personal Info</div>
        <div class="step" data-step="2">Referee Details</div>
        <div class="step" data-step="3">Profile Picture</div>
    </div>

    <form id="accountForm" method="POST" action="{{ route('my-account-save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="userType" value="referee">
        
        <!-- Step 1: Personal Information -->
        <div class="step-content active" id="step1">
            <h3>Personal Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ $user->first_name ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ $user->last_name ?? '' }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="{{ $user->email }}" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="{{ $referee->phone ?? $user->phone ?? '' }}">
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country">
                    <option value="">Select Country</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" {{ ($referee->country ?? $user->country ?? '') == $code ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Step 2: Referee Details -->
        <div class="step-content" id="step2">
            <h3>Referee Information</h3>
            
            <div class="form-group">
                <label for="certification_level">Certification Level</label>
                <select id="certification_level" name="certification_level">
                    <option value="">Select Level</option>
                    <option value="1" {{ ($referee->certification_level ?? '') == '1' ? 'selected' : '' }}>Level 1</option>
                    <option value="2" {{ ($referee->certification_level ?? '') == '2' ? 'selected' : '' }}>Level 2</option>
                    <option value="3" {{ ($referee->certification_level ?? '') == '3' ? 'selected' : '' }}>Level 3</option>
                    <option value="4" {{ ($referee->certification_level ?? '') == '4' ? 'selected' : '' }}>Level 4</option>
                    <option value="5" {{ ($referee->certification_level ?? '') == '5' ? 'selected' : '' }}>Level 5</option>
                </select>
            </div>

            <div class="form-group">
                <label for="experience_years">Years of Experience</label>
                <input type="number" id="experience_years" name="experience_years" value="{{ $referee->experience_years ?? '' }}" min="0" max="50">
            </div>

            <div class="form-group">
                <label for="specialties">Specialties</label>
                <textarea id="specialties" name="specialties" rows="3" placeholder="List your referee specialties...">{{ $referee->specialties ?? '' }}</textarea>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself...">{{ $referee->bio ?? '' }}</textarea>
            </div>
        </div>

        <!-- Step 3: Profile Picture -->
        <div class="step-content" id="step3">
            <h3>Profile Picture</h3>
            
            <div class="profile-picture-container">
                <img id="profilePreview" src="{{ $referee && $referee->profile_picture ? asset($referee->profile_picture) : asset('assets/club-dashboard-main/assets/user.png') }}" 
                     alt="Profile Picture" class="profile-picture">
                <button type="button" class="camera-icon" onclick="openCamera()">
                    <i class="fas fa-camera"></i>
                </button>
                <input type="file" id="profilePicture" name="profile_picture" accept="image/*" style="display: none;" onchange="handleFileSelect(event)">
                <input type="hidden" id="capturedImage" name="captured_image" value="{{ $referee && $referee->profile_picture ? '' : '' }}">
            </div>
            
            <div class="form-group">
                <label>Upload Options</label>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" class="btn-primary" onclick="document.getElementById('profilePicture').click()">
                        <i class="fas fa-upload"></i> Choose File
                    </button>
                    <button type="button" class="btn-primary" onclick="openCamera()">
                        <i class="fas fa-camera"></i> Take Photo
                    </button>
                </div>
            </div>
            
            <!-- Camera Modal -->
            <div id="cameraModal" class="camera-modal" style="display: none;">
                <div class="camera-modal-content">
                    <div class="camera-header">
                        <h3>Take Profile Picture</h3>
                        <button type="button" onclick="closeCamera()" class="close-btn">&times;</button>
                    </div>
                    <div class="camera-body">
                        <video id="video" autoplay style="width: 100%; max-width: 400px; height: auto;"></video>
                        <canvas id="canvas" style="display: none;"></canvas>
                        <div id="frameOverlay"></div>
                    </div>
                    <div class="camera-footer">
                        <button type="button" onclick="capturePhoto()" class="btn-primary">
                            <i class="fas fa-camera"></i> Capture
                        </button>
                        <button type="button" onclick="closeCamera()" class="btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <button type="button" id="prevBtn" class="btn-primary" style="display: none;">Previous</button>
            <button type="button" id="nextBtn" class="btn-primary">Next</button>
            <button type="submit" id="submitBtn" class="btn-primary" style="display: none;">Save Changes</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;
    let stream = null;
    
    const steps = document.querySelectorAll('.step');
    const stepContents = document.querySelectorAll('.step-content');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('accountForm');
    
    // Form validation rules
    const validationRules = {
        first_name: { required: true, minLength: 2, message: 'First name must be at least 2 characters' },
        last_name: { required: true, minLength: 2, message: 'Last name must be at least 2 characters' },
        email: { required: true, pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, message: 'Please enter a valid email address' },
        phone: { pattern: /^[\+]?[1-9][\d]{0,15}$/, message: 'Please enter a valid phone number' },
        certification_level: { required: false },
        experience_years: { min: 0, max: 50, message: 'Experience years must be between 0 and 50' }
    };
    
    function validateField(fieldName, value) {
        const rules = validationRules[fieldName];
        if (!rules) return true;
        
        if (rules.required && (!value || value.trim() === '')) {
            return { valid: false, message: `${fieldName.replace('_', ' ')} is required` };
        }
        
        if (value && rules.minLength && value.length < rules.minLength) {
            return { valid: false, message: rules.message };
        }
        
        if (value && rules.pattern && !rules.pattern.test(value)) {
            return { valid: false, message: rules.message };
        }
        
        if (value && rules.min !== undefined && parseInt(value) < rules.min) {
            return { valid: false, message: rules.message };
        }
        
        if (value && rules.max !== undefined && parseInt(value) > rules.max) {
            return { valid: false, message: rules.message };
        }
        
        return { valid: true };
    }
    
    function showFieldError(fieldName, message) {
        const field = document.getElementById(fieldName);
        const existingError = field.parentNode.querySelector('.field-error');
        
        if (existingError) {
            existingError.remove();
        }
        
        field.classList.add('validation-error');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }
    
    function clearFieldError(fieldName) {
        const field = document.getElementById(fieldName);
        const existingError = field.parentNode.querySelector('.field-error');
        
        if (existingError) {
            existingError.remove();
        }
        
        field.classList.remove('validation-error');
    }
    
    function validateStep(step) {
        let isValid = true;
        const stepElement = document.getElementById(`step${step}`);
        const fields = stepElement.querySelectorAll('input, select, textarea');
        
        fields.forEach(field => {
            const fieldName = field.name || field.id;
            const value = field.value;
            
            if (fieldName && validationRules[fieldName]) {
                const validation = validateField(fieldName, value);
                
                if (!validation.valid) {
                    showFieldError(fieldName, validation.message);
                    isValid = false;
                } else {
                    clearFieldError(fieldName);
                }
            }
        });
        
        return isValid;
    }
    
    function showStep(step) {
        // Hide all step contents
        stepContents.forEach(content => content.classList.remove('active'));
        
        // Show current step content
        document.getElementById(`step${step}`).classList.add('active');
        
        // Update step navigation
        steps.forEach((stepEl, index) => {
            stepEl.classList.remove('active');
            if (index + 1 === step) {
                stepEl.classList.add('active');
            }
        });
        
        // Update buttons
        prevBtn.style.display = step > 1 ? 'inline-block' : 'none';
        nextBtn.style.display = step < totalSteps ? 'inline-block' : 'none';
        submitBtn.style.display = step === totalSteps ? 'inline-block' : 'none';
    }
    
    nextBtn.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('blur', function() {
            const fieldName = this.name || this.id;
            const value = this.value;
            
            if (fieldName && validationRules[fieldName]) {
                const validation = validateField(fieldName, value);
                
                if (!validation.valid) {
                    showFieldError(fieldName, validation.message);
                } else {
                    clearFieldError(fieldName);
                }
            }
        });
    });
    
    // Camera functionality
    window.openCamera = function() {
        const modal = document.getElementById('cameraModal');
        modal.style.display = 'flex';
        
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function(mediaStream) {
                stream = mediaStream;
                const video = document.getElementById('video');
                video.srcObject = stream;
            })
            .catch(function(err) {
                alert('Error accessing camera: ' + err.message);
                closeCamera();
            });
    };
    
    window.closeCamera = function() {
        const modal = document.getElementById('cameraModal');
        modal.style.display = 'none';
        
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    };
    
    window.capturePhoto = function() {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        const imageData = canvas.toDataURL('image/jpeg', 0.8);
        document.getElementById('capturedImage').value = imageData;
        document.getElementById('profilePreview').src = imageData;
        
        closeCamera();
    };
    
    // File upload handling
    window.handleFileSelect = function(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }
            
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    };
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all steps
        let allValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                allValid = false;
            }
        }
        
        if (allValid) {
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;
            
            // Create FormData for file uploads
            const formData = new FormData(form);
            
            // Submit form via fetch for better error handling
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showMessage('success', data.message || 'Profile updated successfully!');
                    // Redirect after a short delay
                    setTimeout(() => {
                        window.location.href = data.redirect || window.location.href;
                    }, 1500);
                } else {
                    // Show error message
                    showMessage('error', data.message || 'Failed to save profile');
                    submitBtn.innerHTML = 'Save Changes';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('error', 'An error occurred while saving your profile');
                submitBtn.innerHTML = 'Save Changes';
                submitBtn.disabled = false;
            });
        } else {
            // Go to first step with errors
            for (let i = 1; i <= totalSteps; i++) {
                if (!validateStep(i)) {
                    currentStep = i;
                    showStep(currentStep);
                    break;
                }
            }
        }
    });
    
    // Function to show messages
    function showMessage(type, message) {
        // Remove existing messages
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new message
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.style.cssText = type === 'success' 
            ? 'background: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #badbcc;'
            : 'background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c2c7;';
        
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        alertDiv.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
        
        // Insert at the top of the form
        const stepBox = document.querySelector('.step-box');
        stepBox.insertBefore(alertDiv, stepBox.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Initialize
    showStep(currentStep);
});
</script>
@endsection

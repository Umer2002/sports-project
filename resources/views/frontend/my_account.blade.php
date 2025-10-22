@extends('layouts.default')
<!-- I AM IN frontend/my_account.blade.php -->

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

        .player-only,
        .club-only,
        .referee-only,
        .college-only,
        .not-referee {
            display: none;
        }

        .camera-frame {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }

        #cameraPreview {
            position: relative;
            width: 100%;
            height: 300px;
            object-fit: cover;
        }



        #frameOverlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('storage/head-shoulder-frame.png') }}') center center no-repeat;
            background-size: contain;
            z-index: 10;
            pointer-events: none;
        }


        .alert-align {
            color: red;
            font-size: 0.95rem;
            margin-top: 10px;
            display: none;
        }
    </style>

@php
    $user = Auth::user();
    $userType = $user && $user->roles->isNotEmpty() ? $user->roles->first()->name : null;
    $name = '';
    if ($userType === 'club' && isset($club) && $club) {
        $name = $club->name;
    } elseif ($userType === 'player' && isset($player) && $player) {
        $name = $player->name;
    }

@endphp

    <!-- Assuming this file is user_account.blade.php -->
    <div class="container-fluid">
        <div class="row">
        <div class="col-lg-2 mb-4">
            {{-- @include('players.partials.sidebar') --}}
        </div>
        <div class="col-lg-10">
            <div class="step-box">
                <div class="step-nav">
                    <div class="step active" id="step1-tab">Step 1</div>
                    <div class="step" id="step2-tab">Step 2</div>
                    @if ($userType === 'player')
                        <div class="step" id="step3-tab">Step 3</div>
                        <div class="step" id="step4-tab">Step 4</div>
                        <div class="step" id="step5-tab">Step 5</div>
                    @else
                        <div class="step" id="step4-tab">Step 3</div>
                        <div class="step" id="step5-tab">Step 5</div>
                    @endif
                    {{-- <div class="step" id="step3-tab ">Step 3</div> --}}
                    <div class="step" id="step6-tab">Finish</div>
                </div>

                <form id="wizardForm" method="POST" action="{{ route('my-account-save') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_type" id="user_type"
                        value="{{ $userType }}">

                    <!-- STEP 1 -->
                    <div id="step1" class="step-content">
                        <div class="step-title">Step 1: Basic Info</div>
                        @php
                            $name = '';
                            if ($userType === 'club' && isset($club) && $club) {
                                $name = $club->name;
                            } elseif ($userType === 'player' && isset($player) && $player) {
                                $name = $player->name;
                            }
                        @endphp
                        <div class="mb-3 referee-only">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" value="{{ old('full_name', isset($referee) ? $referee->full_name : '') }}" placeholder="Enter your full name">
                        </div>

                        <div class="mb-3 not-referee">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $name) }}" required placeholder="Enter your full name">
                        </div>

                        <div class="mb-3 club-only college-only">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" placeholder="Upload logo">
                        </div>

                        <div class="mb-3 college-only">
                            <label class="form-label">College Name</label>
                            <input type="text" class="form-control" name="college_name" value="{{ old('college_name', isset($college) ? $college->college_name : '') }}" placeholder="Enter college name">
                        </div>

                        <div class="mb-3 d-none player-only ">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="dob" value="{{ old('dob', isset($player) ? $player->birthday : '') }}" placeholder="Select date of birth">
                        </div>

                        <div class="mb-3 player-only">
                            <label class="form-label">Nationality</label>
                            <select name="nationality" class="form-control" placeholder="Select nationality">
                                <option value="">Select Nationality</option>
                                @foreach ($countries as $code => $name)
                                    <option value="{{ $code }}"
                                        {{ old('nationality', isset($player) ? $player->nationality : '') == $code ? 'selected' : '' }}>
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 player-only">
                            <label class="form-label">Position</label>
                            <select name="position" class="form-control" placeholder="Select position">
                                <option value="">Select Position</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position['id'] }}"
                                        {{ old('position', isset($player) ? $player->position : '') == $position['position_name'] ? 'selected' : '' }}>
                                        {{ $position['position_name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-primary" onclick="validateStepAndGo(1, 2)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
                    <div id="step2" class="step-content d-none">
                        <div class="step-title">Step 2: Contact Info</div>
                        @php
                            if ($userType === 'club') {
                                $user = $club;
                            } elseif ($userType === 'player') {
                                $user = $player;
                            } elseif ($userType === 'college') {
                                $user = $college;
                            } elseif ($userType === 'referee') {
                                $user = $referee;
                            }
                        @endphp
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', isset($user) && $user ? $user->phone : '') }}" placeholder="Enter your phone number">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" placeholder="Enter your address">{{ old('address', isset($user) && $user ? $user->address : '') }}</textarea>
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Preferred Contact Method</label>
                            <input type="text" class="form-control" name="preferred_contact_method" value="{{ old('preferred_contact_method', isset($referee) ? $referee->preferred_contact_method : '') }}" placeholder="Preferred contact method">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Government ID</label>
                            <input type="text" class="form-control" name="government_id" value="{{ old('government_id', isset($referee) ? $referee->government_id : '') }}" placeholder="Government ID">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Languages Spoken</label>
                            <input type="text" class="form-control" name="languages_spoken" value="{{ old('languages_spoken', isset($referee) ? $referee->languages_spoken : '') }}" placeholder="Languages spoken">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="{{ old('city', isset($referee) ? $referee->city : '') }}" placeholder="City">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Region</label>
                            <input type="text" class="form-control" name="region" value="{{ old('region', isset($referee) ? $referee->region : '') }}" placeholder="Region">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" name="country" value="{{ old('country', isset($referee) ? $referee->country : '') }}" placeholder="Country">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">License Type</label>
                            <input type="text" class="form-control" name="license_type" value="{{ old('license_type', isset($referee) ? $referee->license_type : '') }}" placeholder="License type">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Certifying Body</label>
                            <input type="text" class="form-control" name="certifying_body" value="{{ old('certifying_body', isset($referee) ? $referee->certifying_body : '') }}" placeholder="Certifying body">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">License Expiry Date</label>
                            <input type="date" class="form-control" name="license_expiry_date" value="{{ old('license_expiry_date', isset($referee->license_expiry_date) ? $referee->license_expiry_date->format('Y-m-d') : '') }}" placeholder="License expiry date">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Certification Level</label>
                            <input type="number" class="form-control" name="certification_level" value="{{ old('certification_level', isset($referee) ? $referee->certification_level : '') }}" placeholder="Certification level">
                        </div>

                        <div class="mb-3 player-only">
                            <label class="form-label">Jersey Number</label>
                            <input type="number" class="form-control" name="jersey_no" value="{{ old('jersey_no', isset($player) ? $player->jersey_no : '') }}" placeholder="Jersey number">
                        </div>

                        <div class="mb-3 player-only">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" class="form-control" name="height" value="{{ old('height', isset($player) ? $player->height : '') }}" placeholder="Height in cm">
                        </div>
                        <div class="mb-3 player-only">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" class="form-control" name="weight" value="{{ old('weight', isset($player) ? $player->weight : '') }}" placeholder="Weight in kg">
                        </div>
                        <div class="mb-3 player-only">
                            <label class="form-label">Debut Date</label>
                            <input type="date" class="form-control" name="debut_date" value="{{ old('debut_date', isset($player) ? $player->debut : '') }}" placeholder="Debut date">
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="goBack(1)">Back</button>
                            <button type="button" class="btn btn-primary"
                                onclick="validateStepAndGo(2, 3)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 3 -->
                    <div id="step3" class="step-content player-only">
                        <div class="step-title">Step 3: Capture Your Image</div>
                        <p>Please align your face and shoulders in the guide frame below and capture your photo.</p>

                        <div class="mb-3 text-center camera-frame">
                            <div style="position: relative; width: 100%; height: 300px;">
                                <video id="cameraPreview" autoplay muted playsinline></video>
                                <div id="frameOverlay"></div>
                            </div>

                            @if (isset($player) && !empty($player->photo))
                                <div class="mt-3">
                                    <label class="form-label">Previously Captured Image:</label><br>
                                    <img src="{{ asset($player->photo) }}" alt="Previous Photo" class="img-thumbnail"
                                        style="max-width: 200px;">
                                </div>
                            @endif

                            <canvas id="capturedCanvas" class="d-none"></canvas>
                            <input type="hidden" name="captured_image" id="captured_image">
                            <div class="alert-align" id="alignWarning">⚠️ Please align your face within the frame to
                                continue.
                            </div>
                            <button type="button" class="btn btn-warning mt-2"
                                onclick="validateAndCapture()">Capture</button>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(2)">Back</button>
                            <button type="button" class="btn btn-primary"
                                onclick="validateStepAndGo(3, 4)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 4 -->
                    <div id="step4" class="step-content d-none">
                        <div class="step-title">Step 4: Social Media Links</div>

                        @php
                            $social = [];

                            if ($userType === 'club' && isset($club)) {
                                $social = is_string($club->social_links)
                                    ? json_decode($club->social_links, true)
                                    : $club->social_links;
                            }

                            if ($userType === 'player' && isset($player)) {
                                $social = is_string($player->social_links)
                                    ? json_decode($player->social_links, true)
                                    : $player->social_links;
                            }
                        @endphp


                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="url" class="form-control" name="social_links[facebook]" value="{{ old('social_links.facebook', $social['facebook'] ?? '') }}" placeholder="Facebook profile URL">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">X (Twitter)</label>
                            <input type="url" class="form-control" name="social_links[twitter]" value="{{ old('social_links.twitter', $social['twitter'] ?? '') }}" placeholder="X (Twitter) profile URL">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dribbble</label>
                            <input type="url" class="form-control" name="social_links[dribbble]" value="{{ old('social_links.dribbble', $social['dribbble'] ?? '') }}" placeholder="Dribbble profile URL">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" class="form-control" name="social_links[linkedin]" value="{{ old('social_links.linkedin', $social['linkedin'] ?? '') }}" placeholder="LinkedIn profile URL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">youtube</label>
                            <input type="url" class="form-control" name="social_links[youtube]" value="{{ old('social_links.youtube', $social['youtube'] ?? '') }}" placeholder="youtube profile URL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pinterest</label>
                            <input type="url" class="form-control" name="social_links[pinterest]" value="{{ old('social_links.pinterest', $social['pinterest'] ?? '') }}" placeholder="pinterest profile URL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Snapchat</label>
                            <input type="url" class="form-control" name="social_links[snapchat]" value="{{ old('social_links.snapchat', $social['snapchat'] ?? '') }}" placeholder="Snapchat profile URL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiktok</label>
                            <input type="url" class="form-control" name="social_links[tiktok]" value="{{ old('social_links.tiktok', $social['tiktok'] ?? '') }}" placeholder="Tiktok profile URL">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" class="form-control" name="social_links[instagram]" value="{{ old('social_links.instagram', $social['instagram'] ?? '') }}" placeholder="Instagram profile URL">
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="goBack(3)">Back</button>
                            <button type="button" class="btn btn-primary"
                                onclick="validateStepAndGo(4, 5)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 5 -->
                    <div id="step5" class="step-content d-none">
                        <div class="step-title">Step 5: Additional Info</div>

                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" name="bio" placeholder="Tell us about yourself">{{ old('bio', isset($user) && $user ? $user->bio : '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PayPal Link</label>
                            <input type="url" class="form-control" name="paypal_link" value="{{ old('paypal_link', optional($player)->paypal_link ?? optional($club)->paypal_link) }}" placeholder="PayPal.me link">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Background Check Passed</label>
                            <select name="background_check_passed" class="form-control">
                                <option value="1" {{ old('background_check_passed', isset($referee) ? $referee->background_check_passed : false) ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('background_check_passed', isset($referee) ? $referee->background_check_passed : false) ? '' : 'selected' }}>No</option>
                            </select>
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Liability Insurance</label>
                            <select name="liability_insurance" class="form-control">
                                <option value="1" {{ old('liability_insurance', isset($referee) ? $referee->liability_insurance : false) ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('liability_insurance', isset($referee) ? $referee->liability_insurance : false) ? '' : 'selected' }}>No</option>
                            </select>
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Liability Document</label>
                            <input type="file" class="form-control" name="liability_document" placeholder="Upload liability document">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Sports Officiated</label>
                            <input type="text" class="form-control" name="sports_officiated[]" value="{{ old('sports_officiated', isset($referee->sports_officiated) ? implode(',', $referee->sports_officiated) : '') }}" placeholder="Comma separated sports">
                        </div>

                        <div class="mb-3 referee-only">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-control" name="internal_notes" placeholder="Internal notes">{{ old('internal_notes', isset($referee) ? $referee->internal_notes : '') }}</textarea>
                        </div>



                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" onclick="goBack(4)">Back</button>
                            <button type="button" class="btn btn-primary"
                                onclick="validateStepAndGo(5, 6)">Next</button>
                        </div>
                    </div>

                    <!-- STEP 6 -->
                    <div id="step6" class="step-content d-none">
                        <div class="step-title">Final Step</div>
                        <p>Click finish to save and complete your profile.</p>

                        <div class="text-end">
                            <input type="hidden" name="userType" value="{{$userType}}">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(5)">Back</button>
                          @if($userType == 'player')
                            @if(!$requiresPlayerPayment)
                                <button type="submit" class="btn btn-success">Finish</button>
                            @else
                                <button type="button" class="btn btn-success" onclick="handleStripePayment()">Finish</button>
                            @endif
                          @else
                            <button type="submit" class="btn btn-success">Update Now</button>
                          @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const userType = "{{ strtolower($userType) }}"; // ✅ keep this only
        const playerNeedsPayment = @json($requiresPlayerPayment);

        // check if step is 1 than hide step 3
        if (userType === 'player') {
            document.getElementById('step3').classList.add('d-none');
        }

        function validateStepAndGo(currentStep, nextStep) {
            const step = document.getElementById('step' + currentStep);
            const inputs = step.querySelectorAll('input, select, textarea');

            for (const input of inputs) {
                if (!input.checkValidity()) {
                    input.reportValidity();
                    input.focus();
                    return false;
                }
            }
            document.addEventListener('DOMContentLoaded', () => {
                toggleFieldsByUserType();
                initCameraIfPresent();
                goToStep(1); // ✅ ensure only step 1 is shown on load
            });
            // Skip Step 3 for clubs
            if (userType === 'club' && nextStep === 3) {
                nextStep = 4;
            }
            if (nextStep === 3) {
                const step3 = document.getElementById('step3');
                step3.classList.remove('d-none');
                step3.classList.remove('player-only');

                // Optional: update nav tab
                document.getElementById('step3-tab')?.classList.add('active');
                step3.style.display = 'block';
            }

            goToStep(nextStep);
        }

        function goBack(currentStep) {
            let prevStep = currentStep - 1;

            // Skip Step 3 if going back from Step 4 to 3 for clubs
            if (userType === 'club' && currentStep === 4) {
                prevStep = 2;
            }

            goToStep(prevStep);
        }

        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
            document.getElementById('step' + step)?.classList.remove('d-none');

            document.querySelectorAll('.step-nav .step').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + step + '-tab')?.classList.add('active');
        }

        function toggleFieldsByUserType() {
            const types = ['player', 'club', 'referee', 'college'];
            types.forEach(t => {
                document.querySelectorAll('.' + t + '-only').forEach(el => {
                    el.style.display = userType === t ? 'block' : 'none';
                });
            });
            document.querySelectorAll('.not-referee').forEach(el => {
                el.style.display = userType === 'referee' ? 'none' : 'block';
            });
        }

        function initCameraIfPresent() {
            const video = document.getElementById('cameraPreview');
            if (video) {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(stream => {
                        video.srcObject = stream;
                    })
                    .catch(error => {
                        console.error('Camera access denied:', error);
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleFieldsByUserType();
            initCameraIfPresent();
        });

        function validateAndCapture() {
            const frame = document.getElementById('frameOverlay').getBoundingClientRect();
            const video = document.getElementById('cameraPreview');
            const warning = document.getElementById('alignWarning');
            const canvas = document.getElementById('capturedCanvas');

            const faceCentered = frame.top > 0 && frame.bottom < window.innerHeight;

            if (!faceCentered) {
                warning.style.display = 'block';
                return;
            }

            warning.style.display = 'none';
            captureImage();
        }

        function captureImage() {
            const video = document.getElementById('cameraPreview');
            const canvas = document.getElementById('capturedCanvas');
            const ctx = canvas.getContext('2d');

            const videoWidth = video.videoWidth;
            const videoHeight = video.videoHeight;

            // Adjust these values to match your overlay frame's transparent area
            const cropX = videoWidth * 0.25; // 25% from left
            const cropY = videoHeight * 0.15; // 15% from top
            const cropWidth = videoWidth * 0.5; // 50% of width
            const cropHeight = videoHeight * 0.7; // head + shoulder portrait

            canvas.width = cropWidth;
            canvas.height = cropHeight;

            ctx.drawImage(
                video,
                cropX, cropY, cropWidth, cropHeight,
                0, 0, cropWidth, cropHeight
            );

            const imageData = canvas.toDataURL('image/png');
            document.getElementById('captured_image').value = imageData;
            canvas.classList.remove('d-none');
        }

        document.getElementById('wizardForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json' // 👈 Tell Laravel it's AJAX!
                    },
                })
                .then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                })
                .then(data => {
                    alert('Profile saved successfully!');
                    window.location.href = data.redirect || "{{ route('my-account') }}";
                })
                .catch(async err => {
                    let msg = "An error occurred while saving your profile.";
                    try {
                        const json = await err.json();
                        msg = json.message || msg;
                    } catch (e) {}
                    alert(msg);
                });
        });
        async function handleStripePayment() {
            if (!playerNeedsPayment) {
                document.getElementById('wizardForm').dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                return;
            }

            const form = document.getElementById('wizardForm');
            const formData = new FormData(form);

            try {
                const saveResponse = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json', // 👈 Tell Laravel it's AJAX!
                    },
                });

                if (!saveResponse.ok) {
                    throw saveResponse;
                }

                const checkoutResponse = await fetch("{{ route('player.checkout') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({})
                });

                const checkoutData = await checkoutResponse.json();

                if (checkoutResponse.ok && checkoutData.url) {
                    window.location.href = checkoutData.url; // Redirect to Stripe Checkout
                    return;
                }

                throw new Error(checkoutData.error || 'Could not start payment. Please try again.');
            } catch (err) {
                let msg = "An error occurred while saving your profile.";

                if (err instanceof Response) {
                    try {
                        const json = await err.json();
                        msg = json.message || msg;
                    } catch (_) {}
                } else if (err?.message) {
                    msg = err.message;
                }

                alert(msg);
            }
        }
    </script>

@endsection

@endsection

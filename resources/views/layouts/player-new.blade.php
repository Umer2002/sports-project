<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>@yield('title', 'Player Dashboard')</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <script>
        (function() {
            const storageKey = 'p2e-player-theme';
            try {
                const storedTheme = localStorage.getItem(storageKey);
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', theme);
            } catch (error) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        })();
    </script>

    <!-- New Dashboard Styles -->
    <link rel='stylesheet' type='text/css' media='screen' href='{{ asset('assets/player-dashboard/css/main.css') }}'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Additional styles for functionality preservation -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">

    @if (app()->environment('local') || file_exists(public_path('build/manifest.json')))
        @viteReactRefresh
        @vite(['resources/js/app.js'])
    @endif

    @php
        // Determine sport-specific theme
        $sportName = isset($player) && $player->sport ? strtolower($player->sport->name) : 'soccer';
        $sportMap = [
            'football' => 'american-football',
            'american football' => 'american-football',
            'track and field' => 'track-field',
            'field hockey' => 'field-hockey',
            'mixed martial arts' => 'mma',
            'golf' => 'glof',
        ];
        $dashboardSport = $sportMap[$sportName] ?? $sportName;
    @endphp

    <!-- Sport-specific customizations can be added here -->
    <style>
        :root {
            @if ($dashboardSport === 'american-football')
                --primary-sport-color: #013369;
                --secondary-sport-color: #D50A0A;
            @elseif($dashboardSport === 'soccer')
                --primary-sport-color: #00A86B;
                --secondary-sport-color: #FFFFFF;
            @elseif($dashboardSport === 'basketball')
                --primary-sport-color: #FF8C00;
                --secondary-sport-color: #000000;
            @else
                --primary-sport-color: #39a2ff;
                --secondary-sport-color: #f9a825;
            @endif
        }
    </style>

    @yield('header_styles')
    @stack('header_styles')
</head>

<body class="player-dashboard">
    <div class="main-dashboard">
        <i class="fa fa-bars hamburger" id="hamburger"></i>
        <div class="overlay" id="overlay"></div>

        <!-- Left Sidebar -->
        <div class="left-bar" id="sidebar">
            <div class="left-main">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="logo">
                            <a href="{{ route('player.dashboard') }}">
                                <img src="{{ asset('assets/player-dashboard/images/logo.png') }}" alt="Logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="Left-profile">
                            @if (!empty($player->photo))
                                <img src="{{ asset('storage/players/' . $player->photo) }}" alt="Profile"
                                    class="w-50">
                            @endif
                            <h2>{{ ucfirst(auth()->user()->name) }}</h2>
                            <p>{{ $player->position->position_name ?? 'Player' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="sidebar">
                            <h4>main</h4>
                            <ul>
                                @include('players.partials.sidebar-menu-items', [
                                    'player' => $player ?? null,
                                ])
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="logout-account">
                            <form action="http://localhost:8000/logout" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">
                                <button type="submit" class="logout-button"
                                    style="background-color: #F6A437; width: 80%; padding: 10px; border-radius: 25px;">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="right-bar">
            <div class="top-bar">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 d-flex align-items-center">
                        <div class="topbar-text">
                            <h2>@yield('page-title', 'Dashboard')</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 mobile-screen-topbar">
                        <div class="search-box">
                            <img src="{{ asset('assets/player-dashboard/images/search.svg') }}" alt="Icon">
                            <input type="text" placeholder="Search">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 mobile-screen-topbar">
                        <div class="topbar">
                            <button class="icon-btn" title="Fullscreen">
                                <img src="{{ asset('assets/player-dashboard/images/la_expand.svg') }}" alt="Icon">
                            </button>
                            <button class="icon-btn" title="Notifications">
                                <img src="{{ asset('assets/player-dashboard/images/Bell.svg') }}" alt="Icon">
                            </button>
                            <button class="icon-btn" title="Settings">
                                <img src="{{ asset('assets/player-dashboard/images/uil_setting.svg') }}"
                                    alt="Icon">
                            </button>
                            <button class="icon-btn" type="button" id="themeToggle" aria-label="Toggle theme"
                                title="Toggle theme">
                                <i class="fas fa-sun"></i>
                            </button>
                            <button class="icon-btn ab" type="button" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop-two">
                                @if (!empty($player->photo))
                                    <img src="{{ asset('storage/players/' . $player->photo) }}" alt="Profile"
                                        style="width: 25px">
                                @else
                                    <img src="{{ asset('assets/player-dashboard/images/profile1.svg') }}"
                                        alt="Icon">
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="middle">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Profile Edit Modal (from original design) -->
    <div class="modal modal-top-modal fade" id="staticBackdrop-two" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content modal-content-one">
                <div class="modal-header modal-header-one">
                    <div class="modal-headding">
                        <h2 class="modal-title edit-profile-text fs-5" id="staticBackdropLabel">
                            Edit Profile <label>Update details or set privacy. Preview updates live.</label>
                        </h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-content">
                        <div class="row g-3">
                            <div class="col-md-4 col-md-12">
                                <div class="profile-left">
                                    <div class="profile-image">
                                        @if (!empty($player->photo))
                                            <img src="{{ asset('storage/players/' . $player->photo) }}"
                                                alt="Profile"
                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div
                                                style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666; font-size: 14px;">
                                                No Image</div>
                                        @endif
                                    </div>
                                    <h1 class="profile-title">Profile preview</h1>
                                    <div class="preview-box">
                                        <strong>{{ auth()->user()->name }}</strong>
                                        <div>{{ $player->position->position_name ?? '' }}</div>
                                        <small style="display: flex; flex-wrap: wrap; gap: 8px;">
                                            <span><strong>Nationality:</strong>
                                                {{ $player->nationality ?? 'N/A' }}</span>
                                            <span><strong>Height:</strong> {{ $player->height ?? 'N/A' }}</span>
                                            <span><strong>Weight:</strong> {{ $player->weight ?? 'N/A' }}</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-md-12">
                                <div class="profile-right">
                                    <form id="profileEditForm" method="POST"
                                        action="{{ route('player.profile.update') }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First name</label>
                                                <input type="text" class="form-control input-btn"
                                                    name="first_name"
                                                    value="{{ auth()->user()->first_name ?? (explode(' ', auth()->user()->name)[0] ?? '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last name</label>
                                                <input type="text" class="form-control input-btn" name="last_name"
                                                    value="{{ auth()->user()->last_name ?? (count(explode(' ', auth()->user()->name)) > 1 ? explode(' ', auth()->user()->name)[1] : '') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control input-btn" name="email"
                                                    value="{{ auth()->user()->email }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone</label>
                                                <input type="text" class="form-control input-btn" name="phone"
                                                    value="{{ isset($player) ? $player->phone : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Birthday</label>
                                                <input type="date" class="form-control input-btn" name="birthday"
                                                    value="{{ isset($player) ? $player->birthday : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Age (auto)</label>
                                                <input type="text" class="form-control input-btn"
                                                    value="{{ isset($player) ? $player->age : 'Auto calculated' }}"
                                                    disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Height</label>
                                                <input type="text" class="form-control input-btn" name="height"
                                                    value="{{ isset($player) ? $player->height : '' }}"
                                                    placeholder="e.g., 178 cm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Weight</label>
                                                <input type="text" class="form-control input-btn" name="weight"
                                                    value="{{ isset($player) ? $player->weight : '' }}"
                                                    placeholder="e.g., 75 kg">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nationality</label>
                                                <input type="text" class="form-control input-btn"
                                                    name="nationality"
                                                    value="{{ isset($player) ? $player->nationality : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Jersey Number</label>
                                                <input type="number" class="form-control input-btn" name="jersey_no"
                                                    value="{{ isset($player) ? $player->jersey_no : '' }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Bio</label>
                                                <textarea class="form-control input-btn" name="bio" rows="3" placeholder="Tell us about yourself...">{{ isset($player) ? $player->bio : '' }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Profile Picture</label>
                                                <input type="file" class="form-control input-btn"
                                                    name="profile_picture" accept="image/*">
                                                @if (isset($player) && $player->photo)
                                                    <small class="text-muted">Current: {{ $player->photo }}</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label">Name display preference</label>
                                            <div class="d-flex gap-3 mt-1">
                                                <button type="button"
                                                    class="btn btn-outline-secondary show-two-btn flex-fill"
                                                    data-display="full">Show full name</button>
                                                <button type="button"
                                                    class="btn btn-outline-secondary show-two-btn flex-fill"
                                                    data-display="partial">Show first name + last initial</button>
                                            </div>
                                        </div>

                                        <div class="privacy-section mt-4">
                                            <h6>Privacy <small>Choose what's public on your profile.</small></h6>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[profile_private]" id="privateSwitch">
                                                        <label class="form-check-label" for="privateSwitch">Make
                                                            entire profile private</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_email]" id="hideEmail">
                                                        <label class="form-check-label" for="hideEmail">Hide
                                                            email</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_phone]" id="hidePhone">
                                                        <label class="form-check-label" for="hidePhone">Hide
                                                            phone</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_birthday]" id="hideBorn">
                                                        <label class="form-check-label" for="hideBorn">Hide
                                                            birthday</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_age]" id="hideAge">
                                                        <label class="form-check-label" for="hideAge">Hide
                                                            age</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_weight]" id="hideWeight">
                                                        <label class="form-check-label" for="hideWeight">Hide
                                                            weight</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_nationality]" id="hideNationality">
                                                        <label class="form-check-label" for="hideNationality">Hide
                                                            nationality</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="privacy[hide_height]" id="hideHeight">
                                                        <label class="form-check-label" for="hideHeight">Hide
                                                            height</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 d-flex gap-3">
                                            <button type="submit" class="btn-save">Save Changes</button>
                                            <button type="button" class="btn-cancel"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/player-dashboard/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Additional scripts for functionality preservation -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    @stack('scripts')
    @yield('footer_scripts')

    <!-- Profile Edit Modal JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeStorageKey = 'p2e-player-theme';
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const storedTheme = localStorage.getItem(themeStorageKey);
            const htmlElement = document.documentElement;
            const themeToggleButton = document.getElementById('themeToggle');
            const themeIcon = themeToggleButton ? themeToggleButton.querySelector('i') : null;

            function applyTheme(theme) {
                htmlElement.setAttribute('data-bs-theme', theme);
                if (themeIcon) {
                    themeIcon.classList.remove('fa-moon', 'fa-sun');
                    themeIcon.classList.add(theme === 'dark' ? 'fa-sun' : 'fa-moon');
                }
                if (themeToggleButton) {
                    themeToggleButton.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
                }
            }

            applyTheme(storedTheme || (prefersDark ? 'dark' : 'light'));

            if (themeToggleButton) {
                themeToggleButton.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
                    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem(themeStorageKey, nextTheme);
                    applyTheme(nextTheme);
                });
            }

            // Name display preference buttons
            const displayButtons = document.querySelectorAll('.show-two-btn');
            displayButtons.forEach(button => {
                button.addEventListener('click', function() {
                    displayButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Profile form submission
            const profileForm = document.getElementById('profileEditForm');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('.btn-save');
                    const originalText = submitBtn.textContent;

                    submitBtn.textContent = 'Saving...';
                    submitBtn.disabled = true;

                    fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                alert('Profile updated successfully!');

                                // Update preview
                                updateProfilePreview(data.player);

                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById(
                                    'staticBackdrop-two'));
                                if (modal) modal.hide();

                                // Reload page to reflect changes
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                alert('Error updating profile: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating profile. Please try again.');
                        })
                        .finally(() => {
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }

            // Update profile preview function
            function updateProfilePreview(player) {
                const previewName = document.querySelector('.preview-box strong');
                const previewPosition = document.querySelector('.preview-box div:nth-child(2)');
                const previewDetails = document.querySelector('.preview-box small');

                if (previewName) previewName.textContent = player.name;
                if (previewPosition) previewPosition.textContent = player.position || '';
                if (previewDetails) {
                    const details = [player.nationality, player.height, player.weight].filter(Boolean).join(' · ');
                    previewDetails.textContent = details;
                }
            }

            // Profile picture preview
            const profilePictureInput = document.querySelector('input[name="profile_picture"]');
            if (profilePictureInput) {
                profilePictureInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Update preview if exists
                            const previewImg = document.querySelector('.profile-image');
                            if (previewImg) {
                                previewImg.style.backgroundImage = `url(${e.target.result})`;
                                previewImg.style.backgroundSize = 'cover';
                                previewImg.style.backgroundPosition = 'center';
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        // theme.js
        (function() {
            const STORAGE_KEY = "theme"; // "light" | "dark"
            const body = document.body;

            function applyTheme(theme) {
                // Bootstrap expects data-bs-theme
                body.setAttribute("data-bs-theme", theme);

                // If you also use Tailwind's .dark selectors, keep class in sync:
                if (theme === "dark") {
                    body.classList.add("dark");
                } else {
                    body.classList.remove("dark");
                }

                // (Optional) sync ARIA state for any toggle buttons
                document.querySelectorAll(".theme-toggle[aria-pressed]").forEach(btn => {
                    btn.setAttribute("aria-pressed", String(theme === "dark"));
                });

                // persist
                try {
                    localStorage.setItem(STORAGE_KEY, theme);
                } catch {}
            }

            function currentTheme() {
                // 1) stored
                try {
                    const saved = localStorage.getItem(STORAGE_KEY);
                    if (saved === "light" || saved === "dark") return saved;
                } catch {}
                // 2) from DOM
                const domTheme = body.getAttribute("data-bs-theme");
                if (domTheme === "light" || domTheme === "dark") return domTheme;
                // 3) system
                return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            // init on load
            applyTheme(currentTheme());

            // click handler (event delegation works even if buttons are added later)
            document.addEventListener("click", (e) => {
                const t = e.target.closest(".theme-toggle");
                if (!t) return;

                const next = (currentTheme() === "dark") ? "light" : "dark";
                applyTheme(next);
            });

            // Optional: react to OS theme changes if user hasn't explicitly chosen
            // (comment out if you always want to respect stored choice)
            // const mq = window.matchMedia("(prefers-color-scheme: dark)");
            // mq.addEventListener("change", () => {
            //   const saved = localStorage.getItem(STORAGE_KEY);
            //   if (!saved) applyTheme(mq.matches ? "dark" : "light");
            // });
        })();
    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>@yield('title', 'Player Dashboard')</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('storage/theme/logo.png') }}">

    <!-- New Dashboard Styles -->
    <link rel='stylesheet' type='text/css' media='screen' href='{{ asset('assets/player-dashboard/css/main.css') }}'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Additional styles for functionality preservation -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

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

        .logout-account a {
            color: var(--error);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            padding: 10px 0;
        }

        .logout-account a img {
            margin-right: 10px;
        }

        .logout-account a:hover {
            color: var(--error);
        }

        /* Override for content areas that need the new design */
        .content {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        /* Hide old navbar for new design */
        .navbar {
            display: none !important;
        }
    </style>

    @yield('header_styles')
</head>

<body>
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
                                <img src="{{ asset('storage/players/' . $player->photo) }}" alt="Profile">
                            @else
                                <img src="{{ asset('assets/player-dashboard/images/profile.png') }}" alt="Profile">
                            @endif
                            <h2>{{ ucfirst(auth()->user()->name) }}</h2>
                            <p>{{ isset($player) && $player->position ? $player->position->position_name : 'Player' }}
                            </p>
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
                                <input type="hidden" name="_token" value="zLk9oNTWK6IIoLaAFibYNZn43Yj95c57IhQQbgkC"
                                    autocomplete="off"> <button type="submit" class="logout-button">
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
                            <button class="icon-btn ab" type="button" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop-two">
                                <img src="{{ asset('assets/player-dashboard/images/profile1.svg') }}" alt="Icon">
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

    <!-- Profile Edit Modal -->
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
                                        <div>
                                            {{ isset($player) && $player->position ? $player->position->position_name : '' }}
                                        </div>
                                        <small style="display: flex; flex-wrap: wrap; gap: 8px;">
                                            <span><strong>Nationality:</strong>
                                                {{ isset($player) ? $player->nationality ?? 'N/A' : 'N/A' }}</span>
                                            <span><strong>Height:</strong>
                                                {{ isset($player) ? $player->height ?? 'N/A' : 'N/A' }}</span>
                                            <span><strong>Weight:</strong>
                                                {{ isset($player) ? $player->weight ?? 'N/A' : 'N/A' }}</span>
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
                                                <label class="form-label">Jersey Number</label>
                                                <input type="number" class="form-control input-btn" name="jersey_no"
                                                    value="{{ isset($player) ? $player->jersey_no : '' }}">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Profile Picture</label>
                                                <input type="file" class="form-control input-btn"
                                                    name="profile_picture" accept="image/*">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/player-dashboard/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>

    <!-- Additional scripts for functionality preservation -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <!-- Legacy scripts for backward compatibility -->
    <script src="{{ asset('assets/js/bundles/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('assets/js/bundles/jquery.sparkline.min.js') }}"></script>

    @stack('scripts')
    @yield('footer_scripts')

    <!-- Profile Edit Modal JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                                alert('Profile updated successfully!');

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
        });
    </script>
</body>

</html>

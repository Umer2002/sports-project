@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">College / University Dashboard</h2>
    <p class="text-muted">Welcome, {{ $user->name }}. Manage coaches and navigate sport dashboards.</p>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Assign Coaches</span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCoachModal">Add Coach</button>
                </div>
                <div class="card-body">
                    <p class="mb-2">Create coach accounts and map them to your managed clubs. Coaches will have their own logins.</p>
                    <div class="mt-3">
                        <h6 class="mb-2">Recent Coaches</h6>
                        <ul class="list-group">
                            @forelse($coaches as $coach)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $coach->name }} <span class="text-muted">{{ $coach->email }}</span>
                                </li>
                            @empty
                                <li class="list-group-item">No coaches yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Create Managed Club</span>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createClubModal" type="button">Add Club</button>
                </div>
                <div class="card-body">
                    <p class="mb-2">Managed Clubs (hidden):</p>
                    <ul class="list-group mb-3">
                        @forelse($managedClubs as $club)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $club->name }}</strong>
                                    <small class="text-muted ms-2">#{{ $club->id }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-secondary disabled" title="Open club dashboard as college (coming soon)">Open</a>
                            </li>
                        @empty
                            <li class="list-group-item">No managed clubs yet. Create one using the button above.</li>
                        @endforelse
                    </ul>
                    <hr>
                    <p class="mb-2">Quick links to preview sport dashboards:</p>
                    <div class="row g-2">
                        @foreach($sports as $sport)
                            @php $slug = Str::of($sport->name)->lower()->replace(' ', '-'); @endphp
                            <div class="col-md-4">
                                <a class="btn btn-outline-secondary w-100"
                                   href="{{ asset('assets/player-dashboard/' . $slug . '-dashboard.html') }}" target="_blank">
                                    {{ $sport->name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Create Club Modal -->
    <div class="modal fade" id="createClubModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Managed Club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('college.clubs.create') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sport</label>
                                <select class="form-select" name="sport_id" required>
                                    <option value="">Select sport</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Club Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Defaults to College - Sport">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PayPal Link</label>
                                <input type="url" name="paypal_link" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Joining URL</label>
                                <input type="url" name="joining_url" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Club</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Coach Modal -->
    <div class="modal fade" id="addCoachModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Coach</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('college.coaches.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Temporary Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Optional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Age</label>
                                <input type="number" name="age" min="18" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country_id" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sport</label>
                                <select name="sport_id" class="form-select" required>
                                    @foreach($sports as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Assign to Managed Club</label>
                                <select name="club_id" class="form-select" required>
                                    <option value="">Select a club</option>
                                    @foreach($managedClubs as $club)
                                        <option value="{{ $club->id }}">{{ $club->name }} (#{{ $club->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Social Links</label>
                                <input type="url" name="social_links[]" class="form-control mb-2" placeholder="https://">
                                <input type="url" name="social_links[]" class="form-control" placeholder="https://">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Coach</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

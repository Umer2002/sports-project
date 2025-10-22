<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join {{ $club->name }} - Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .registration-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .club-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            border: 4px solid #667eea;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: transform 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .club-info {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="registration-card">
        <div class="club-info">
            @if($club->logo)
                <img src="{{ asset('uploads/clubs/' . $club->logo) }}" alt="{{ $club->name }} Logo" class="club-logo">
            @elseif($club->sport && $club->sport->icon_path)
                <img src="{{ asset('uploads/sports/' . $club->sport->icon_path) }}" alt="{{ $club->sport->name }} Icon" class="club-logo">
            @else
                <div class="club-logo d-flex align-items-center justify-content-center" style="background: #667eea; color: white; font-size: 2rem;">
                    <i class="fas fa-trophy"></i>
                </div>
            @endif
            
            <h2 class="mb-3">{{ $club->name }}</h2>
            <p class="text-muted mb-2">
                <i class="fas fa-map-marker-alt me-2"></i>
                {{ $club->city ?? 'Unknown' }}, {{ $club->country ?? 'Unknown' }}
            </p>
            @if($club->sport)
                <p class="text-muted mb-3">
                    <i class="fas fa-futbol me-2"></i>
                    {{ $club->sport->name }}
                </p>
            @endif
            @if($club->description)
                <p class="mb-3">{{ $club->description }}</p>
            @endif
        </div>

        <h4 class="mb-4">Join {{ $club->name }}</h4>
        
        @if($user)
            @if($user->hasRole('player'))
                @if(isset($user->player) && $user->player->has_paid)
                    <p class="text-success mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        You have already paid and can join this club!
                    </p>
                    <a href="{{ route('register.player', ['club' => $club->id]) }}" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i>
                        Join Club
                    </a>
                @else
                    <p class="text-muted mb-4">Ready to become part of our team? You'll need to complete payment to join.</p>
                    <a href="{{ route('register.player', ['club' => $club->id]) }}" class="btn-register">
                        <i class="fas fa-credit-card me-2"></i>
                        Register & Pay
                    </a>
                @endif
            @else
                <p class="text-muted mb-4">Ready to become part of our team? Click below to start your registration process.</p>
                <a href="{{ route('register.player', ['club' => $club->id]) }}" class="btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Register Now
                </a>
            @endif
        @else
            <p class="text-muted mb-4">Ready to become part of our team? Please log in or register to join this club.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('login') }}?redirect=club-register/{{ $club->id }}" class="btn-register" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </a>
                <a href="{{ route('register.player', ['club' => $club->id]) }}" class="btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Register
                </a>
            </div>
        @endif
        
        <div class="mt-4">
            <a href="{{ route('public.club.profile', $club->slug) }}" class="text-muted">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Club Profile
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

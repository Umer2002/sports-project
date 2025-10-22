@php
    $currentUser = auth()->user();
    $playerModel = $player ?? ($currentUser?->player);
@endphp

<aside id="leftsidebar" class="sidebar">
    <div class="menu">
        <ul class="list">
            <li class="sidebar-user-panel active">
                <div class="user-panel">
                    <div class="image">
                        @if (!empty($playerModel?->photo))
                            <img src="{{ asset('storage/players/' . $playerModel->photo) }}" class="user-img-style" alt="User Image" />
                        @else
                            <img src="{{ asset('assets/images/usrbig.jpg') }}" class="user-img-style" alt="User Image" />
                        @endif
                    </div>
                </div>
                <div class="profile-usertitle">
                    <div class="sidebar-userpic-name"> {{ $currentUser?->name }} </div>
                    <div class="profile-usertitle-job">{{ $playerModel?->position->position_name ?? 'Player' }}</div>
                </div>
            </li>
            @include('players.partials.sidebar-menu-items', ['player' => $playerModel])
        </ul>
    </div>
</aside>

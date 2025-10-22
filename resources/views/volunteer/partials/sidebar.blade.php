@php($user = auth()->user())
<aside id="leftsidebar" class="sidebar">
  <div class="menu">
    <ul class="list">
      <li class="sidebar-user-panel">
        <div class="user-panel">
          <div class="image">
            <img src="{{ asset('assets/images/usrbig.jpg') }}" class="user-img-style" alt="User Image" />
          </div>
        </div>
        <div class="profile-usertitle">
          <div class="sidebar-userpic-name"> {{ $user?->name }} </div>
          <div class="profile-usertitle-job">Ambassador</div>
        </div>
      </li>

      <li class="{{ request()->routeIs('volunteer.dashboard') ? 'active' : '' }}">
        <a href="{{ route('volunteer.dashboard') }}">
          <i data-feather="home"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('volunteer.clubs.*') ? 'active' : '' }}">
        <a href="{{ route('volunteer.clubs.index') }}">
          <i data-feather="users"></i>
          <span>Clubs</span>
        </a>
      </li>

      <li class="{{ request()->routeIs('volunteer.promotions.*') ? 'active' : '' }}">
        <a href="{{ route('volunteer.promotions.index') }}">
          <i data-feather="speaker"></i>
          <span>Promotions</span>
        </a>
      </li>
    </ul>
  </div>
</aside>
<div class="p-3">
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline-danger w-100">
      <i class="zmdi zmdi-power"></i> Logout
    </button>
  </form>
</div>

<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="#" class="navbar-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="#" class="bars"></a>
            <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" style="width: 50px;
                height: 50px;"/>
                <span class="logo-name">P2E</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="pull-left">
                <li>
                    <a href="#" class="sidemenu-collapse">
                        <i class="material-icons">reorder</i>
                    </a>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="fullscreen">
                    <a href="javascript:;" class="fullscreen-btn">
                        <i class="fas fa-expand"></i>
                    </a>
                </li>
                <li class="dropdown user_profile">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" role="button">
                        <img src="{{ asset('assets/images/user.jpg') }}" width="32" height="32" alt="User">
                    </a>
                    <ul class="dropdown-menu pullDown">
                        <li class="body">
                            <ul class="user_dw_menu">
                                <li>
                                    <a href="/my/account"><i class="material-icons">person</i>Profile</a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="material-icons">power_settings_new</i>Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                {{-- <li class="pull-right">
                    <a href="#" class="js-right-sidebar" data-close="true">
                        <i class="fas fa-cog"></i>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
</nav>

<aside id="leftsidebar" class="sidebar">
    <!-- Menu -->
    <div class="menu">
        <ul class="list">
            <li class="sidebar-user-panel">
                <div class="user-panel">
                    <div class="image">
                        <img src="{{ asset('assets/images/usrbig.jpg') }}" class="user-img-style" alt="User Image" />
                    </div>
                </div>
                <div class="profile-usertitle">
                    <div class="sidebar-userpic-name"> Emily Smith </div>
                    <div class="profile-usertitle-job">Manager</div>
                </div>
            </li>

            <!-- Home -->
            <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i data-feather="monitor"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Communication & Engagements -->
            @php
                $communicationRoutes = [
                    'admin.blog',
                    'admin.blogcategory*',
                    'admin.email.',
                    'admin.news',
                    'admin.tasks',
                    'admin.events',
                ];
            @endphp
            <li class="{{ request()->routeIs($communicationRoutes) ? 'active' : '' }}">
                <a href="#" onClick="return false;" class="menu-toggle">
                    <i data-feather="calendar"></i>
                    <span>Communication & Engagements</span>
                </a>
                <ul class="ml-menu">
                    <li class="{{ request()->routeIs(['admin.blog*', 'admin.blogcategory*']) ? 'active' : '' }}">
                        <a href="#" onClick="return false;" class="menu-toggle">
                            Blog's Category</a>
                        <ul class="ml-menu">
                            <li class="{{ request()->routeIs('admin.blogcategory.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.blogcategory.index') }}">Blog's Category</a>
                            </li>
                            <li class="{{ request()->routeIs(['admin.blog.index', 'admin.blog.create', 'admin.blog.edit', 'admin.blog.show']) ? 'active' : '' }}">
                                <a href="{{ route('admin.blog.index') }}">Blogs</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle" onClick="return false;">
                            News
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ request()->routeIs(['admin.news.index', 'admin.news.show', 'admin.news.edit']) ? 'active' : '' }}">
                                <a href="{{ route('admin.news.index') }}">News List</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.news.create') ? 'active' : '' }}">
                                <a href="{{ route('admin.news.create') }}">Create News</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle" onClick="return false;">
                            Tasks
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ request()->routeIs(['admin.tasks.index', 'admin.tasks.show', 'admin.tasks.edit']) ? 'active' : '' }}">
                                <a href="{{ route('admin.tasks.index') }}">Tasks List</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.tasks.create') ? 'active' : '' }}">
                                <a href="{{ route('admin.tasks.create') }}">Add Task</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle" onClick="return false;">
                            Events
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ request()->routeIs(['admin.events.index', 'admin.events.show', 'admin.events.edit']) ? 'active' : '' }}">
                                <a href="{{ route('admin.events.index') }}">Events List</a>
                            </li>
                            <li class="{{ request()->routeIs('admin.events.create') ? 'active' : '' }}">
                                <a href="{{ route('admin.events.create') }}">Create Event</a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ Request::is('admin/email*') ? 'active' : '' }}">
                        <a href="#" onClick="return false;" class="menu-toggle">
                            Email
                        </a>
                        <ul class="ml-menu">
                            <li class="{{ Request::routeIs('admin.email.inbox') ? 'active' : '' }}">
                                <a href="{{ route('admin.email.inbox') }}">Inbox</a>
                            </li>
                            <li class="{{ Request::routeIs('admin.email.compose') ? 'active' : '' }}">
                                <a href="{{ route('admin.email.compose') }}">Compose</a>
                            </li>
                            <li class="{{ Request::routeIs('admin.email.sent') ? 'active' : '' }}">
                                <a href="{{ route('admin.email.sent') }}">Sent</a>
                            </li>
                            <li class="{{ Request::routeIs('admin.email.drafts') ? 'active' : '' }}">
                                <a href="{{ route('admin.email.drafts') }}">Drafts</a>
                            </li>
                            <li class="{{ Request::routeIs('admin.email.trash') ? 'active' : '' }}">
                                <a href="{{ route('admin.email.trash') }}">Trash</a>
                            </li>
                        </ul>
                    </li>
                    {{-- add front.logout linke there --}}

                </ul>
            </li>

            <!-- Sports Management -->
            @php
                $sportsManagementPatterns = [
                    'admin/ads*',
                    'admin/sports*',
                    'admin/age-groups*',
                    'admin/genders*',
                    'admin/sport-classification-groups*',
                    'admin/sport-classification-options*',
                    'admin/rewards*',
                    'admin/injury-reports*',
                    'admin/clubs*',
                    'bulk-clubs*',
                    'admin/players*',
                    'admin/teams*',
                    'admin/coaches*',
                    'admin/positions*',
                    'admin/stats*',
                    'admin/tournamentformats*',
                    'admin/tournaments*',
                    'admin/referees*',
                    'admin/game-expertise*',
                    'admin/venues*',
                    'admin/volunteers*',
                    'admin/payments*',
                    'admin/donations*',
                    'admin/payout_plans*',
                ];

                $sportsManagementActive = collect($sportsManagementPatterns)->contains(
                    fn($pattern) => Request::is($pattern),
                );
            @endphp
            <li class="{{ $sportsManagementActive ? 'active' : '' }}">
                <a href="#" onClick="return false;" class="menu-toggle">
                    <i data-feather="command"></i>
                    <span>Sports Management</span>
                </a>
                <ul class="ml-menu">
                    <li class="{{ Request::is('admin/ads*') ? 'active' : '' }}">
                        <a href="{{ route('admin.ads.index') }}">Ads</a>
                    </li>
                    <li class="{{ Request::is('admin/sports*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sports.index') }}">Sports</a>
                    </li>
                    <li class="{{ Request::is('admin/age-groups*') ? 'active' : '' }}">
                        <a href="{{ route('admin.age_groups.index') }}">Age Groups</a>
                    </li>
                    <li class="{{ Request::is('admin/genders*') ? 'active' : '' }}">
                        <a href="{{ route('admin.genders.index') }}">Genders</a>
                    </li>
                    <li class="{{ Request::is('admin/sport-classification-groups*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sport_classification_groups.index') }}">Classification Groups</a>
                    </li>
                    <li class="{{ Request::is('admin/sport-classification-options*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sport_classification_options.index') }}">Classification Options</a>
                    </li>
                    <li class="{{ Request::is('admin/rewards*') ? 'active' : '' }}">
                        <a href="{{ route('admin.rewards.index') }}">Rewards</a>
                    </li>
                    <li class="{{ Request::is('admin/injury-reports*') ? 'active' : '' }}">
                        <a href="{{ route('admin.injury_reports.index') }}">Injury Reports</a>
                    </li>
                    <li class="{{ Request::is('admin/clubs*') ? 'active' : '' }}">
                        <a href="{{ route('admin.clubs.index') }}">Clubs</a>
                    </li>
                    <li class="{{ Request::is('bulk-clubs*') ? 'active' : '' }}">
                        <a href="{{ route('bulk-clubs.import.form') }}">Bulk Import Clubs</a>
                    </li>
                    <li class="{{ Request::is('admin/players*') ? 'active' : '' }}">
                        <a href="{{ route('admin.players.index') }}">Players</a>
                    </li>
                    <li class="{{ Request::is('admin/teams*') ? 'active' : '' }}">
                        <a href="{{ route('admin.teams.index') }}">Teams</a>
                    </li>
                    <li class="{{ Request::is('admin/coaches*') ? 'active' : '' }}">
                        <a href="{{ route('admin.coaches.index') }}">Coaches</a>
                    </li>
                    <li class="{{ Request::is('admin/positions*') ? 'active' : '' }}">
                        <a href="{{ route('admin.positions.index') }}">Positions</a>
                    </li>
                    <li class="{{ Request::is('admin/stats*') ? 'active' : '' }}">
                        <a href="{{ route('admin.stats.index') }}">Stats</a>
                    </li>
                    <li class="{{ Request::is('admin/player-stats*') ? 'active' : '' }}">
                        <a href="{{ route('admin.player-stats.index') }}">Player Stats</a>
                    </li>
                    <li class="{{ Request::is('admin/tournaments*') ? 'active' : '' }}">
                        <a href="{{ route('admin.tournaments.index') }}">Tournaments</a>
                    </li>
                    <li class="{{ Request::is('admin/referees*') ? 'active' : '' }}">
                        <a href="{{ route('admin.referees.index') }}">Referees</a>
                    </li>
                    <li class="{{ Request::is('admin/game-expertise*') ? 'active' : '' }}">
                        <a href="{{ route('admin.game-expertise.index') }}">
                            <i class="fas fa-star"></i> Game Expertise
                        </a>
                    </li>
                    <li class="{{ Request::is('admin/venues*') ? 'active' : '' }}">
                        <a href="{{ route('admin.venues.index') }}">Venues</a>
                    </li>
                    <li class="{{ Request::is('admin/volunteers*') ? 'active' : '' }}">
                        <a href="{{ route('admin.volunteers.index') }}">Volunteers</a>
                    </li>
                    <li class="{{ Request::is('admin/payments*') ? 'active' : '' }}">
                        <a href="{{ route('admin.payments.index') }}">Payments</a>
                    </li>
                    <li class="{{ Request::is('admin/donations*') ? 'active' : '' }}">
                        <a href="{{ route('admin.donations.index') }}">Donations</a>
                    </li>
                    <li class="{{ Request::is('admin/payout_plans*') ? 'active' : '' }}">
                        <a href="{{ route('admin.payout_plans.index') }}">Payout Plans</a>
                    </li>
                </ul>
            </li>

            <!-- E-commerce -->
            <li class="{{ Request::is('admin/productcategory*') || Request::is('admin/products*') ? 'active' : '' }}">
                <a href="#" onClick="return false;" class="menu-toggle">
                    <i data-feather="shopping-cart"></i>
                    <span>E-commerce</span>
                </a>
                <ul class="ml-menu">
                    <li class="{{ Request::is('admin/productcategory*') ? 'active' : '' }}">
                        <a href="{{ route('admin.productcategory.index') }}">Product's Category</a>
                    </li>
                    <li class="{{ Request::is('admin/products*') ? 'active' : '' }}">
                        <a href="{{ route('admin.products.index') }}">Product</a>
                    </li>
                    <li class="{{ Request::is('admin/orders*') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}">Orders</a>
                    </li>
                </ul>
            </li>

            <!-- User Management -->
            <li class="{{ Request::is('admin/users*') || Request::is('admin/roles*') ? 'active' : '' }}">
                <a href="#" onClick="return false;" class="menu-toggle">
                    <i data-feather="copy"></i>
                    <span>User Management</span>
                </a>
                <ul class="ml-menu">
                    <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}">Users</a>
                    </li>
                    <li class="{{ Request::is('admin/roles*') ? 'active' : '' }}">
                        <a href="{{ route('admin.roles.index') }}">Roles</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('front.logout') }}">Logout </a>
            </li>
        </ul>
    </div>
    <!-- #Menu -->
</aside>

<!-- Right Sidebar -->
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs tab-nav-right" role="tablist">
        <li role="presentation">
            <a href="#skins" data-bs-toggle="tab" class="active">SKINS</a>
        </li>
        <li role="presentation">
            <a href="#settings" data-bs-toggle="tab">SETTINGS</a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane in active in active stretchLeft" id="skins">
            {{-- Theme selection content intentionally left for future use --}}
        </div>
        <div role="tabpanel" class="tab-pane stretchRight" id="settings">
            <div class="demo-settings">
                <p>GENERAL SETTINGS</p>
                <ul class="setting-list">
                    <li>
                        <span>Report Panel Usage</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox" checked>
                                <span class="lever switch-col-green"></span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <span>Email Redirect</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox">
                                <span class="lever switch-col-blue"></span>
                            </label>
                        </div>
                    </li>
                </ul>
                <p>SYSTEM SETTINGS</p>
                <ul class="setting-list">
                    <li>
                        <span>Notifications</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox" checked>
                                <span class="lever switch-col-purple"></span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <span>Auto Updates</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox" checked>
                                <span class="lever switch-col-cyan"></span>
                            </label>
                        </div>
                    </li>
                </ul>
                <p>ACCOUNT SETTINGS</p>
                <ul class="setting-list">
                    <li>
                        <span>Offline</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox" checked>
                                <span class="lever switch-col-red"></span>
                            </label>
                        </div>
                    </li>
                    <li>
                        <span>Location Permission</span>
                        <div class="switch">
                            <label>
                                <input type="checkbox">
                                <span class="lever switch-col-lime"></span>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>

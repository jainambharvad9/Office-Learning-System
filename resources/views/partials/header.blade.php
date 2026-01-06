<!-- Header Component -->
<header class="main-header">
    <div class="header-content">
        <div class="logo-section">
            <h1 class="brand-logo" style="width: 230px;">
                <i class="fas fa-graduation-cap"></i>
                Office Learning
            </h1>
            <span class="brand-tagline">Empowering Your Career Growth</span>
        </div>

        <nav class="main-nav">
            <ul class="nav-links">
                @if(auth()->check())
                    @if(auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}"
                                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i
                                    class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><a href="{{ route('admin.interns') }}"
                                class="{{ request()->routeIs('admin.interns') ? 'active' : '' }}"><i class="fas fa-users"></i>
                                Interns</a></li>
                        <li><a href="{{ route('admin.upload.form') }}"
                                class="{{ request()->routeIs('admin.upload.form') ? 'active' : '' }}"><i
                                    class="fas fa-video"></i> Upload</a></li>
                        <li><a href="{{ route('admin.videos') }}"
                                class="{{ request()->routeIs('admin.videos') ? 'active' : '' }}"><i class="fas fa-film"></i>
                                Videos</a></li>
                        <li><a href="{{ route('admin.categories') }}"
                                class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}"><i
                                    class="fas fa-tags"></i>
                                Categories</a></li>
                        <li><a href="{{ route('admin.reports') }}"
                                class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i
                                    class="fas fa-chart-bar"></i> Reports</a></li>
                    @else
                        <li><a href="{{ route('intern.dashboard') }}"
                                class="{{ request()->routeIs('intern.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i>
                                Dashboard</a></li>
                        <li><a href="#" onclick="showProfile()"><i class="fas fa-user"></i> Profile</a></li>
                    @endif
                @endif
            </ul>
        </nav>

        <div class="header-actions">
            <!-- Dark Mode Toggle -->
            <button id="theme-toggle" class="theme-toggle" title="Toggle Dark Mode">
                <i class="fas fa-moon"></i>
                <i class="fas fa-sun"></i>
            </button>

            @if(auth()->check())
                <div class="user-menu">
                    <button class="user-menu-btn" onclick="toggleUserMenu()">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="#" onclick="showProfile()"><i class="fas fa-user"></i> Profile</a>
                        <a href="#" onclick="showSettings()"><i class="fas fa-cog"></i> Settings</a>
                        <hr>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            @endif
        </div>
    </div>
</header>
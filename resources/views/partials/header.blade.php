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

        @if(auth()->check() && !auth()->user()->isAdmin())
            <!-- Intern Search Box -->
            <div class="search-section">
                <div class="search-box-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="videoSearch" class="video-search-input" placeholder="Search videos...">
                    <div id="searchResults" class="search-results-dropdown"></div>
                </div>
            </div>
        @endif

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
                        <li><a href="{{ route('admin.quizzes.index') }}"
                                class="{{ request()->routeIs('admin.quizzes*') ? 'active' : '' }}"><i class="fas fa-brain"></i>
                                Quizzes</a></li>
                        <li><a href="{{ route('admin.reports') }}"
                                class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}"><i
                                    class="fas fa-chart-bar"></i> Reports</a></li>
                    @else
                        <li><a href="{{ route('intern.dashboard') }}"
                                class="{{ request()->routeIs('intern.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i>
                                Dashboard</a></li>
                        <li><a href="{{ route('intern.videos.all') }}"
                                class="{{ request()->routeIs('intern.videos.all') ? 'active' : '' }}"><i
                                    class="fas fa-film"></i>
                                Videos</a></li>
                        <li><a href="{{ route('intern.quizzes.index') }}"
                                class="{{ request()->routeIs('intern.quizzes*') ? 'active' : '' }}"><i class="fas fa-brain"></i>
                                Quizzes</a></li>
                    @endif
                @endif
            </ul>
        </nav>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="header-actions" >
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

<script>
    // Mobile Menu Toggle
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNav = document.querySelector('.main-nav');

        if (mobileMenuToggle && mainNav) {
            mobileMenuToggle.addEventListener('click', function () {
                mainNav.classList.toggle('mobile-menu-open');
                mobileMenuToggle.classList.toggle('active');
            });

            // Close menu when a link is clicked
            const navLinks = mainNav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    mainNav.classList.remove('mobile-menu-open');
                    mobileMenuToggle.classList.remove('active');
                });
            });
        }

        // Live Video Search
        const videoSearch = document.getElementById('videoSearch');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        if (videoSearch) {
            videoSearch.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('intern.search.videos') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                searchResults.innerHTML = '<div class="search-no-results"><i class="fas fa-search"></i> No videos found</div>';
                                return;
                            }

                            let html = '';
                            data.forEach(video => {
                                html += `
                                    <a href="${video.url}" class="search-result-item">
                                        <div class="search-result-content">
                                            <div class="search-result-title">${escapeHtml(video.title)}</div>
                                            <div class="search-result-category">${escapeHtml(video.category)}</div>
                                        </div>
                                    </a>
                                `;
                            });

                            searchResults.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            searchResults.innerHTML = '<div class="search-error">Error searching videos</div>';
                        });
                }, 300);
            });

            // Close search results when clicking outside
            document.addEventListener('click', function (e) {
                if (!videoSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.innerHTML = '';
                }
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
    });

    // User Menu Toggle
    function toggleUserMenu() {
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            userDropdown.classList.toggle('show');
        }
    }

    // Profile placeholder
    function showProfile() {
        alert('Profile page coming soon!');
    }

    // Settings placeholder
    function showSettings() {
        alert('Settings page coming soon!');
    }
</script>

<style>
    .search-box-wrapper {
        position: relative;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.6);
        pointer-events: none;
    }

    .video-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 2.5rem;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: white;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .video-search-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .video-search-input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
    }

    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .search-result-item {
        display: block;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        text-decoration: none;
        color: var(--text-primary);
        transition: background 0.2s ease;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover {
        background: var(--surface);
    }

    .search-result-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .search-result-title {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.9rem;
    }

    .search-result-category {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .search-no-results,
    .search-error {
        padding: 1rem;
        text-align: center;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .search-box-wrapper {
            max-width: 200px;
        }

        .video-search-input {
            font-size: 0.85rem;
            padding: 0.4rem 0.5rem 0.4rem 2rem;
        }

        .search-results-dropdown {
            max-height: 300px;
        }
    }
</style>
{{--
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile Menu Toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNav = document.querySelector('.main-nav');

        if (mobileMenuToggle && mainNav) {
            mobileMenuToggle.addEventListener('click', function () {
                mainNav.classList.toggle('mobile-menu-open');
                mobileMenuToggle.classList.toggle('active');
            });

            // Close menu when a link is clicked
            const navLinks = mainNav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    mainNav.classList.remove('mobile-menu-open');
                    mobileMenuToggle.classList.remove('active');
                });
            });
        }

        // Live Video Search
        const videoSearch = document.getElementById('videoSearch');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        if (videoSearch && searchResults) {
            videoSearch.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                // Clear results if query is too short
                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    searchResults.style.display = 'none';
                    return;
                }

                // Show loading state
                searchResults.style.display = 'block';
                searchResults.innerHTML = '<div class="search-loading"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

                // Debounce search
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Close search results when clicking outside
            document.addEventListener('click', function (e) {
                if (!videoSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.innerHTML = '';
                    searchResults.style.display = 'none';
                }
            });

            // Prevent closing when clicking inside search results
            searchResults.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Perform the actual search
        async function performSearch(query) {
            try {
                const response = await fetch(`/intern/search-videos?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });

                // Check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response');
                }

                const data = await response.json();

                // Handle empty results
                if (!data || data.length === 0) {
                    searchResults.innerHTML = `
                        <div class="search-no-results">
                            <i class="fas fa-search"></i> 
                            <p>No videos found for "${escapeHtml(query)}"</p>
                        </div>
                    `;
                    return;
                }

                // Display results
                displaySearchResults(data);

            } catch (error) {
                console.error('Search error:', error);
                searchResults.innerHTML = `
                    <div class="search-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error searching videos. Please try again.</p>
                    </div>
                `;
            }
        }

        // Display search results
        function displaySearchResults(videos) {
            let html = '<div class="search-results-container">';

            videos.forEach(video => {
                html += `
                    <a href="${video.url}" class="search-result-item">
                        <div class="search-result-content">
                            <div class="search-result-header">
                                <i class="fas fa-play-circle"></i>
                                <div class="search-result-title">${escapeHtml(video.title)}</div>
                            </div>
                            ${video.description ? `<div class="search-result-description">${escapeHtml(video.description)}</div>` : ''}
                            <div class="search-result-footer">
                                <span class="search-result-category">
                                    <i class="fas fa-folder"></i> ${escapeHtml(video.category)}
                                </span>
                            </div>
                        </div>
                    </a>
                `;
            });

            html += '</div>';
            searchResults.innerHTML = html;
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
    });

    // User Menu Toggle
    function toggleUserMenu() {
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            userDropdown.classList.toggle('show');

            // Close dropdown when clicking outside
            if (userDropdown.classList.contains('show')) {
                document.addEventListener('click', closeUserMenuOutside);
            } else {
                document.removeEventListener('click', closeUserMenuOutside);
            }
        }
    }

    // Close user menu when clicking outside
    function closeUserMenuOutside(e) {
        const userDropdown = document.getElementById('userDropdown');
        const userMenuButton = document.querySelector('[onclick="toggleUserMenu()"]');

        if (userDropdown && !userDropdown.contains(e.target) && !userMenuButton?.contains(e.target)) {
            userDropdown.classList.remove('show');
            document.removeEventListener('click', closeUserMenuOutside);
        }
    }

    // Profile placeholder
    function showProfile() {
        alert('Profile page coming soon!');
    }

    // Settings placeholder
    function showSettings() {
        alert('Settings page coming soon!');
    }

    // Clear search on page load
    window.addEventListener('load', function () {
        const videoSearch = document.getElementById('videoSearch');
        const searchResults = document.getElementById('searchResults');

        if (videoSearch) {
            videoSearch.value = '';
        }

        if (searchResults) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
        }
    });
</script>

<style>
    /* Search Results Styling */
    #searchResults {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        margin-top: 8px;
        display: none;
    }

    .search-results-container {
        padding: 8px;
    }

    .search-result-item {
        display: block;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 4px;
        text-decoration: none;
        color: inherit;
        transition: background-color 0.2s;
    }

    .search-result-item:hover {
        background-color: #f3f4f6;
    }

    .search-result-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }

    .search-result-header i {
        color: #3b82f6;
        font-size: 16px;
    }

    .search-result-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }

    .search-result-description {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .search-result-footer {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-result-category {
        font-size: 11px;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .search-result-category i {
        font-size: 10px;
    }

    .search-loading,
    .search-no-results,
    .search-error {
        padding: 20px;
        text-align: center;
        color: #6b7280;
    }

    .search-loading i,
    .search-no-results i,
    .search-error i {
        font-size: 24px;
        margin-bottom: 8px;
        display: block;
    }

    .search-error {
        color: #ef4444;
    }

    .search-error i {
        color: #ef4444;
    }

    /* Scrollbar styling */
    #searchResults::-webkit-scrollbar {
        width: 6px;
    }

    #searchResults::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #searchResults::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    #searchResults::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style> --}}